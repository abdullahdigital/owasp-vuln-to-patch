
import { mount } from 'svelte';
import Main from './pages/Main.svelte';
import './app.css';


mount(Main, {
  target: document.getElementById('app'),
  props: {} // Add any props here
});
