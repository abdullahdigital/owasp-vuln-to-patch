<script>
  let name = "";
  let email = "";
  let phone = "";
  let date = "";
  let time = "";
  let service = "";
  let message = "";

  async function handleSubmit(e) {
    e.preventDefault();

    try {
      const response = await fetch("http://localhost:8000/appointment.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ name, email, phone, date, time, service, message }),
      });

      const result = await response.json();
      if (result.status === "success") {
        alert(result.message);
        // Clear form fields after successful submission
        name = "";
        email = "";
        phone = "";
        date = "";
        time = "";
        service = "";
        message = "";
      } else {
        alert("Error: " + result.message);
      }
    } catch (error) {
      alert("An error occurred: " + error.message);
    }
  }
</script>

<style>
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
</style>

<div id="appointment" class="pt-12 pb-20 px-6">
  <section class="container mx-auto max-w-4xl">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-4">Book Your Appointment</h1>
    <p class="text-xl text-center text-gray-600 mb-12">Schedule your visit with our dental experts today.</p>

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
            <input type="text" bind:value={name} placeholder="Full Name" class="input-field" required />
            <input type="email" bind:value={email} placeholder="Email Address" class="input-field" required />
            <input type="tel" bind:value={phone} placeholder="Phone Number" class="input-field" required />
            <input type="date" bind:value={date} class="input-field" required />

            <select bind:value={time} class="input-field" required>
              <option value="">Select Time</option>
              <option value="08:00">8:00 AM</option>
              <option value="09:00">9:00 AM</option>
              <option value="10:00">10:00 AM</option>
            </select>

            <select bind:value={service} class="input-field" required>
              <option value="">Select Service</option>
              <option value="General Checkup">General Checkup</option>
              <option value="Cleaning">Cleaning</option>
            </select>

            <textarea bind:value={message} placeholder="Additional Information" class="input-field"></textarea>
            <button type="submit" class="submit-btn">Book Appointment</button>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
