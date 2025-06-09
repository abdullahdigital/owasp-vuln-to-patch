<script>
  import { writable } from 'svelte/store';
  import { wrap } from 'svelte-spa-router/wrap';
  import Router from 'svelte-spa-router';

  import Home from '../pages/Home.svelte';
  import About from '../pages/About.svelte';
  import Services from '../pages/Services.svelte';
  import Appointment from '../pages/Appointment.svelte';
  import Contact from '../pages/Contact.svelte';
  import Login from '../pages/Login.svelte';
  import History from '../pages/History.svelte';
  import AdminLogin from '../pages/AdminLogin.svelte';
  import AdminDashboard from '../pages/AdminDashboard.svelte';

  const routes = {
    '/': Home,
    '/about': About,
    '/services': Services,
    '/appointment': Appointment,
    '/contact': Contact,
    '/login': Login,
    '/history': History,
     '/history/:id': History, 
    '/adminLogin': AdminLogin,
  '/adminDashboard': AdminDashboard,}
</script>

<Router {routes} />
