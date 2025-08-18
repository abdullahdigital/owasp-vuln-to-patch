<script>
  let name = "";
  let email = "";
  let message = ""; // This binds to the message input field
  let file = null;
  let errorMessage = "";

  // These are for your XXE modal, not directly related to XSS, but kept for context.
  let attackResult = "";
  let showResult = false;

  function handleFileUpload(e) {
      file = e.target.files[0];
  }

  async function handleSubmit(e) {
      e.preventDefault();
      errorMessage = "";
      showResult = false;

      // Reset the heading to its original state before new submission, for clarity.
      const pageHeading = document.querySelector('#contact h1');
      if (pageHeading) {
          pageHeading.textContent = "Contact Us";
      }

      const formData = new FormData();
      formData.append("name", name);
      formData.append("email", email);
      formData.append("message", message); // Send the user's message
      if (file) formData.append("file", file);

      try {
          const response = await fetch("http://localhost:8000/api/contact", {
              method: "POST",
              body: formData,
              headers: {
                  'Accept': 'application/json'
              }
          });

          const result = await response.json();

          if (!response.ok) {
              throw new Error(result.message || 'Server error');
          }

          // ***** SECURE XSS FIX: Use textContent instead of innerHTML *****
          // This treats the server response as plain text, preventing script execution.
          if (pageHeading && result.display_message) { // Use a safely prepared message from backend
              pageHeading.textContent = "Received: " + result.display_message;
          } else if (pageHeading && result.message) { // Fallback, though display_message is preferred
              pageHeading.textContent = "Received: " + result.message;
          }

          // If you want to show the XXE result, that part of your UI should also use textContent or a safe rendering method
          // (You already use textContent in your modal: '{attackResult}', '{dom.saveXML()}') which is good.
          if (result.xml_content) {
              attackResult = result.xml_content;
              showResult = true; // Show the XXE modal if XML content is returned
          }


          alert("Message processed!");
          resetForm();

      } catch (error) {
          errorMessage = error.message;
          console.error("Error sending message:", error);
          alert("Error: " + errorMessage);
      }
  }

  function resetForm() {
      name = "";
      email = "";
      message = "";
      file = null;
      const fileInput = document.getElementById("file");
      if (fileInput) fileInput.value = "";

      const pageHeading = document.querySelector('#contact h1');
      if (pageHeading) {
          pageHeading.textContent = "Contact Us"; // Reset to original safe text
      }
      // Also hide the XXE output section
      const xxeOutputDiv = document.getElementById("xxeOutput");
      if (xxeOutputDiv) xxeOutputDiv.classList.add('hidden');
  }

  // Bindings for form elements (assuming Svelte or similar, as you have bind:value)
  // If pure JS, you'd use document.getElementById('name').value
</script>

{#if showResult}
<div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
      <div class="p-4 border-b flex justify-between items-center bg-red-600 text-white">
          <h2 class="text-xl font-bold">💣 XXE Attack Successful - File Content Leaked</h2>
          <button on:click={() => showResult = false} class="text-white hover:text-gray-200">
              ✕
          </button>
      </div>
      <div class="p-4 bg-gray-900 text-green-400 overflow-auto flex-grow">
          <div class="font-mono text-sm whitespace-pre-wrap break-words">{attackResult}</div>
      </div>
      <div class="p-4 border-t bg-gray-100 flex justify-between items-center">
          <div class="text-sm text-gray-600">
              <strong>Vulnerability:</strong> XML External Entity (XXE) Injection
          </div>
          <strong>What can this attack do:</strong>
          <ul class="text-sm list-disc ml-5 text-red-500">
              <li>Reads sensitive files via entity injection (e.g., /etc/passwd).</li>
              <li>Performs SSRF to access internal services or cloud metadata.</li>
              <li>Causes DoS using recursive entities (e.g., billion laughs).</li>
          </ul>

          <button
              on:click={() => {
                  showResult = false;
                  resetForm();
              }}
              class="px-4 py-2 bg-sky-500 text-white rounded hover:bg-sky-600 transition"
          >
              Close & Reset Form
          </button>
      </div>
  </div>
</div>
{/if}


<div id="contact" class="pt-32 pb-20 px-6">
  <section class="container mx-auto max-w-4xl">
      <h1 class="text-4xl font-bold text-center text-gray-800 mb-4">Contact Us</h1>
      <p class="text-xl text-center text-gray-600 mb-12">We'd love to hear from you. Get in touch with us today!</p>

      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="md:flex">
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

              <div class="md:w-1/2 p-8">
                  <form on:submit={handleSubmit}>
                      <div class="mb-6">
                          <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
                          <input
                              type="text"
                              id="name"
                              bind:value={name}
                              placeholder="Your Name"
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 input-field"
                              required
                          />
                      </div>

                      <div class="mb-6">
                          <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                          <input
                              type="email"
                              id="email"
                              bind:value={email}
                              placeholder="Your Email"
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 input-field"
                              required
                          />
                      </div>

                      <div class="mb-6">
                          <label for="message" class="block text-gray-700 font-medium mb-2">Message</label>
                          <textarea
                              id="message"
                              bind:value={message}
                              placeholder="Your Message"
                              rows="4"
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 input-field"
                              required
                          ></textarea>
                      </div>

                      <div class="mb-6">
                          <label for="file" class="block text-gray-700 font-medium mb-2">Upload Document (XML, PDF)</label>
                          <input
                              type="file"
                              id="file"
                              on:change={handleFileUpload}
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 input-field"
                          />
                      </div>

                      <button
                          type="submit"
                          class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300"
                      >
                          Send Message
                      </button>
                  </form>

                  <div id="xxeOutput" class="mt-6 bg-gray-900 text-green-400 p-4 rounded-lg overflow-auto hidden">
                      <h4 class="text-white font-bold mb-2">💥 XXE Attack Result:</h4>
                      <pre id="xxeResult" class="whitespace-pre-wrap break-words"></pre>
                  </div>
                  
              </div>
          </div>
      </div>
  </section>
</div>