<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div class="flex items-center gap-2">
        <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
            <AppLogoIcon class="size-4 fill-current text-white dark:text-black" />
        </div>
        <div class="ml-1 grid flex-1 text-left text-xl">
            <span class="mb-0.5 truncate font-pacifico font-semibold leading-none">Edu Wave</span>
        </div>
    </div>
</template>
