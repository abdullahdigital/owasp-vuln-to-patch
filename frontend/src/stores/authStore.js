// frontend/src/stores/authStore.js
import { writable } from 'svelte/store';
import { push } from 'svelte-spa-router';

// Initialize store with data from sessionStorage if available
const storedUser = sessionStorage.getItem('user');
const initialUser = storedUser ? JSON.parse(storedUser) : null;

export const user = writable(initialUser);

export const login = async (email, password) => {
    try {
        const response = await fetch('http://localhost:8000/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email, password }),
            credentials: 'include'
        });

        if (!response.ok) {
            throw new Error('Login failed');
        }

        const data = await response.json();
        const userData = {
            ...data.user,
            token: data.token
        };
        user.set(userData);
        sessionStorage.setItem('user', JSON.stringify(userData));
        return userData;
    } catch (error) {
        console.error('Login error:', error);
        throw error;
    }
};

export const register = async (firstName, lastName, email, password, phone) => {
    try {
        const response = await fetch('http://localhost:8000/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                first_name: firstName, 
                last_name: lastName, 
                email, 
                password,
                phone: phone || null
            }),
            credentials: 'include'
        });

        if (!response.ok) {
            throw new Error('Registration failed');
        }

        const data = await response.json();
        const userData = {
            ...data.user,
            token: data.token
        };
        user.set(userData);
        sessionStorage.setItem('user', JSON.stringify(userData));
        return userData;
    } catch (error) {
        console.error('Registration error:', error);
        throw error;
    }
};

export const logout = async () => {
    try {
        await fetch('http://localhost:8000/api/logout', {
            method: 'POST',
            credentials: 'include'
        });
    } catch (error) {
        console.error('Logout error:', error);
    } finally {
        user.set(null);
        sessionStorage.removeItem('user');
        push('/login');
    }
};

export const checkAuth = async () => {
    try {
        const response = await fetch('http://localhost:8000/api/user', {
            credentials: 'include'
        });

        if (response.ok) {
            const data = await response.json();
            const userData = {
                ...data.user,
                token: data.token
            };
            user.set(userData);
            sessionStorage.setItem('user', JSON.stringify(userData));
            return userData;
        }
    } catch (error) {
        console.error('Auth check error:', error);
    }
    return null;
};

// Helper function to get auth headers
// Usage: headers: { ...getAuthHeaders() }
export const token = {
    get: () => {
        const storedUser = sessionStorage.getItem('user');
        if (storedUser) {
            const user = JSON.parse(storedUser);
            return user.token;
        }
        return null;
    }
};

export const getAuthHeaders = () => {
    let headers = {};
    const storedUser = sessionStorage.getItem('user');
    if (storedUser) {
        const user = JSON.parse(storedUser);
        if (user.token) {
            headers['Authorization'] = `Bearer ${user.token}`;
        }
    }
    return headers;
};
