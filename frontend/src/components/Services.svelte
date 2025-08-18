<script>
  import { push } from 'svelte-spa-router'
  import { onMount } from 'svelte';

  let services = [];
  let loading = true;
  let error = null;

  // Fetch services from backend
  onMount(async () => {
    try {
      const response = await fetch('http://localhost:8000/api/services', {
        credentials: 'include'
      });
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      services = await response.json();
      loading = false;
    } catch (err) {
      error = err.message;
      loading = false;
    }
  });

  // Function to navigate to appointment page
  function bookAppointment() {
    push('/appointment')
  }
</script>

<section id="services" class="pt-22 pb-0 px-6 bg-gray-50">
  <div class="container mx-auto">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-4">Our Dental Services</h1>
    <p class="text-xl text-center text-gray-600 max-w-3xl mx-auto mb-16">
      Comprehensive dental care tailored to your unique needs and goals.
    </p>

    {#if loading}
      <div class="text-center py-12">
        <i class="fas fa-spinner fa-spin text-4xl text-sky-500"></i>
        <p class="mt-4 text-gray-600">Loading services...</p>
      </div>
    {:else if error}
      <div class="text-center py-12">
        <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
        <p class="mt-4 text-gray-600">Error loading services: {error}</p>
      </div>
    {:else}
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">
        {#each services as service}
          <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
            <div class="bg-sky-100 w-full h-48 flex items-center justify-center">
              <i class="fas fa-tooth text-6xl text-sky-500"></i>
            </div>
            <div class="p-6">
              <div class="flex items-center mb-4">
                <div class="bg-sky-100 p-3 rounded-full mr-4">
                  <i class="fas fa-tooth text-sky-500"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">{service.name}</h3>
              </div>
              <p class="text-gray-600 mb-4">
                {service.description}
              </p>
              <div class="mb-6">
                <p class="text-lg font-bold text-sky-600">${service.price}</p>
              </div>
              <button class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-2 px-4 rounded transition" on:click={bookAppointment}>
                Book Appointment
              </button>
            </div>
          </div>
        {/each}
      </div>
    {/if}
  </div>
</section>
