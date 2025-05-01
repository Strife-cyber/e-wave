/**
 * Generates a flashcard from a conversation
 * @param {string} question - The user's question
 * @param {string} answer - The assistant's answer
 * @returns {Object} - A flashcard object with question and answer
 */
export function generateFlashcardFromConversation(question, answer) {
    // Ensure the question ends with a question mark
    const formattedQuestion = question.endsWith('?') ? question : `${question}?`;

    // Create a concise answer for the flashcard
    let flashcardAnswer = answer;
    if (answer.length > 100) {
        // Find a good breaking point (end of sentence) if possible
        const periodIndex = answer.substring(0, 120).lastIndexOf('.');
        const endIndex = periodIndex > 50 ? periodIndex + 1 : 100;
        flashcardAnswer = answer.substring(0, endIndex) + (endIndex < answer.length ? '...' : '');
    }

    return {
        question: formattedQuestion,
        answer: flashcardAnswer,
    };
}

/**
 * Parses imported flashcards from JSON
 * @param {string} jsonData - JSON string of flashcard data
 * @returns {Array} - Array of flashcard objects
 */
export function parseImportedFlashcards(jsonData) {
    try {
        const data = JSON.parse(jsonData);

        // Validate the data structure
        if (!Array.isArray(data)) {
            return new Error('Invalid flashcard data format');
        }

        // Ensure each flashcard has the required fields
        return data.map((card) => {
            if (!card.question || !card.answer) {
                throw new Error('Invalid flashcard format');
            }

            return {
                question: card.question,
                answer: card.answer,
                flipped: false,
            };
        });
    } catch (error) {
        console.error('Error parsing flashcards:', error);
        throw error;
    }
}
