<script>
  import { push } from 'svelte-spa-router';
  import { onMount } from 'svelte';

  let email = "";
  let password = "";
  let firstName = "";
  let lastName = "";
  let phone = "";
  let confirmPassword = "";
  let isLogin = true;
  let notification = { show: false, message: '', isError: false };

  onMount(() => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      try {
        const user = JSON.parse(atob(token));
        push(user.is_admin ? '/admin' : '/appointments');
      } catch (e) {
        console.error("Invalid token:", e);
      }
    }
  });

  function toggleToLogin() {
    isLogin = true;
    notification.show = false;
  }

  function toggleToSignUp() {
    isLogin = false;
    notification.show = false;
  }

  async function handleSubmit(e) {
    e.preventDefault();
    notification.show = false;

    try {
      if (isLogin) {
        await handleLogin();
      } else {
        await handleSignup();
      }
    } catch (error) {
      showError(error.message);
    }
  }

  async function handleLogin() {
    const response = await fetch('http://localhost:8000/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    });

    const data = await response.json();

    if (data.status === "success") {
      localStorage.setItem('auth_token', data.token);
      showSuccess(`Logged in as ${data.user.email}`);
      push(data.user.is_admin ? '/admin' : '/');
    } else {
      // The backend already logs failed login attempts.
      // No need for a redundant log from the frontend here.
      throw new Error(data.message || "Login failed");
    }
  }

  async function handleSignup() {
    if (password !== confirmPassword) {
      throw new Error("Passwords don't match");
    }

    const response = await fetch('http://localhost:8000/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        signup: true,
        email,
        password,
        firstName,
        lastName,
        phone
      })
    });

    const data = await response.json();

    if (data.status === "success") {
      localStorage.setItem('auth_token', data.token);
      showSuccess(`Account created for ${email}`);
      push('/appointment');
    } else {
      throw new Error(data.message || "Signup failed");
    }
  }

  function showError(message) {
    notification = { show: true, message, isError: true };
  }

  function showSuccess(message) {
    notification = { show: true, message, isError: false };
  }

  // This `sendLogToBackend` function is still useful for other frontend-initiated logs
  // like the XSS detection or appointment actions in the AdminDashboard.
  async function sendLogToBackend(action, source = 'frontend', additionalData = null) {
    try {
        const response = await fetch('http://localhost:8000/logs.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, source, additional_data: additionalData }),
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('sendLogToBackend error response:', errorText);
            return;
        }

        const data = await response.json();
        console.log('Log sent successfully:', data);

    } catch (error) {
        console.error('sendLogToBackend network or other error:', error);
    }
  }

</script>


<div id="login" class="pt-32 pb-20 px-6">
  <section class="container mx-auto max-w-md">
    {#if notification.show}
      <div class={notification.isError ? 'error' : 'success'}>
        {notification.message}
      </div>
    {/if}

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="p-8">
        <!-- User Icon -->
        <div class="flex justify-center mb-8">
          <div class="bg-sky-100 p-4 rounded-full">
            <i class="fas fa-user-circle text-sky-500 text-5xl"></i>
          </div>
        </div>

        <!-- Toggle between Login and Signup -->
        <ul class="flex border-b mb-8">
          <li class="mr-1">
            <button 
              on:click={toggleToLogin} 
              class="bg-white inline-block py-2 px-4 font-semibold {isLogin ? 'text-sky-500 border-b-2 border-sky-500' : 'text-gray-500'}"
            >
              Login
            </button>
          </li>
          <li class="mr-1">
            <button 
              on:click={toggleToSignUp} 
              class="bg-white inline-block py-2 px-4 font-semibold {!isLogin ? 'text-sky-500 border-b-2 border-sky-500' : 'text-gray-500'}"
            >
              Sign Up
            </button>
          </li>
        </ul>

        <!-- Login Form -->
        {#if isLogin}
          <form on:submit={handleSubmit}>
            <div class="mb-6">
              <label class="block text-gray-700 font-medium mb-2">Email Address</label>
              <input 
                type="text" 
                bind:value={email} 
                placeholder="Your Email"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" 
                required
              >
            </div>

            <div class="mb-6">
              <label class="block text-gray-700 font-medium mb-2">Password</label>
              <input 
                type="password" 
                bind:value={password} 
                placeholder="Your Password"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" 
                required
              >
            </div>

            <button 
              type="submit" 
              class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300"
            >
              Login
            </button>
          </form>
        {:else}
          <!-- Signup Form -->
          <form on:submit={handleSubmit}>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div>
                <label class="block text-gray-700 font-medium mb-2">First Name</label>
                <input 
                  type="text" 
                  bind:value={firstName} 
                  placeholder="First Name"
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" 
                  required
                >
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-2">Last Name</label>
                <input 
                  type="text" 
                  bind:value={lastName} 
                  placeholder="Last Name"
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" 
                  required
                >
              </div>
            </div>

            <div class="mb-6">
              <label class="block text-gray-700 font-medium mb-2">Email Address</label>
              <input 
                type="email" 
                bind:value={email} 
                placeholder="Your Email"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" 
                required
              >
            </div>

            <div class="mb-6">
              <label class="block text-gray-700 font-medium mb-2">Phone Number</label>
              <input 
                type="tel" 
                bind:value={phone} 
                placeholder="Your Phone Number"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" 
                required
              >
            </div>

            <div class="mb-6">
              <label class="block text-gray-700 font-medium mb-2">Password</label>
              <input 
                type="password" 
                bind:value={password} 
                placeholder="Your Password"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" 
                required
              >
            </div>

            <div class="mb-6">
              <label class="block text-gray-700 font-medium mb-2">Confirm Password</label>
              <input 
                type="password" 
                bind:value={confirmPassword} 
                placeholder="Confirm Password"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" 
                required
              >
            </div>

            <button 
              type="submit" 
              class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300"
            >
              Sign Up
            </button>
          </form>
        {/if}
      </div>
    </div>
  </section>
</div>



<style>
  .error {
    background-color: #fee2e2;
    color: #b91c1c;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    border-left: 4px solid #b91c1c;
  }

  .success {
    background-color: #dcfce7;
    color: #166534;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    border-left: 4px solid #166534;
  }

  .nav-link {
    position: relative;
    color: #374151;
    font-weight: 500;
    transition: color 0.3s ease;
  }

  .nav-link:hover {
    color: #0284c7;
  }

  .nav-underline {
    position: absolute;
    bottom: -2px;
    left: 0;
    height: 2px;
    width: 0;
    background-color: #0ea5e9;
    transition: width 0.3s ease;
  }

  .nav-link:hover .nav-underline {
    width: 100%;
  }
</style>