
import { mount } from 'svelte';
import Main from './pages/Main.svelte';
import './app.css';

// Set API base URL
const API_BASE = 'http://localhost:8000/api';

mount(Main, {
  target: document.getElementById('app'),
  props: {
    apiBase: API_BASE
  }
});
