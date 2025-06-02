<script>
  import { onMount } from 'svelte';

  // Dummy data for appointments & users
  let appointments = [
    {
      id: 1,
      patient: 'Sarah Johnson',
      email: 'sarah@example.com',
      date: '2025-05-15',
      time: '10:00 AM',
      service: 'Teeth Cleaning',
      status: 'Confirmed',
      avatar: 'https://randomuser.me/api/portraits/women/43.jpg',
    },
    {
      id: 2,
      patient: 'Michael Chen',
      email: 'michael@example.com',
      date: '2025-05-16',
      time: '2:30 PM',
      service: 'Dental Implant',
      status: 'Pending',
      avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
    },
  ];

  let users = [
    { id: 1, name: 'Sarah Johnson', email: 'sarah@example.com', role: 'patient' },
    { id: 2, name: 'Michael Chen', email: 'michael@example.com', role: 'patient' },
    { id: 3, name: 'Admin User', email: 'admin@example.com', role: 'admin' },
  ];

  // Vulnerable action logs (Insufficient Logging & Monitoring demo)
  let logs = [];

  function logAction(action) {
    const timestamp = new Date().toISOString();
    logs = [...logs, { action, timestamp }];
  }

  // Edit and delete appointment demo functions (Broken Access Control if not protected)
  function deleteAppointment(id) {
    appointments = appointments.filter(a => a.id !== id);
    logAction(`Deleted appointment ID ${id}`);
  }

  function confirmAppointment(id) {
    appointments = appointments.map(a =>
      a.id === id ? { ...a, status: 'Confirmed' } : a
    );
    logAction(`Confirmed appointment ID ${id}`);
  }

  onMount(async () => {
    // Fetch logs from backend (your existing code)
    const res = await fetch('http://localhost:8000/logs.php?fetch_logs=true');
    const data = await res.json();
    logs = data.map(l => ({
      timestamp: l.timestamp,
      action: `${l.action} (IP: ${l.ip})`
    }));

    // New: Log XSS from URL parameter (if any)
    const urlParams = new URLSearchParams(window.location.search);
    const xssPayload = urlParams.get('xss');
    if (xssPayload) {
      // Log the XSS payload as is, demonstrating vulnerable logging
      logAction(`XSS payload logged: ${xssPayload}`);
    }
  });
</script>


<section class="pt-20 pb-20 px-6 bg-gray-50 min-h-screen font-poppins">
  <div class="container mx-auto max-w-7xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Admin Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center">
          <div class="bg-sky-100 p-3 rounded-full mr-4">
            <i class="fas fa-calendar-check text-sky-500 text-xl"></i>
          </div>
          <div>
            <p class="text-gray-500">Today's Appointments</p>
            <h3 class="text-2xl font-bold text-gray-800">
              {appointments.filter(a => a.date === new Date().toISOString().slice(0,10)).length}
            </h3>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center">
          <div class="bg-green-100 p-3 rounded-full mr-4">
            <i class="fas fa-users text-green-500 text-xl"></i>
          </div>
          <div>
            <p class="text-gray-500">Total Patients</p>
            <h3 class="text-2xl font-bold text-gray-800">{users.length}</h3>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center">
          <div class="bg-purple-100 p-3 rounded-full mr-4">
            <i class="fas fa-dollar-sign text-purple-500 text-xl"></i>
          </div>
          <div>
            <p class="text-gray-500">Monthly Revenue</p>
            <h3 class="text-2xl font-bold text-gray-800">$24,560</h3>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center">
          <div class="bg-yellow-100 p-3 rounded-full mr-4">
            <i class="fas fa-clock text-yellow-500 text-xl"></i>
          </div>
          <div>
            <p class="text-gray-500">Pending Appointments</p>
            <h3 class="text-2xl font-bold text-gray-800">
              {appointments.filter(a => a.status === 'Pending').length}
            </h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Appointments Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
      <div class="p-6 border-b">
        <h2 class="text-xl font-bold text-gray-800">Recent Appointments</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            {#each appointments as appt (appt.id)}
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img class="h-10 w-10 rounded-full" src={appt.avatar} alt={appt.patient} />
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{appt.patient}</div>
                      <div class="text-sm text-gray-500">{appt.email}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{appt.date}</div>
                  <div class="text-sm text-gray-500">{appt.time}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{appt.service}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                    appt.status === 'Confirmed'
                      ? 'bg-green-100 text-green-800'
                      : 'bg-yellow-100 text-yellow-800'}`}>
                    {appt.status}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                  {#if appt.status !== 'Confirmed'}
                    <button on:click={() => confirmAppointment(appt.id)}
                      class="text-green-600 hover:text-green-900">Confirm</button>
                  {/if}
                  <button on:click={() => deleteAppointment(appt.id)}
                    class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
    </div>

    <!-- Users List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="p-6 border-b">
        <h2 class="text-xl font-bold text-gray-800">Registered Users</h2>
      </div>
      <ul class="divide-y divide-gray-200">
        {#each users as user}
          <li class="p-4 flex justify-between items-center">
            <div>
              <p class="text-gray-900 font-medium">{user.name}</p>
              <p class="text-gray-500 text-sm">{user.email}</p>
            </div>
            <span class="px-3 py-1 text-sm rounded-full
              {user.role === 'admin' ? 'bg-sky-100 text-sky-800' : 'bg-gray-100 text-gray-600'}">
              {user.role}
            </span>
          </li>
        {/each}
      </ul>
    </div>

    <!-- Logs (Demonstration of Insufficient Logging & Monitoring) -->
    <div class="mt-10 bg-white rounded-xl shadow p-6">
      <h3 class="text-lg font-bold mb-4 text-gray-700">Action Logs</h3>
      {#if logs.length === 0}
        <p class="text-gray-500 italic">No actions performed yet.</p>
      {:else}
        <ul class="text-sm text-gray-600 space-y-1 max-h-40 overflow-y-auto">
          {#each logs as log}
            <li>
              <span class="font-mono">{log.timestamp}</span> - {log.action}
            </li>
          {/each}
        </ul>
      {/if}
    </div>
  </div>
</section>