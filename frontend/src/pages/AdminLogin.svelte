<script>
    import Navbar from '../components/Navbar.svelte';
    import Footer from '../components/Footer.svelte';
    import AdminLogin from '../components/AdminLogin.svelte';
    import { push } from 'svelte-spa-router';
  </script>
  
  <Navbar />
  <main class="container-fluid mx-auto p-6">
    <AdminLogin on:loginSuccess={() => push('/adminDashboard')} />
  </main>
  <Footer />