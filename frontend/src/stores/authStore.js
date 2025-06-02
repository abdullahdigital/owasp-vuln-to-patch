// src/stores/authStore.js
import { writable } from 'svelte/store';

// Safe access to localStorage
const safeGet = (key) => {
    if (typeof localStorage !== 'undefined') {
        return localStorage.getItem(key);
    }
    return null;
};

const safeSet = (key, value) => {
    if (typeof localStorage !== 'undefined') {
        if (value === null || value === undefined) {
            localStorage.removeItem(key);
        } else {
            localStorage.setItem(key, value);
        }
    }
};

// Initialize from localStorage
const storedUser = safeGet('user');
const storedToken = safeGet('token');

export const user = writable(storedUser ? JSON.parse(storedUser) : null);
export const token = writable(storedToken || null);

// Sync with localStorage
user.subscribe(value => {
    safeSet('user', value ? JSON.stringify(value) : null);
});

token.subscribe(value => {
    safeSet('token', value);
});

// Logout function
export function logout() {
    user.set(null);
    token.set(null);
}