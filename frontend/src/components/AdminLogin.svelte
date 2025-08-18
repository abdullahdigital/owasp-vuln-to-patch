<script>
    import { createEventDispatcher } from 'svelte';
    let username = '';
    let password = '';
    let errorMessage = '';

    const dispatch = createEventDispatcher();

    function login() {
      if (username === 'admin' && password === 'supersecret') {
        errorMessage = '';
        dispatch('loginSuccess');
      } else {
        errorMessage = 'Invalid username or password.';
      }
    }
  </script>
  
  <section class="flex items-center justify-center min-h-screen bg-gray-50 font-poppins px-4">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg">
      <h2 class="text-2xl font-bold mb-6 text-sky-600">Admin Login</h2>
  
      {#if errorMessage}
        <div class="bg-red-100 text-red-700 p-2 mb-4 rounded">{errorMessage}</div>
      {/if}
  
      <form on:submit|preventDefault={login} class="space-y-4">
        <div>
          <label class="block mb-1 font-medium text-gray-700">Username</label>
          <input type="text" bind:value={username} required
                 class="w-full p-2 border border-gray-300 rounded focus:outline-sky-500" />
        </div>
        <div>
          <label class="block mb-1 font-medium text-gray-700">Password</label>
          <input type="password" bind:value={password} required
                 class="w-full p-2 border border-gray-300 rounded focus:outline-sky-500" />
        </div>
        <button type="submit" 
                class="w-full bg-sky-600 text-white py-2 rounded hover:bg-sky-700 transition">
          Login
        </button>
      </form>
    </div>
  </section>
  
  <style>
    .font-poppins {
      font-family: 'Poppins', sans-serif;
    }
  </style>
  