<script>
    let name = "";
    let email = "";
    let message = "";
    let file;
  
    function handleFileUpload(e) {
      file = e.target.files[0];
    }
  
    function handleSubmit(e) {
      e.preventDefault();
      alert(`Message from ${name}: ${message}`);
    }
  </script>
  
  <!-- Contact Us Page -->
  <div id="contact" class="pt-32 pb-20 px-6">
    <section class="container mx-auto max-w-4xl">
      <h1 class="text-4xl font-bold text-center text-gray-800 mb-4">Contact Us</h1>
      <p class="text-xl text-center text-gray-600 mb-12">We'd love to hear from you. Get in touch with us today!</p>
  
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="md:flex">
          <!-- Contact Information -->
          <div class="md:w-1/2 bg-sky-500 text-white p-8">
            <h2 class="text-2xl font-bold mb-6">Get in Touch</h2>
            <p class="text-sky-100 mb-4">
              Reach out to us with any questions or feedback. Our team is here to help.
            </p>
            <h3 class="font-bold mb-2">Contact Information</h3>
            <p class="text-sky-100">Phone: (555) 987-6543</p>
            <p class="text-sky-100">Email: support@smilecare.com</p>
            <p class="text-sky-100">Address: 456 Care Blvd, Suite 100, New York, NY 10002</p>
          </div>
  
          <!-- Contact Form -->
          <div class="md:w-1/2 p-8">
            <form on:submit={handleSubmit}>
              <div class="mb-6">
                <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
                <input type="text" id="name" bind:value={name} placeholder="Your Name" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 input-field" required>
              </div>
  
              <div class="mb-6">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                <input type="email" id="email" bind:value={email} placeholder="Your Email" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 input-field" required>
              </div>
  
              <div class="mb-6">
                <label for="message" class="block text-gray-700 font-medium mb-2">Message</label>
                <textarea id="message" bind:value={message} placeholder="Your Message" rows="4" 
                          class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 input-field" required></textarea>
              </div>
  
              <div class="mb-6">
                <label for="file" class="block text-gray-700 font-medium mb-2">Upload Document (XML, PDF)</label>
                <input type="file" id="file" on:change={handleFileUpload} 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 input-field" required>
              </div>
  
              <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                Send Message
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>
  