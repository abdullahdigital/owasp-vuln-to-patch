<script>
  let email = "";
  let password = "";
  let firstName = "";
  let lastName = "";
  let phone = "";
  let confirmPassword = "";
  let isLogin = true;

  function toggleToLogin() {
      isLogin = true;
  }

  function toggleToSignUp() {
      isLogin = false;
  }

  function handleSubmit(e) {
      e.preventDefault();
      if (isLogin) {
          alert(`Logged in as ${email}`);
      } else {
          alert(`Account created for ${firstName} ${lastName}`);
      }
  }
</script>
<!-- Login/Signup Page -->
<div id="login" class="pt-32 pb-20 px-6">
  <section class="container mx-auto max-w-md">
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="p-8">
              <!-- User Icon -->
              <div class="flex justify-center mb-8">
                  <div class="bg-sky-100 p-4 rounded-full">
                      <i class="fas fa-user-circle text-sky-500 text-5xl"></i>
                  </div>
              </div>

              <!-- Toggle between Login and Signup -->
              <ul class="flex border-b mb-8" id="authTabs">
                  <li class="mr-1">
                      <button 
                          on:click={toggleToLogin} 
                          class="bg-white inline-block py-2 px-4 font-semibold {isLogin ? 'text-sky-500 border-b-2 border-sky-500' : 'text-gray-500'}"
                          aria-selected={isLogin}
                          aria-label="Login">
                          Login
                      </button>
                  </li>
                  <li class="mr-1">
                      <button 
                          on:click={toggleToSignUp} 
                          class="bg-white inline-block py-2 px-4 font-semibold {!isLogin ? 'text-sky-500 border-b-2 border-sky-500' : 'text-gray-500'}"
                          aria-selected={!isLogin}
                          aria-label="Sign Up">
                          Sign Up
                      </button>
                  </li>
              </ul>

              <!-- Login Form -->
              {#if isLogin}
                  <form on:submit={handleSubmit}>
                      <div class="mb-6">
                          <label for="login-email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                          <input type="email" bind:value={email} placeholder="Your Email"
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" required>
                      </div>

                      <div class="mb-6">
                          <label for="login-password" class="block text-gray-700 font-medium mb-2">Password</label>
                          <input type="password" bind:value={password} placeholder="Your Password"
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" required>
                      </div>

                      <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                          Login
                      </button>
                  </form>
              {/if}

              <!-- Signup Form -->
              {#if !isLogin}
                  <form on:submit={handleSubmit}>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                          <div>
                              <label for="first-name" class="block text-gray-700 font-medium mb-2">First Name</label>
                              <input type="text" bind:value={firstName} placeholder="First Name"
                                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" required>
                          </div>
                          <div>
                              <label for="last-name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                              <input type="text" bind:value={lastName} placeholder="Last Name"
                                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" required>
                          </div>
                      </div>

                      <div class="mb-6">
                          <label for="signup-email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                          <input type="email" bind:value={email} placeholder="Your Email"
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" required>
                      </div>

                      <div class="mb-6">
                          <label for="signup-phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                          <input type="tel" bind:value={phone} placeholder="Your Phone Number"
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" required>
                      </div>

                      <div class="mb-6">
                          <label for="signup-password" class="block text-gray-700 font-medium mb-2">Password</label>
                          <input type="password" bind:value={password} placeholder="Your Password"
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" required>
                      </div>

                      <div class="mb-6">
                          <label for="confirm-password" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                          <input type="password" bind:value={confirmPassword} placeholder="Confirm Password"
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" required>
                      </div>

                      <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                          Sign Up
                      </button>
                  </form>
              {/if}
          </div>
      </div>
  </section>
</div>
