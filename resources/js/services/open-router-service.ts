// OpenRouter API integration

// Replace with your actual API key
const OPENROUTER_API_KEY = import.meta.env.VITE_OPENROUTER_API_KEY || '';

/**
 * Sends a message to the OpenRouter API and returns the response
 * @param {Array} messages - Array of message objects with role and content
 * @returns {Promise<string>} - The assistant's response
 */
export async function sendMessageToOpenRouter(messages) {
    // If no API key is provided, use mock responses for development
    if (!OPENROUTER_API_KEY) {
        return generateMockResponse(messages[messages.length - 1].content);
    }

    try {
        const response = await fetch('https://openrouter.ai/api/v1/chat/completions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${OPENROUTER_API_KEY}`,
                'HTTP-Referer': window.location.origin, // Required by OpenRouter
                'X-Title': 'Academic Echo Chamber', // Optional app name
            },
            body: JSON.stringify({
                model: 'openai/gpt-3.5-turbo', // You can change the model as needed
                messages: messages,
            }),
        });

        if (!response.ok) {
            const errorData = await response.json();
            console.error('OpenRouter API error:', errorData);
            return new Error('Failed to get response from OpenRouter');
        }

        const data = await response.json();
        return data.choices[0].message.content;
    } catch (error) {
        console.error('Error calling OpenRouter API:', error);
        throw error;
    }
}

/**
 * Generates mock responses for development without an API key
 * @param {string} question - The user's question
 * @returns {string} - A mock response
 */
function generateMockResponse(question) {
    const responses = {
        'What is photosynthesis?':
            "Photosynthesis is the process by which green plants and some other organisms use sunlight to synthesize foods with carbon dioxide and water. It's essentially how plants convert light energy into chemical energy.",
        'Who wrote Romeo and Juliet?':
            "Romeo and Juliet was written by William Shakespeare. It's one of his most famous tragedies, written early in his career.",
        'What is the Pythagorean theorem?':
            'The Pythagorean theorem states that in a right triangle, the square of the length of the hypotenuse equals the sum of the squares of the other two sides. It can be written as a² + b² = c², where c is the hypotenuse.',
        'What caused World War I?':
            'World War I was triggered by the assassination of Archduke Franz Ferdinand of Austria, but underlying causes included militarism, alliances, imperialism, and nationalism among European powers.',
        'How does gravity work?':
            "Gravity is the force by which objects with mass attract one another. According to Einstein's general relativity, gravity is not a force but a consequence of the curvature of spacetime caused by mass and energy.",
    };

    // Check if we have a predefined response
    for (const [key, value] of Object.entries(responses)) {
        if (question.toLowerCase().includes(key.toLowerCase())) {
            return value;
        }
    }

    // Default response
    return `That's an interesting question about "${question.split(' ').slice(0, 3).join(' ')}...". In an academic context, I would approach this by researching credible sources and analyzing different perspectives. Could you provide more specific details about what aspect you're interested in?`;
}
