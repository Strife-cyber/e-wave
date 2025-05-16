// OpenRouter API integration for an Intelligent Echo Chamber

// Replace with your actual API key
const OPENROUTER_API_KEY = import.meta.env.VITE_OPENROUTER_API_KEY || '';

/**
 * Model selection configuration
 * Maps query types and complexity to preferred models
 */
const MODEL_CONFIG = {
    simple: {
        description: 'Short, factual queries (e.g., definitions, basic facts)',
        models: ['openai/gpt-3.5-turbo', 'mistralai/mixtral-8x7b'],
        maxTokens: 100,
    },
    intermediate: {
        description: 'Explanations, summaries, or moderate reasoning',
        models: ['openai/gpt-4o-mini', 'anthropic/claude-3-haiku'],
        maxTokens: 500,
    },
    complex: {
        description: 'Multi-step reasoning, technical, or creative tasks',
        models: ['openai/gpt-4o', 'anthropic/claude-3.5-sonnet'],
        maxTokens: 2000,
    },
};

/**
 * Determines the complexity of a query based on keywords and length
 * @param {string} query - The user's query
 * @returns {string} - Complexity level (simple, intermediate, complex)
 */
function determineQueryComplexity(query) {
    const words = query.split(' ').length;
    const complexKeywords = ['explain', 'analyze', 'compare', 'derive', 'prove', 'multi-step', 'technical'];
    const intermediateKeywords = ['summarize', 'describe', 'how does', 'why is'];

    if (complexKeywords.some((keyword) => query.toLowerCase().includes(keyword)) || words > 50) {
        return 'complex';
    } else if (intermediateKeywords.some((keyword) => query.toLowerCase().includes(keyword)) || words > 20) {
        return 'intermediate';
    }
    return 'simple';
}

/**
 * Selects the best model based on query complexity and subject
 * @param {string} query - The user's query
 * @returns {string} - Selected model
 */
function selectModel(query) {
    const complexity = determineQueryComplexity(query);
    const config = MODEL_CONFIG[complexity];

    // Placeholder for subject-specific model selection (e.g., math prefers certain models)
    // Could be extended with NLP-based subject detection
    const subject = detectSubject(query); // Implement subject detection logic if needed
    console.log(subject);

    // Choose the first available model (could be enhanced with performance metrics)
    return config.models[0];
}

/**
 * Placeholder for subject detection (e.g., math, history, science)
 * @param {string} query - The user's query
 * @returns {string} - Detected subject
 */
function detectSubject(query) {
    // Implement NLP-based subject detection or keyword matching
    // For now, return a default subject
    console.log(query);
    return 'general';
}

/**
 * Sends a message to the OpenRouter API and returns the response
 * @param {Array} messages - Array of message objects with role and content
 * @param {string} userLevel - User's knowledge level (beginner, intermediate, advanced)
 * @returns {Promise<Object>} - The assistant's response with additional study aids
 */
export async function sendMessageToOpenRouter(messages, userLevel = 'intermediate') {
    // If no API key is provided, use mock responses for development
    if (!OPENROUTER_API_KEY) {
        return generateMockResponse(messages[messages.length - 1].content);
    }

    const query = messages[messages.length - 1].content;
    const selectedModel = selectModel(query);
    const complexity = determineQueryComplexity(query);

    try {
        const response = await fetch('https://openrouter.ai/api/v1/chat/completions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${OPENROUTER_API_KEY}`,
                'HTTP-Referer': window.location.origin,
                'X-Title': 'Academic Echo Chamber',
            },
            body: JSON.stringify({
                model: selectedModel,
                messages: [
                    {
                        role: 'system',
                        content: `You are an academic tutor creating an intelligent echo chamber to help users study. Adapt explanations to the user's knowledge level: ${userLevel}. Provide a concise answer, a summary of key points, and a follow-up question or quiz to reinforce learning. If the query is complex, break it down into steps.`,
                    },
                    ...messages,
                ],
                max_tokens: MODEL_CONFIG[complexity].maxTokens,
            }),
        });

        if (!response.ok) {
            const errorData = await response.json();
            console.error('OpenRouter API error:', errorData);
            return new Error('Failed to get response from OpenRouter');
        }

        const data = await response.json();
        const assistantResponse = data.choices[0].message.content;

        // Enhance response with study aids
        return {
            response: assistantResponse,
            modelUsed: selectedModel,
            complexity: complexity,
            // Placeholder for tracking user progress (e.g., store in localStorage or database)
            progress: updateUserProgress(query, assistantResponse),
        };
    } catch (error) {
        console.error('Error calling OpenRouter API:', error);
        throw error;
    }
}

/**
 * Placeholder for updating user progress
 * @param {string} query - The user's query
 * @param {string} response - The assistant's response
 * @returns {Object} - Updated progress
 */
function updateUserProgress(query, response) {
    // Implement progress tracking (e.g., topics covered, quiz results)
    return { topics: [query], lastResponse: response };
}

/**
 * Generates mock responses for development without an API key
 * @param {string} question - The user's question
 * @returns {Object} - A mock response with study aids
 */
function generateMockResponse(question) {
    const responses = {
        'What is photosynthesis?': {
            response:
                "Photosynthesis is the process by which green plants and some other organisms use sunlight to synthesize foods with carbon dioxide and water. It's essentially how plants convert light energy into chemical energy.",
            summary: 'Key points: Plants use sunlight, CO₂, and water to produce food and oxygen.',
            quiz: 'What are the main inputs for photosynthesis?',
        },
        'Who wrote Romeo and Juliet?': {
            response: "Romeo and Juliet was written by William Shakespeare. It's one of his most famous tragedies, written early in his career.",
            summary: 'Key points: William Shakespeare, famous tragedy, early career.',
            quiz: 'Name another play written by Shakespeare.',
        },
        'What is the Pythagorean theorem?': {
            response:
                'The Pythagorean theorem states that in a right triangle, the square of the length of the hypotenuse equals the sum of the squares of the other two sides. It can be written as a² + b² = c², where c is the hypotenuse.',
            summary: 'Key points: Right triangle, a² + b² = c², hypotenuse.',
            quiz: 'In a right triangle with sides 3 and 4, what is the length of the hypotenuse?',
        },
        'What caused World War I?': {
            response:
                'World War I was triggered by the assassination of Archduke Franz Ferdinand of Austria, but underlying causes included militarism, alliances, imperialism, and nationalism among European powers.',
            summary: 'Key points: Assassination, militarism, alliances, imperialism, nationalism.',
            quiz: 'Which event directly triggered World War I?',
        },
        'How does gravity work?': {
            response:
                "Gravity is the force by which objects with mass attract one another. According to Einstein's general relativity, gravity is not a force but a consequence of the curvature of spacetime caused by mass and energy.",
            summary: 'Key points: Mass attraction, spacetime curvature, Einstein’s general relativity.',
            quiz: 'What causes the curvature of spacetime according to general relativity?',
        },
    };

    // Check if we have a predefined response
    for (const [key, value] of Object.entries(responses)) {
        if (question.toLowerCase().includes(key.toLowerCase())) {
            return {
                ...value,
                modelUsed: 'mock-model',
                complexity: determineQueryComplexity(question),
                progress: updateUserProgress(question, value.response),
            };
        }
    }

    // Default response
    return {
        response: `That's an interesting question about "${question
            .split(' ')
            .slice(0, 3)
            .join(
                ' ',
            )}...". In an academic context, I would approach this by researching credible sources and analyzing different perspectives. Could you provide more specific details about what aspect you're interested in?`,
        summary: 'No key points available yet; please clarify the query.',
        quiz: 'What specific aspect of this topic would you like to explore?',
        modelUsed: 'mock-model',
        complexity: 'simple',
        progress: updateUserProgress(question, 'default response'),
    };
}
