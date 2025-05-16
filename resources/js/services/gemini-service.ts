import { GoogleGenAI } from '@google/genai';

const VITE_GEMINI_API_KEY = import.meta.env.VITE_GEMINI_API_KEY || '';

const ai = new GoogleGenAI({ apiKey: VITE_GEMINI_API_KEY });

export default async function prompt(content: string) {
    const response = await ai.models.generateContent({
        model: 'gemini-2.0-flash',
        contents: content,
    });

    return response.text;
}
