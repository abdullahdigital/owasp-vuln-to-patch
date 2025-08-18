<script>
  import { user } from '../stores/authStore.js';
  
  let formData = {
    name: '',
    date: '',
    service: '',
    phone: '',
    message: ''
  };
  
  let notification = {
    show: false,
    message: '',
    isError: false
  };

  async function handleSubmit(e) {
    e.preventDefault();
    notification.show = false;
    
    try {
        const currentUser = $user;
        if (!currentUser) {
            notification = {
                show: true,
                message: "Please login to book an appointment",
                isError: true
            };
            return;
        }
        
        const response = await fetch('http://localhost:8000/api/appointments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${currentUser.token}`
            },
            credentials: 'include',
            body: JSON.stringify({
                name: formData.name,
                service_id: formData.service,
                date: formData.date,
                phone: formData.phone,
                notes: formData.message
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(`HTTP error! status: ${response.status}. Errors: ${JSON.stringify(errorData.errors || errorData)}`);
        }
        
        const result = await response.json();
        
        notification = {
            show: true,
            message: result.message || 'Appointment booked successfully!',
            isError: false
        };
        
        // Reset form after successful submission
        formData = {
            name: '',
            service: '',
            date: '',
            phone: '',
            message: ''
        };
        
    } catch (error) {
        notification = {
            show: true,
            message: "Error: " + error.message,
            isError: true
        };
    }
}
</script>

<style>
  /* Your existing styles remain unchanged */
  .input-field {
    width: 100%;
    padding: 0.75rem;
    margin-bottom: 1rem;
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    transition: border-color 0.3s ease;
  }

  .input-field:focus {
    border-color: #38bdf8;
    outline: none;
  }

  .submit-btn {
    width: 100%;
    background-color: #38bdf8;
    color: white;
    font-weight: bold;
    padding: 0.75rem;
    border-radius: 0.5rem;
    transition: background-color 0.3s ease;
  }

  .submit-btn:hover {
    background-color: #0ea5e9;
  }

  .error {
    background-color: #fee2e2;
    color: #b91c1c;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
  }

  .success {
    background-color: #dcfce7;
    color: #166534;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
  }
</style>

<div id="appointment" class="pt-12 pb-20 px-6">
  <section class="container mx-auto max-w-4xl">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-4">Book Your Appointment</h1>
    <p class="text-xl text-center text-gray-600 mb-12">Schedule your visit with our dental experts today.</p>
   
    {#if notification.show}
      <div class={notification.isError ? 'error' : 'success'}>
        {notification.message}
      </div>
    {/if}
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="md:flex">
        <div class="md:w-1/2 bg-sky-500 text-white p-8">
          <h2 class="text-2xl font-bold mb-6">Appointment Information</h2>
          <p>Monday - Friday: 8:00 AM - 6:00 PM</p>
          <p>Saturday: 9:00 AM - 2:00 PM</p>
          <p>Sunday: Closed</p>
          <p class="mt-4">Phone: (555) 123-4567</p>
          <p>Email: info@smilecare.com</p>
        </div>

        <div class="md:w-1/2 p-8">
          <form on:submit={handleSubmit}>
            <input type="text" bind:value={formData.name} placeholder="Full Name" class="input-field" required />
            
            <input type="date" bind:value={formData.date} class="input-field" required />
            
            <input type="tel" bind:value={formData.phone} placeholder="Phone Number" class="input-field" required />

            <select bind:value={formData.service} class="input-field" required>
              <option value="">Select Service</option>
              <option value="1">General Checkup</option>
              <option value="2">Cleaning</option>
              <option value="3">Tooth Extraction</option>
              <option value="4">Root Canal</option>
            </select>

            <textarea bind:value={formData.message} placeholder="Additional Information" class="input-field"></textarea>
            <button type="submit" class="submit-btn">Book Appointment</button>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>