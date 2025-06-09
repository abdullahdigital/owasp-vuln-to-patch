<script>
  import { push } from 'svelte-spa-router'

  // Function to navigate to appointment page
  function bookAppointment() {
    push('/appointment')
  }

  // Local image imports
  import generalDentistry from '../img/dental_services/general-dentistry.webp';
  import cosmeticDentistry from '../img/dental_services/emergency-dentistry.webp';
  import orthodontics from '../img/dental_services/orthodontics.webp';
  import dentalImplants from '../img/dental_services/dental-implants.webp';
  import pediatricDentistry from '../img/dental_services/pediatric-dentistry.webp';
</script>

<section id="services" class="pt-22 pb-0 px-6 bg-gray-50">
  <div class="container mx-auto">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-4">Our Dental Services</h1>
    <p class="text-xl text-center text-gray-600 max-w-3xl mx-auto mb-16">
      Comprehensive dental care tailored to your unique needs and goals.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">

      <!-- Service Card 1 -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
        <img src={generalDentistry} alt="General Dentistry" class="w-full h-48 object-cover" />
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div class="bg-sky-100 p-3 rounded-full mr-4">
              <i class="fas fa-tooth text-sky-500"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">General Dentistry</h3>
          </div>
          <p class="text-gray-600 mb-4">
            Comprehensive care for patients of all ages, including exams, cleanings, fillings, and more.
          </p>
          <ul class="space-y-2 mb-6">
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Dental exams & cleanings</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Fillings & restorations</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Root canal therapy</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Gum disease treatment</li>
          </ul>
          <button class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-2 px-4 rounded transition" on:click={bookAppointment}>
            Book Appointment
          </button>
        </div>
      </div>

      <!-- Service Card 2 -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
        <img src={cosmeticDentistry} alt="Cosmetic Dentistry" class="w-full h-48 object-cover" />
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div class="bg-sky-100 p-3 rounded-full mr-4">
              <i class="fas fa-star text-sky-500"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">Emergency Dentistry</h3>
          </div>
          <p class="text-gray-600 mb-4">
            Get fast relief from dental emergencies like pain, injury, or broken teeth.
          </p>
          <ul class="space-y-2 mb-6">
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Toothache relief</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Knocked-out teeth</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Cracked or chipped teeth</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Lost fillings or crowns</li>
          </ul>
          <button class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-2 px-4 rounded transition" on:click={bookAppointment}>
            Book Appointment
          </button>
        </div>
      </div>

      <!-- Service Card 3 -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
        <img src={orthodontics} alt="Orthodontics" class="w-full h-48 object-cover" />
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div class="bg-sky-100 p-3 rounded-full mr-4">
              <i class="fas fa-teeth text-sky-500"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">Orthodontics</h3>
          </div>
          <p class="text-gray-600 mb-4">
            Straighten your teeth and correct your bite with our orthodontic solutions.
          </p>
          <ul class="space-y-2 mb-6">
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Traditional braces</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Invisalign clear aligners</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Retainers</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Early interceptive treatment</li>
          </ul>
          <button class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-2 px-4 rounded transition" on:click={bookAppointment}>
            Book Appointment
          </button>
        </div>
      </div>

      <!-- Service Card 4 -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
        <img src={dentalImplants} alt="Dental Implants" class="w-full h-48 object-cover" />
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div class="bg-sky-100 p-3 rounded-full mr-4">
              <i class="fas fa-implant text-sky-500"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">Dental Implants</h3>
          </div>
          <p class="text-gray-600 mb-4">
            Permanent solution for missing teeth that look, feel, and function like natural teeth.
          </p>
          <ul class="space-y-2 mb-6">
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Single tooth implants</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Implant-supported bridges</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> All-on-4 full arch restoration</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Bone grafting</li>
          </ul>
          <button class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-2 px-4 rounded transition" on:click={bookAppointment}>
            Book Appointment
          </button>
        </div>
      </div>

      <!-- Service Card 5 -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
        <img src={pediatricDentistry} alt="Pediatric Dentistry" class="w-full h-48 object-cover" />
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div class="bg-sky-100 p-3 rounded-full mr-4">
              <i class="fas fa-baby text-sky-500"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">Pediatric Dentistry</h3>
          </div>
          <p class="text-gray-600 mb-4">
            Gentle and fun dental care for kids to keep their smiles healthy and bright.
          </p>
          <ul class="space-y-2 mb-6">
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Child-friendly exams</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Preventive cleanings</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Fluoride treatments</li>
            <li class="flex items-center text-gray-600"><i class="fas fa-check text-sky-500 mr-2"></i> Sealants</li>
          </ul>
          <button class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-2 px-4 rounded transition" on:click={bookAppointment}>
            Book Appointment
          </button>
        </div>
      </div>

    </div>
  </div>
</section>
