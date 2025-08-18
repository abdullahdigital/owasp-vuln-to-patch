<script>
  import { push } from 'svelte-spa-router';
  import { onMount } from 'svelte';
  
  let services = [];
  let loading = false;
  let error = null;
  let success = false;
  
  // Form fields
  let email = '';
  let phone = '';
  let service = '';
  let date = '';
  let time = '';
  let notes = '';
  
  // Fetch available services
  onMount(async () => {
    try {
      const response = await fetch('http://localhost:8000/api/services');
      if (!response.ok) throw new Error('Failed to fetch services');
      services = await response.json();
    } catch (err) {
      error = err.message;
    }
  });
  
  // Handle form submission
  async function handleSubmit(e) {
    e.preventDefault();
    loading = true;
    error = null;
    
    try {
      const response = await fetch('http://localhost:8000/api/appointments', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          email,
          phone,
          service_id: service,
          date,
          time,
          notes
        }),
        credentials: 'include'
      });
      
      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to create appointment');
      }
      
      const data = await response.json();
      success = true;
      // Reset form after successful submission
      email = '';
      phone = '';
      service = '';
      date = '';
      time = '';
      notes = '';
      
      // Redirect to appointments page after 2 seconds
      setTimeout(() => push('/appointments'), 2000);
    } catch (err) {
      error = err.message;
    } finally {
      loading = false;
    }
  }
</script>

<section class="py-12 px-6 bg-gray-50">
  <div class="container mx-auto max-w-2xl">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Book an Appointment</h1>
    
    {#if success}
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        Appointment booked successfully! Redirecting to appointments page...
      </div>
    {:else}
      <form on:submit={handleSubmit} class="bg-white rounded-lg shadow-md p-8">
        {#if error}
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {error}
          </div>
        {/if}
        
        <div class="mb-6">
          <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
          <input
            type="email"
            id="email"
            bind:value={email}
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
            required
          >
        </div>
        
        <div class="mb-6">
          <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
          <input
            type="tel"
            id="phone"
            bind:value={phone}
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
            required
          >
        </div>
        
        <div class="mb-6">
          <label for="service" class="block text-gray-700 font-medium mb-2">Service</label>
          <select
            id="service"
            bind:value={service}
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
            required
          >
            <option value="">Select a service</option>
            {#each services as s}
              <option value={s.id}>{s.name}</option>
            {/each}
          </select>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <label for="date" class="block text-gray-700 font-medium mb-2">Date</label>
            <input
              type="date"
              id="date"
              bind:value={date}
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
              required
            >
          </div>
          
          <div>
            <label for="time" class="block text-gray-700 font-medium mb-2">Time</label>
            <input
              type="time"
              id="time"
              bind:value={time}
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
              required
            >
          </div>
        </div>
        
        <div class="mb-8">
          <label for="notes" class="block text-gray-700 font-medium mb-2">Additional Notes</label>
          <textarea
            id="notes"
            bind:value={notes}
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
            rows="4"
          ></textarea>
        </div>
        
        <button
          type="submit"
          class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300"
          disabled={loading}
        >
          {#if loading}
            <i class="fas fa-spinner fa-spin mr-2"></i> Processing...
          {:else}
            Book Appointment
          {/if}
        </button>
      </form>
    {/if}
  </div>
</section>