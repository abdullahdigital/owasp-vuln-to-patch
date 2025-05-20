<script>
    let email = '';
    let password = '';
    function handleLogin() {
      alert(`Email: ${email}, Password: ${password}`);
    }
  </script>
  
  <div class="p-6 bg-white shadow-md rounded-md">
    <h2 class="text-xl mb-4">Login</h2>
    <input type="email" placeholder="Email" bind:value={email} class="border p-2 w-full mb-2" />
    <input type="password" placeholder="Password" bind:value={password} class="border p-2 w-full mb-2" />
    <button on:click={handleLogin} class="bg-blue-500 text-white p-2 rounded w-full">Login</button>
  </div>
  