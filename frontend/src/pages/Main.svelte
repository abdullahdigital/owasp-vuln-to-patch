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
    '/adminDashboard': wrap({
      component: AdminDashboard,
      conditions: [
        async () => {
          const token = localStorage.getItem('auth_token');
          if (!token) return false;

          try {
            const response = await fetch('http://localhost:8000/verify_token.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
              }
            });
            
            if (!response.ok) return false;
            
            const result = await response.json();
            return result.is_admin === true;
          } catch (e) {
            console.error('Token verification failed:', e);
            return false;
          }
        }
      ]
    })
  }
</script>

<Router {routes} />
