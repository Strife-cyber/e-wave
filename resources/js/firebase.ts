// Import the functions you need from the SDKs you need
import { initializeApp } from 'firebase/app';
import { getDatabase } from 'firebase/database';

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
    apiKey: 'AIzaSyBQd0v6A2rjUk5NGli2dEpwfSNlCKYmkp8',
    authDomain: 'eduwave-75e01.firebaseapp.com',
    databaseURL: 'https://eduwave-75e01-default-rtdb.firebaseio.com',
    projectId: 'eduwave-75e01',
    storageBucket: 'eduwave-75e01.firebasestorage.app',
    messagingSenderId: '369806260456',
    appId: '1:369806260456:web:08786678914a066b3f315d',
    measurementId: 'G-J5HZZ91V7R',
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
export const database = getDatabase(app);
