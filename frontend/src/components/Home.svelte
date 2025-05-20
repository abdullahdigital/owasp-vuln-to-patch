<script>
  export let title = "Dental Clinic";
  
  function showPage(page) {
    console.log(`Navigating to: ${page}`);
  }
</script>

<!-- Home Page -->
<div id="home" class="page active">
  <!-- Hero Section -->
  <section class="hero min-h-screen flex items-center justify-center text-white pt-20 bg-gradient-to-r from-sky-500 to-blue-600">
    <div class="container mx-auto px-6 text-center">
      <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in">Your Perfect Smile Starts Here</h1>
      <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto animate-fade-in delay-100">
        Experience world-class dental care in a comfortable and relaxing environment.
      </p>
      <button
        class="bg-white text-sky-600 hover:bg-gray-100 font-bold py-3 px-8 rounded-full text-lg transition-all duration-300 transform hover:scale-105 animate-fade-in delay-200"
        on:click={() => showPage('appointment')}
      >
        Book an Appointment
      </button>
    </div>
  </section>

  <!-- Services Overview -->
  <section class="py-20 bg-white">
    <div class="container mx-auto px-6">
      <h2 class="text-3xl font-bold text-center text-gray-800 mb-16">Our Dental Services</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {#each [
          { icon: 'tooth', title: 'General Dentistry', description: 'Comprehensive dental care including checkups, cleanings, fillings, and preventive treatments.' },
          { icon: 'teeth-open', title: 'Cosmetic Dentistry', description: 'Enhance your smile with teeth whitening, veneers, bonding, and other cosmetic procedures.' },
          { icon: 'teeth', title: 'Orthodontics', description: 'Straighten your teeth with traditional braces or clear aligners for a perfect smile.' }
        ] as service}
          <div class="service-card bg-white rounded-xl shadow-lg p-8 transition-all duration-300 hover:shadow-xl hover:-translate-y-2">
            <div class="text-sky-500 mb-4">
              <i class={`fas fa-${service.icon} text-4xl`}></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-3">{service.title}</h3>
            <p class="text-gray-600">{service.description}</p>
            <button
              class="mt-4 text-sky-500 font-semibold hover:text-sky-600 transition flex items-center group"
              on:click={() => showPage('services')}
            >
              Learn More <span class="ml-1 group-hover:translate-x-1 transition-transform">→</span>
            </button>
          </div>
        {/each}
      </div>
      
      <div class="text-center mt-12">
        <button 
          class="bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-8 rounded-full text-lg transition-all duration-300 transform hover:scale-105"
          on:click={() => showPage('services')}
        >
          View All Services
        </button>
      </div>
    </div>
  </section>

  <!-- Why Choose Us -->
  <section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
      <h2 class="text-3xl font-bold text-center text-gray-800 mb-16">Why Choose {title}?</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {#each [
          { icon: 'user-md', title: 'Expert Dentists', description: 'Our team of highly qualified dentists provides personalized care with the latest techniques.' },
          { icon: 'shield-alt', title: 'Advanced Technology', description: 'We use state-of-the-art equipment for accurate diagnosis and comfortable treatments.' },
          { icon: 'heart', title: 'Patient-Centered Care', description: 'Your comfort and satisfaction are our top priorities at every visit.' }
        ] as feature}
          <div class="text-center p-6 hover:transform hover:-translate-y-2 transition-transform duration-300">
            <div class="bg-sky-100 w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-4">
              <i class={`fas fa-${feature.icon} text-sky-500 text-3xl`}></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">{feature.title}</h3>
            <p class="text-gray-600">{feature.description}</p>
          </div>
        {/each}
      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section class="py-20 bg-white">
    <div class="container mx-auto px-6">
      <h2 class="text-3xl font-bold text-center text-gray-800 mb-16">What Our Patients Say</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {#each [
          { 
            rating: 5,
            quote: "I've been coming to {title} for years. The staff is friendly and professional, and they always make me feel comfortable. My smile has never looked better!",
            image: 'https://randomuser.me/api/portraits/women/43.jpg',
            name: 'Sarah Johnson',
            role: 'Regular Patient'
          },
          {
            rating: 5,
            quote: "The cosmetic dentistry work I had done at {title} completely transformed my smile. I couldn't be happier with the results. Highly recommend!",
            image: 'https://randomuser.me/api/portraits/men/32.jpg',
            name: 'Michael Chen',
            role: 'Cosmetic Patient'
          }
        ] as testimonial}
          <div class="bg-gray-50 p-8 rounded-xl shadow hover:shadow-lg transition">
            <div class="flex items-center mb-4">
              {#each Array(testimonial.rating) as _, i}
                <i class="fas fa-star text-yellow-400 mr-1"></i>
              {/each}
            </div>
            <p class="text-gray-600 mb-6">"{testimonial.quote.replace('{title}', title)}"</p>
            <div class="flex items-center">
              <img src={testimonial.image} alt="Patient" class="w-12 h-12 rounded-full mr-4">
              <div>
                <h4 class="font-bold text-gray-800">{testimonial.name}</h4>
                <p class="text-gray-500 text-sm">{testimonial.role}</p>
              </div>
            </div>
          </div>
        {/each}
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-16 bg-sky-600 text-white">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-3xl font-bold mb-6">Ready for Your Perfect Smile?</h2>
      <p class="text-xl mb-8 max-w-2xl mx-auto">Schedule your appointment today and experience the difference at {title}.</p>
      <button 
        class="bg-white text-sky-600 hover:bg-gray-100 font-bold py-3 px-8 rounded-full text-lg transition-all duration-300 transform hover:scale-105"
        on:click={() => showPage('appointment')}
      >
        Book Now
      </button>
    </div>
  </section>
</div>

<style global>
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .animate-fade-in {
    animation: fadeIn 0.8s ease-out forwards;
  }
  
  .delay-100 {
    animation-delay: 0.1s;
  }
  
  .delay-200 {
    animation-delay: 0.2s;
  }
  
  .service-card:hover i {
    animation: pulse 1.5s infinite;
  }
  
  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
  }
  
  .hero {
    background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
  }
</style>