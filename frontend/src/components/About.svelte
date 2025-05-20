<script>
    export let clinicName = "SmileCare Dental";
    
    // Team members data
    const teamMembers = [
      {
        name: "Dr. Emily Wilson",
        role: "Founder & Lead Dentist",
        bio: "Specializes in cosmetic and restorative dentistry with over 15 years of experience.",
        image: "https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
      },
      {
        name: "Dr. Michael Rodriguez",
        role: "Orthodontist",
        bio: "Specializes in braces and Invisalign treatments for patients of all ages.",
        image: "https://images.unsplash.com/photo-1622253692010-333f2da60341?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1528&q=80"
      },
      {
        name: "Dr. Sarah Johnson",
        role: "Pediatric Dentist",
        bio: "Makes dental visits fun and comfortable for our youngest patients.",
        image: "https://images.unsplash.com/photo-1594824476967-48c8b9642736?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1374&q=80"
      }
    ];
  
    // Technology features
    const technologies = [
      {
        icon: "teeth",
        title: "Digital X-Rays",
        description: "Reduces radiation exposure by up to 90% compared to traditional x-rays."
      },
      {
        icon: "camera",
        title: "Intraoral Cameras",
        description: "Allows you to see what we see for better understanding of your dental health."
      },
      {
        icon: "laser",
        title: "Laser Dentistry",
        description: "Minimally invasive treatments with less discomfort and faster healing."
      }
    ];
  
    // Core values
    const values = [
      {
        icon: "heartbeat",
        title: "Patient-Centered Care",
        description: "We listen to your concerns and tailor treatments to your unique needs and goals."
      },
      {
        icon: "flask",
        title: "Evidence-Based Practice",
        description: "Our treatments are based on the latest scientific research and proven techniques."
      },
      {
        icon: "hand-holding-heart",
        title: "Compassionate Service",
        description: "We understand dental visits can be stressful and go above and beyond to ensure your comfort."
      }
    ];
  </script>
  
  <!-- About Page -->
  <div id="about" class="page">
    <!-- Main Content Section -->
    <section class="pt-32 pb-20 px-6">
      <div class="container mx-auto">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-16 animate-fade-in">About {clinicName}</h1>
        
        <!-- Our Story Section -->
        <div class="flex flex-col lg:flex-row items-center mb-20 gap-12">
          <div class="lg:w-1/2">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Our Story</h2>
            <p class="text-gray-600 mb-4">
              Founded in 2010, {clinicName} has been providing exceptional dental care to our community for over a decade. 
              What started as a small practice with a single dentist has grown into a state-of-the-art dental facility 
              with a team of highly skilled professionals.
            </p>
            <p class="text-gray-600 mb-4">
              Our mission has always been to deliver personalized, compassionate care using the latest dental technologies 
              and techniques. We believe that everyone deserves a healthy, beautiful smile, and we're committed to making 
              that a reality for our patients.
            </p>
            <p class="text-gray-600">
              Over the years, we've built lasting relationships with our patients, many of whom have been with us since 
              the beginning. Their trust and satisfaction drive us to continually improve and excel in all aspects of dental care.
            </p>
          </div>
          <div class="lg:w-1/2 animate-fade-in delay-100">
            <img 
              src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" 
              alt="Dental Clinic" 
              class="rounded-xl shadow-lg w-full hover:shadow-xl transition-shadow duration-300"
            >
          </div>
        </div>
        
        <!-- Meet the Founder Section -->
        <div class="flex flex-col lg:flex-row items-center mb-20 gap-12">
          <div class="lg:w-1/2 order-1 lg:order-2">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Meet Dr. Emily Wilson</h2>
            <p class="text-gray-600 mb-4">
              Dr. Emily Wilson, our lead dentist and founder, graduated with honors from Harvard School of Dental Medicine. 
              With over 15 years of experience in general and cosmetic dentistry, Dr. Wilson is dedicated to providing the 
              highest standard of care.
            </p>
            <p class="text-gray-600 mb-4">
              She regularly attends continuing education courses to stay at the forefront of dental advancements. 
              Her gentle approach and attention to detail have earned her a reputation as one of the top dentists in the region.
            </p>
            <p class="text-gray-600">
              "I founded {clinicName} with the vision of creating a dental practice where patients feel comfortable, 
              informed, and truly cared for. Seeing my patients leave with healthy, confident smiles is the most 
              rewarding part of my job."
            </p>
          </div>
          <div class="lg:w-1/2 order-2 lg:order-1 animate-fade-in delay-200">
            <img 
              src="https://images.unsplash.com/photo-1550831107-1553da8c8464?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1374&q=80" 
              alt="Dentist" 
              class="rounded-xl shadow-lg w-full hover:shadow-xl transition-shadow duration-300"
            >
          </div>
        </div>
        
        <!-- Our Values Section -->
        <div class="bg-gray-50 rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300 mb-20 animate-fade-in">
          <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Our Values</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {#each values as value}
              <div class="text-center p-6 hover:transform hover:-translate-y-2 transition-transform duration-300">
                <div class="bg-sky-100 w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-4">
                  <i class={`fas fa-${value.icon} text-sky-500 text-3xl`}></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">{value.title}</h3>
                <p class="text-gray-600">{value.description}</p>
              </div>
            {/each}
          </div>
        </div>
      </div>
    </section>
    
    <!-- Team Section -->
    <section class="py-20 bg-gray-100">
      <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-16 animate-fade-in">Our Dental Team</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {#each teamMembers as member, i}
            <div 
              class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2 animate-fade-in"
              style={`animation-delay: ${i * 100}ms`}
            >
              <img 
                src={member.image} 
                alt={member.name} 
                class="w-full h-64 object-cover hover:scale-105 transition-transform duration-500"
              >
              <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-1">{member.name}</h3>
                <p class="text-sky-500 mb-4">{member.role}</p>
                <p class="text-gray-600 mb-4">{member.bio}</p>
                <div class="flex space-x-4">
                  <a href="#" class="text-sky-500 hover:text-sky-600 transition-colors">
                    <i class="fab fa-linkedin"></i>
                  </a>
                  <a href="#" class="text-sky-500 hover:text-sky-600 transition-colors">
                    <i class="fas fa-envelope"></i>
                  </a>
                </div>
              </div>
            </div>
          {/each}
        </div>
      </div>
    </section>
    
    <!-- Technology Section -->
    <section class="py-20 bg-white">
      <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-16 animate-fade-in">Our Technology</h2>
        <div class="flex flex-col lg:flex-row items-center gap-12">
          <div class="lg:w-1/2">
            <p class="text-gray-600 mb-8">
              At {clinicName}, we invest in the latest dental technologies to ensure accurate diagnoses, 
              effective treatments, and superior patient comfort.
            </p>
            
            <div class="space-y-6">
              {#each technologies as tech}
                <div class="flex items-start group">
                  <div class="bg-sky-100 p-3 rounded-full mr-4 group-hover:bg-sky-200 transition-colors">
                    <i class={`fas fa-${tech.icon} text-sky-500`}></i>
                  </div>
                  <div>
                    <h4 class="font-bold text-gray-800 mb-1">{tech.title}</h4>
                    <p class="text-gray-600">{tech.description}</p>
                  </div>
                </div>
              {/each}
            </div>
          </div>
          <div class="lg:w-1/2 animate-fade-in delay-100">
            <img 
              src="https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" 
              alt="Dental Technology" 
              class="rounded-xl shadow-lg w-full hover:shadow-xl transition-shadow duration-300"
            >
          </div>
        </div>
      </div>
    </section>
  </div>
  
  <style>
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
  </style>