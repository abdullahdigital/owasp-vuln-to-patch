<script>
    import { onMount } from 'svelte';
    import { get } from 'svelte/store';
    import { user, logout, token } from '../stores/authStore';
    import { location, push } from 'svelte-spa-router';

    let displayedAppointments = []; // Will hold either the user's list or a single IDOR-accessed appointment
    let isLoading = true;
    let errorMessage = '';

    // Reactive statement: Recalculate currentLoggedInUserId whenever the 'user' store changes
    let currentLoggedInUserId = null;
    user.subscribe(value => {
        currentLoggedInUserId = value ? value.id : null;
        // If the user logs out, clear displayed data and show a login message
        if (!value) {
            displayedAppointments = [];
            errorMessage = 'Please log in to view your appointment history.';
            isLoading = false; // Stop loading state when logged out
        } else {
            // If user logs in or user data changes, re-fetch appointments
            fetchAppointments();
        }
    });

    /**
     * Fetches appointment data from the backend.
     * This function handles both fetching the current user's appointments
     * and fetching a specific appointment via the IDOR vulnerability.
     */
    async function fetchAppointments() {
        isLoading = true;
        errorMessage = '';
        displayedAppointments = []; // Clear previous data before fetching

        const currentUser = get(user);
        const authToken = get(token);
        const currentUrlLocation = get(location); // Get current URL and query parameters

        // If no user or token, ensure the "Please log in" message is shown.
        if (!currentUser || !authToken) {
            errorMessage = 'Please log in to view your appointment history.';
            isLoading = false;
            return; // Exit the function if not logged in
        }

        const urlParams = new URLSearchParams(currentUrlLocation.search);
        const requestedId = urlParams.get('id'); // Check for the 'id' parameter in the URL

        let apiUrl = `http://localhost:8000/history.php`;
        if (requestedId && !isNaN(parseInt(requestedId, 10))) {
            // If an 'id' is present and valid, request that specific appointment
            apiUrl += `?id=${parseInt(requestedId, 10)}`;
        }
        // If no 'id' parameter, the backend (history.php) will default to
        // returning appointments for the current authenticated user.

        try {
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}` // Always send the user's token
                }
            });

            const result = await response.json();

            if (!response.ok) {
                // Handle API errors (e.g., 401 Unauthorized, 404 Not Found, 500 Server Error)
                errorMessage = result.message || 'Failed to load appointments.';
                if (response.status === 401) {
                    // If the token is invalid or expired, force logout and redirect
                    logout();
                    push('/login');
                }
            } else {
                // API call was successful
                if (requestedId) {
                    // If a single appointment was requested by ID
                    displayedAppointments = result.appointment ? [result.appointment] : [];
                    if (displayedAppointments.length === 0) {
                        errorMessage = 'Appointment not found or you do not have access (though IDOR is enabled).';
                    }
                } else {
                    // Otherwise, display the list of appointments for the current user
                    displayedAppointments = result.appointments || [];
                    if (displayedAppointments.length === 0) {
                        errorMessage = 'No appointments found for your user account.';
                    }
                }
            }
        } catch (error) {
            console.error('Network or fetch error:', error);
            errorMessage = 'Could not connect to the server. Please check your connection.';
        } finally {
            isLoading = false;
        }
    }

    onMount(() => {
        // Subscribe to changes in the URL location (e.g., when 'id' parameter is added/removed)
        const unsubscribeLocation = location.subscribe(() => {
            fetchAppointments(); // Re-fetch data whenever the URL changes
        });

        // Perform the initial fetch when the component first mounts
        fetchAppointments();

        // Return a cleanup function for when the component is unmounted
        return () => {
            unsubscribeLocation();
        };
    });

    // Logout handler
    function handleLogout() {
        logout(); // Clear user and token from Svelte store and localStorage
        push('/login'); // Redirect to the login page
    }
</script>

<section class="max-w-4xl mx-auto p-6 bg-white rounded shadow-md mt-8 font-poppins">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-sky-600">Appointment History</h1>
        {#if $user}
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
            {/each}
        </ul>
    {:else}
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
                http://localhost:5173/#/history?id=3
            </code>
            <p class="mt-2 text-sm">
                (This would attempt to fetch an appointment belonging to `patient2@clinic.com`, User ID 3).
                You might also try `id=5` for the admin's sensitive appointment.
            </p>
        </div>
    {/if}
</section>

<style>
    .font-poppins {
        font-family: 'Poppins', sans-serif;
    }
</style>