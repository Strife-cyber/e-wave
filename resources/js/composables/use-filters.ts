import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce'; // You might need to install lodash: npm install lodash
import { ref, watch } from 'vue';

/**
 * Composable for managing list filtering, searching, sorting, and pagination with Inertia.js.
 * Assumes standard query parameter names unless specific prefixes are needed.
 *
 * @param {string} resourceKey - A key representing the resource being filtered (e.g., 'users', 'products'). Used for partial reloads.
 * @param {object} initialFilters - The initial default filter values.
 * @param {object} options - Optional configuration.
 * @param {string} [options.filterPrefix] - Optional prefix for non-standard filter parameters (e.g., 'filter' for filter[status]=active).
 * @param {string} [options.searchKey='search'] - The query parameter key for the search input.
 * @param {string} [options.sortKey='sort'] - The query parameter key for the sort column.
 * @param {string} [options.directionKey='direction'] - The query parameter key for the sort direction.
 * @param {string} [options.pageKey='page'] - The query parameter key for pagination.
 * @param {number} [options.debounce=300] - Debounce delay in ms for search input.
 */
export function useFilters(resourceKey, initialFilters = {}, options = {}) {
    const {
        filterPrefix, // e.g., 'filter' -> filter[status]=active
        searchKey = 'search',
        sortKey = 'sort',
        directionKey = 'direction',
        pageKey = 'page',
        debounce: debounceDelay = 300, // default debounce delay
    } = options;

    const filtersVisible = ref(false);
    const filters = ref({ ...initialFilters });
    const search = ref(''); // Separate state for search input
    const sort = ref(''); // Separate state for sort column
    const direction = ref(''); // Separate state for sort direction

    // Read initial state from URL on setup
    const initializeFilters = () => {
        const params = new URLSearchParams(window.location.search);

        // Read standard search, sort, direction, and page
        if (params.has(searchKey)) search.value = params.get(searchKey);
        if (params.has(sortKey)) sort.value = params.get(sortKey);
        if (params.has(directionKey)) direction.value = params.get(directionKey);
        // No need to read 'page' into separate state, Inertia handles it automatically on visit

        // Read custom filters based on initialFilters keys and optional prefix
        Object.keys(filters.value).forEach((key) => {
            let paramName = key;
            if (filterPrefix) {
                // Handle potential array notation like filter[status][]
                // Simple key=value or filter[key]=value
                paramName = filterPrefix + '[' + key + ']';
                // For array filters like filter[status][], you'd need more complex logic here or pass arrays directly
                // Laravel automatically handles filter[statuses][]=active&filter[statuses][]=pending
            }

            // Check for both key=value and prefixed[key]=value
            if (params.has(key)) {
                filters.value[key] = params.get(key);
            } else if (params.has(paramName)) {
                filters.value[key] = params.get(paramName);
            } else if (params.has(paramName + '[]')) {
                // Handle array filters like filter[statuses][]
                // This requires parsing potentially multiple values
                const allParams = Array.from(params.entries());
                const arrayValues = allParams.filter(([k, v]) => k === paramName + '[]').map(([k, v]) => v);
                if (arrayValues.length > 0) {
                    filters.value[key] = arrayValues;
                }
            }
        });
    };

    // Apply filters function (can be debounced)
    const performSearchAndFilters = () => {
        const params = {};

        // Add search parameter if not empty
        if (search.value) {
            params[searchKey] = search.value;
        }

        // Add sort parameters if set
        if (sort.value) {
            params[sortKey] = sort.value;
            // Include direction only if sort column is set
            if (direction.value) {
                params[directionKey] = direction.value;
            }
        }

        // Add custom filters
        Object.keys(filters.value).forEach((key) => {
            const value = filters.value[key];
            if (value !== null && value !== '' && (Array.isArray(value) ? value.length > 0 : true)) {
                let paramName = key;
                if (filterPrefix) {
                    paramName = filterPrefix + '[' + key + ']';
                }

                if (Array.isArray(value)) {
                    // Handle array filters: filter[statuses][]=active&filter[statuses][]=pending
                    value.forEach((item) => {
                        // Append [] to the key for array notation in URL
                        params[paramName + '[]'] = item;
                    });
                } else {
                    // Handle single value filters: status=active or filter[status]=active
                    params[paramName] = value;
                }
            }
        });

        // Always reset to page 1 when filters/search/sort change
        params[pageKey] = 1;

        console.log('Applying filters with params:', params); // Debug log

        router.get('/contributions', params, {
            preserveState: true,
            preserveScroll: true,
            // Use the resourceKey for partial reloads
            only: [resourceKey, 'auth'],
        });
    };

    // Debounced version for search input
    const debouncedSearch = debounce(performSearchAndFilters, debounceDelay);

    // Watch specific filter properties to trigger applyFilters
    watch(filters, performSearchAndFilters, { deep: true }); // Watch nested changes in filters object
    watch(sort, performSearchAndFilters);
    watch(direction, performSearchAndFilters);

    // Watch search input separately to use debounced version
    watch(search, debouncedSearch);

    const resetFilters = () => {
        // Reset state variables
        filters.value = { ...initialFilters };
        search.value = '';
        sort.value = '';
        direction.value = '';

        // Perform a visit with only the page parameter to clear others
        // Inertia automatically handles removing query parameters that are not present
        router.get(
            '/contributions',
            // Optionally keep the page parameter here, or remove it to reset to page 1
            // Sending { [pageKey]: 1 } ensures you go back to the first page and clears other params
            { [pageKey]: 1 },
            {
                preserveState: false, // Reset state when clearing filters
                preserveScroll: false, // Go to top when resetting
                only: [resourceKey, 'auth'],
            },
        );
        console.log('Filters reset'); // Debug log
    };

    // Function to handle sorting column clicks
    const sortBy = (column) => {
        if (sort.value === column) {
            // If already sorting by this column, toggle direction
            direction.value = direction.value === 'asc' ? 'desc' : 'asc';
        } else {
            // If sorting by a new column, set it and default to asc
            sort.value = column;
            direction.value = 'asc';
        }
        // Watching sort/direction will trigger performSearchAndFilters
    };

    // Manually trigger initial load
    initializeFilters();

    return {
        // State
        filtersVisible,
        filters,
        search,
        sort,
        direction,

        // Methods
        applyFilters: performSearchAndFilters, // Provide the non-debounced version too if needed
        resetFilters,
        sortBy, // Method to change sorting

        // Initializer (called internally, but maybe useful to expose?)
        // initializeFilters, // Usually not needed outside, called on setup
    };
}
