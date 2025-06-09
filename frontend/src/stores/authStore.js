// frontend/src/stores/authStore.js
import { writable } from 'svelte/store';

// Initialize stores with data from localStorage if available
const initialUser = JSON.parse(localStorage.getItem('user')) || null;
const initialToken = localStorage.getItem('token') || null;

export const user = writable(initialUser);
export const token = writable(initialToken);

export const login = (userData, userToken) => {
    user.set(userData);
    token.set(userToken);
    // Persist to localStorage
    localStorage.setItem('user', JSON.stringify(userData));
    localStorage.setItem('token', userToken);
};

export const logout = () => {
    user.set(null);
    token.set(null);
    // Clear from localStorage
    localStorage.removeItem('user');
    localStorage.removeItem('token');
};
