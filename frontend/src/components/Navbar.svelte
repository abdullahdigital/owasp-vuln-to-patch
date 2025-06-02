<script>
  import { push } from "svelte-spa-router";
  import { onMount } from 'svelte';

  export let title = "Dental Clinic";
  let mobileMenuOpen = false;
  let scrolled = false;
  let isLoggedIn = false;
  let user = null;
  let showDropdown = false; // New state for dropdown visibility

  // Check login status on mount
  onMount(() => {
    updateLoginStatus();
  });

  function updateLoginStatus() {
    const token = localStorage.getItem('auth_token');
    if (token) {
      try {
        user = JSON.parse(atob(token));
        isLoggedIn = true;
      } catch (e) {
        console.error("Invalid token:", e);
        localStorage.removeItem('auth_token');
        isLoggedIn = false;
        user = null;
      }
    } else {
      isLoggedIn = false;
      user = null;
    }
  }

  function toggleMobileMenu() {
    mobileMenuOpen = !mobileMenuOpen;
  }

  function navigateTo(path) {
    push(path);
    mobileMenuOpen = false;
  }

  function handleLogout() {
    localStorage.removeItem('auth_token');
    updateLoginStatus();
    push('/');
    // Optional: reload the page to reset state
    window.location.reload();
  }

  function toggleDropdown() {
    showDropdown = !showDropdown;
  }

  function closeDropdown() {
    showDropdown = false;
  }

  // Scroll effect
  if (typeof window !== 'undefined') {
    window.addEventListener('scroll', () => {
      scrolled = window.scrollY > 10;
    });
  }
</script>

<nav
  class="fixed w-full top-0 z-50 transition-all duration-300 ease-out {scrolled ? 'py-3 bg-white shadow-lg' : 'py-4 bg-white/90'} backdrop-blur-sm"
>
  <div class="max-w-7xl mx-auto px-6">
    <div class="flex justify-between items-center">
      <!-- Logo -->
      <button
        on:click={() => navigateTo('/')}
        class="text-2xl font-bold text-sky-600 flex items-center hover:text-sky-700 transition-colors duration-300 focus:outline-none"
        aria-label="Home"
      >
        <i class="fas fa-tooth mr-2 text-sky-500 animate-pulse"></i>
        <span class="hover:scale-105 transition-transform duration-200">{title}</span>
      </button>

      <!-- Desktop Menu -->
      <div class="hidden md:flex space-x-8 items-center">
        <button class="nav-link relative group" on:click={() => navigateTo('/')}>
          <span>Home</span>
          <span class="nav-underline"></span>
        </button>
        <button class="nav-link relative group" on:click={() => navigateTo('/about')}>
          <span>About</span>
          <span class="nav-underline"></span>
        </button>
        <button class="nav-link relative group" on:click={() => navigateTo('/services')}>
          <span>Services</span>
          <span class="nav-underline"></span>
        </button>
        <button class="nav-link relative group" on:click={() => navigateTo('/appointment')}>
          <span>Appointment</span>
          <span class="nav-underline"></span>
        </button>
        <button class="nav-link relative group" on:click={() => navigateTo('/contact')}>
          <span>Contact</span>
          <span class="nav-underline"></span>
        </button>

        {#if isLoggedIn}
          <!-- User dropdown with click handler -->
          <div class="relative">
            <button 
              on:click={toggleDropdown}
              class="flex items-center space-x-2 focus:outline-none hover:text-sky-600 transition-colors"
            >
              <!-- <span class="text-gray-700">Hi, {user?.first_name || 'User'}</span> -->
              <span class="text-gray-700">Hi, test</span>
              <i class="fas fa-user-circle text-sky-500"></i>
            </button>
            {#if showDropdown}
              <div 
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                use:clickOutside={closeDropdown}
              >
                <button 
                  on:click={handleLogout}
                  class="block px-4 py-2 text-gray-700 hover:bg-gray-100 w-full text-left transition-colors"
                >
                  <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
              </div>
            {/if}
          </div>
        {:else}
          <button
            class="px-4 py-2 bg-gradient-to-r from-sky-500 to-blue-600 text-white rounded-full hover:from-sky-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg"
            on:click={() => navigateTo('/login')}
          >
            Login
          </button>
        {/if}
      </div>

      <!-- Mobile menu toggle -->
      <button
        class="md:hidden text-gray-700 focus:outline-none"
        on:click={toggleMobileMenu}
        aria-label="Menu"
      >
        <i class={`fas ${mobileMenuOpen ? 'fa-times' : 'fa-bars'} text-2xl transition-transform duration-300 ${mobileMenuOpen ? 'rotate-90' : ''}`}></i>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div
      class={`md:hidden overflow-hidden transition-all duration-500 ease-in-out ${mobileMenuOpen ? 'max-h-96 py-4' : 'max-h-0 py-0'}`}
    >
      <div class="flex flex-col space-y-3 mt-4">
        <button class="nav-mobile-link" on:click={() => navigateTo('/')}>Home</button>
        <button class="nav-mobile-link" on:click={() => navigateTo('/about')}>About</button>
        <button class="nav-mobile-link" on:click={() => navigateTo('/services')}>Services</button>
        <button class="nav-mobile-link" on:click={() => navigateTo('/appointment')}>Appointment</button>
        <button class="nav-mobile-link" on:click={() => navigateTo('/contact')}>Contact</button>
        
        {#if isLoggedIn}
          <button
            class="mt-2 px-4 py-2 bg-red-500 text-white rounded-full text-center hover:bg-red-600 transition-colors duration-300"
            on:click={handleLogout}
          >
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </button>
        {:else}
          <button
            class="mt-2 px-4 py-2 bg-sky-500 text-white rounded-full text-center hover:bg-sky-600 transition-colors duration-300"
            on:click={() => navigateTo('/login')}
          >
            Login
          </button>
        {/if}
      </div>
    </div>
  </div>
</nav>

<style global>
  :root {
    --primary: #0ea5e9;
  }

  body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8fafc;
    padding-top: 80px; /* Space for fixed navbar */
  }

  .nav-link {
    position: relative;
    color: #374151; /* Gray-700 */
    font-weight: 500;
    transition: color 0.3s ease, transform 0.2s ease;
  }

  .nav-link:hover {
    color: #0284c7; /* Sky-600 */
    transform: scale(1.05); /* Slightly increase size on hover */
  }

  .nav-link:active {
    transform: scale(0.95); /* Slightly shrink on click */
  }

  .nav-underline {
    position: absolute;
    bottom: -2px;
    left: 0;
    height: 2px;
    width: 0;
    background-color: #0ea5e9; /* Sky-500 */
    transition: width 0.3s ease;
  }

  .nav-link:hover .nav-underline {
    width: 100%; /* Full width on hover */
  }

  .nav-mobile-link {
    padding: 0.5rem 1rem;
    color: #374151;
    border-radius: 0.5rem;
    transition: transform 0.2s ease, background-color 0.2s ease, color 0.2s ease;
  }

  .nav-mobile-link:hover {
    background-color: #e0f2fe; /* Sky-50 */
    color: #0284c7; /* Sky-600 */
    transform: scale(1.02); /* Slightly increase size on hover */
  }

  .nav-mobile-link:active {
    transform: scale(0.95); /* Slightly shrink on click */
  }

  html {
    scroll-behavior: smooth;
  }
</style>

<script context="module">
  // Click outside directive to close dropdown
  export function clickOutside(node, callback) {
    const handleClick = event => {
      if (node && !node.contains(event.target) && !event.defaultPrevented) {
        callback();
      }
    };

    document.addEventListener('click', handleClick, true);

    return {
      destroy() {
        document.removeEventListener('click', handleClick, true);
      }
    };
  }
</script>