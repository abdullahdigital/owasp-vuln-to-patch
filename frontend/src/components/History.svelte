<script>
    import { onMount } from 'svelte';
    import { get } from 'svelte/store';
    import { user, logout, token } from '../stores/authStore';
    import { location, push } from 'svelte-spa-router';

    let displayedAppointments = [];
    let isLoading = true;
    let errorMessage = '';
    let currentLoggedInUserId = null;

    let unsubscribeUser = null;
    let unsubscribeLocation = null;

    function handleLogout() {
        logout();
        push('/login');
    }

    function extractIdFromHash() {
        const hash = window.location.hash; // e.g. "#/history?id=101"
        const queryStringIndex = hash.indexOf('?');
        if (queryStringIndex === -1) return null;

        const queryString = hash.substring(queryStringIndex); // "?id=101"
        const urlParams = new URLSearchParams(queryString);
        return urlParams.get('id');
    }

    function subscribeToUser() {
        if (unsubscribeUser) unsubscribeUser();
        unsubscribeUser = user.subscribe(value => {
            currentLoggedInUserId = value ? value.id : null;
            if (!value) {
                displayedAppointments = [];
                errorMessage = 'Please log in to view your appointment history.';
                isLoading = false;
            } else {
                fetchAppointments();
            }
        });
    }

    async function fetchAppointments() {
        isLoading = true;
        errorMessage = '';
        displayedAppointments = [];

        const currentUser = get(user);
        const authToken = get(token);
        const requestedId = extractIdFromHash();

        console.log('fetchAppointments called.');
        console.log('currentUser:', currentUser);
        console.log('Token:', authToken ? 'Yes' : 'No');
        console.log('Requested ID from hash:', requestedId);

        if (!currentUser || !authToken) {
            errorMessage = 'Please log in to view your appointment history.';
            isLoading = false;
            return;
        }

        let apiUrl = `http://localhost:8000/history.php`;
        if (requestedId && !isNaN(parseInt(requestedId))) {
            apiUrl += `?id=${parseInt(requestedId)}`;
            console.log(`Fetching specific appointment with ID ${requestedId}`);
        } else {
            console.log('Fetching all appointments for user');
        }

        try {
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                }
            });

            const result = await response.json();
            console.log('API result:', result);

            if (!response.ok) {
                errorMessage = result.message || 'Failed to load appointments.';
                if (response.status === 401) {
                    logout();
                    push('/login');
                }
            } else {
                if (requestedId) {
                    displayedAppointments = result.appointment ? [result.appointment] : [];
                    if (displayedAppointments.length === 0) {
                        errorMessage = `Appointment with ID ${requestedId} not found.`;
                    }
                } else {
                    displayedAppointments = result.appointments || [];
                    if (displayedAppointments.length === 0) {
                        errorMessage = 'No appointments found for your account.';
                    }
                }
            }
        } catch (err) {
            console.error('Error fetching:', err);
            errorMessage = 'Could not connect to the server.';
        } finally {
            isLoading = false;
        }
    }

    onMount(() => {
        subscribeToUser();

        unsubscribeLocation = location.subscribe(() => {
            fetchAppointments();
        });

        fetchAppointments();

        return () => {
            if (unsubscribeUser) unsubscribeUser();
            if (unsubscribeLocation) unsubscribeLocation();
        };
    });
</script>



<section class="max-w-4xl mx-auto p-6 bg-white rounded shadow-md mt-8 font-poppins">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-sky-600">Appointment History</h1>
        {#if $user}
            <!-- This logout button is local to History.svelte, Navbar will also have one -->
            <button on:click={handleLogout} class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                Logout ({$user.email})
            </button>
        {/if}
    </div>

    <hr class="my-6 border-t border-gray-300" />

    {#if !($user)}
        <p class="text-red-500 text-lg text-center my-8">
            Please <a href="/#/login" class="underline font-bold text-sky-700 hover:text-sky-800">log in</a> to view your appointment history.
        </p>
    {:else if isLoading}
        <p class="text-gray-600 text-center my-8">Loading appointments...</p>
    {:else if errorMessage}
        <p class="p-4 bg-red-100 text-red-700 rounded border border-red-400 text-center my-8">
            {errorMessage}
        </p>
    {:else if displayedAppointments.length > 0}
        <h2 class="text-2xl font-semibold mb-4 text-sky-700">
            {#if new URLSearchParams(get(location).search).get('id')}
                Requested Appointment Details
            {:else}
                Your Appointments
            {/if}
            {#if currentLoggedInUserId}
                (Logged in as User ID: {currentLoggedInUserId})
            {/if}
        </h2>

        <p class="mb-4 text-gray-600">
            {#if new URLSearchParams(get(location).search).get('id')}
                You are currently viewing a specific appointment.
                <button on:click={() => push('/history')} class="ml-2 px-3 py-1 bg-blue-500 text-white rounded-md text-sm hover:bg-blue-600 transition">
                    Back to My Appointments
                </button>
            {:else}
                These are the appointments associated with your account.
            {/if}
        </p>

        <ul class="space-y-4 mb-8">
            {#each displayedAppointments as appt (appt.id)}
                <li class="p-4 bg-sky-50 rounded border border-sky-200">
                    <p><strong>Appointment ID:</strong> {appt.id}</p>
                    <p><strong>Date:</strong> {appt.date}</p>
                    <p><strong>Time:</strong> {appt.time}</p>
                    <p><strong>Doctor:</strong> {appt.doctor}</p>
                    <p><strong>Notes:</strong> {appt.notes}</p>

                    <h4 class="font-bold text-md text-gray-700 mt-3">Detailed Information:</h4>
                    <p><strong>Internal Billing Code:</strong> <span class="font-mono text-gray-700">{appt.internal_billing_code}</span></p>
                    <p><strong>Patient Private Notes:</strong> <span class="font-mono text-gray-700">{appt.patient_private_notes}</span></p>
                    <p><strong>Doctor Private Comment:</strong> <span class="font-mono text-gray-700">{appt.doctor_private_comment}</span></p>

                    {#if appt.user_id !== currentLoggedInUserId}
                        <div class="mt-4 p-3 bg-red-100 rounded border border-red-300 text-red-700 font-bold">
                            WARNING: This appointment (ID: {appt.id}) belongs to another user (User ID: {appt.user_id}).
                            This demonstrates an Insecure Direct Object Reference (IDOR) vulnerability!
                        </div>
                    {/if}
                </li>
            {:else}
                <!-- This block will render if displayedAppointments is empty but not loading/error -->
                <p class="text-gray-500 text-center my-8">No appointments found for your account.</p>
            {/each}
        </ul>
    {:else}
        <!-- This handles the case where there are no appointments AND not loading/error -->
        <p class="text-gray-500 text-center my-8">No appointments found for your account.</p>
    {/if}


    {#if $user && !(new URLSearchParams(get(location).search).get('id'))}
        <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded">
            <h2 class="text-xl font-bold text-yellow-700">IDOR Vulnerability Demonstration</h2>
            <p class="mt-2">
                As a logged-in user, you can try to access another user's appointment
                by manually changing the URL in your browser to include an `id` parameter.
            </p>
            <p class="mt-2">
                For example, if you are logged in as `test@test.com` (User ID 2),
                try navigating to:
            </p>
            <code class="block mt-2 p-2 bg-gray-100 rounded break-all">
                http://localhost:5173/#/history?id=301
            </code>
            <p class="mt-2 text-sm">
                (This would attempt to fetch an appointment belonging to `patient2@clinic.com`, User ID 3).
                You might also try `id=501` for the admin's sensitive appointment.
            </p>
        </div>
    {/if}
</section>

<style>
    .font-poppins {
        font-family: 'Poppins', sans-serif;
    }
</style>
