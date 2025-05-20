<script>
    import { onMount } from 'svelte';
  
    // Appointment history data (safe)
    let appointments = [
      {
        id: 1,
        date: '2025-05-01',
        time: '10:00 AM',
        doctor: 'Dr. Smith',
        notes: 'Regular checkup',
      },
      {
        id: 2,
        date: '2025-06-10',
        time: '02:30 PM',
        doctor: 'Dr. Lee',
        notes: 'Follow-up appointment',
      },
    ];
  
    // Insecure serialized data string (vulnerable)
    let insecureSerialized = `{
      "date": "2025-07-15",
      "time": "11:00 AM",
      "doctor": "Dr. Evil",
      "notes": "Deserialized appointment"
    }`;
  
    // Unsafe deserialization
    let insecureAppointment;
  
    onMount(() => {
      // Unsafe eval deserialization
      insecureAppointment = eval('(' + insecureSerialized + ')');
    });
  </script>
  
  <section class="max-w-4xl mx-auto p-6 bg-white rounded shadow-md mt-8 font-poppins">
    <h1 class="text-3xl font-bold mb-6 text-sky-600">Appointment History</h1>
  
    <!-- User's booked appointments -->
    <div>
      <ul class="space-y-4">
        {#each appointments as appt}
          <li class="p-4 bg-sky-50 rounded border border-sky-200">
            <p><strong>Date:</strong> {appt.date}</p>
            <p><strong>Time:</strong> {appt.time}</p>
            <p><strong>Doctor:</strong> {appt.doctor}</p>
            <p><strong>Notes:</strong> {appt.notes}</p>
          </li>
        {/each}
      </ul>
    </div>
  
    <!-- Insecure deserialized appointment -->
    {#if insecureAppointment}
      <div class="mt-8 p-4 bg-yellow-50 border border-yellow-300 rounded">
        <p><strong>Date:</strong> {insecureAppointment.date}</p>
        <p><strong>Time:</strong> {insecureAppointment.time}</p>
        <p><strong>Doctor:</strong> {insecureAppointment.doctor}</p>
        <p><strong>Notes:</strong> {insecureAppointment.notes}</p>
      </div>
    {/if}
  </section>
  
  <style>
    .font-poppins {
      font-family: 'Poppins', sans-serif;
    }
  </style>
  