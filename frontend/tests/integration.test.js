// frontend/tests/integration.test.js
import { login, register, logout, checkAuth } from '../src/stores/authStore';
import { user } from '../src/stores/authStore';

describe('Authentication Integration Tests', () => {
  const testUser = {
    name: 'Test User',
    email: `test${Date.now()}@example.com`,
    password: 'password123'
  };

  afterEach(async () => {
    // Clean up by logging out after each test
    await logout();
  });

  test('should register a new user', async () => {
    const response = await register(testUser.name, testUser.email, testUser.password);
    expect(response).toHaveProperty('email', testUser.email);
    expect(response).toHaveProperty('token');
  });

  test('should login with registered user', async () => {
    await register(testUser.name, testUser.email, testUser.password);
    await logout();
    
    const response = await login(testUser.email, testUser.password);
    expect(response).toHaveProperty('email', testUser.email);
    expect(response).toHaveProperty('token');
  });

  test('should check authentication status', async () => {
    await register(testUser.name, testUser.email, testUser.password);
    
    const response = await checkAuth();
    expect(response).toHaveProperty('email', testUser.email);
    expect(response).toHaveProperty('token');
  });

  test('should logout successfully', async () => {
    await register(testUser.name, testUser.email, testUser.password);
    await logout();
    
    const authStatus = await checkAuth();
    expect(authStatus).toBeNull();
  });
});

describe('Appointment Integration Tests', () => {
  const testUser = {
    name: 'Appointment Test',
    email: `appt${Date.now()}@example.com`,
    password: 'password123'
  };
  
  beforeAll(async () => {
    await register(testUser.name, testUser.email, testUser.password);
  });
  
  afterAll(async () => {
    await logout();
  });
  
  test('should fetch available services', async () => {
    const response = await fetch('http://localhost:8000/api/services');
    expect(response.ok).toBeTruthy();
    
    const services = await response.json();
    expect(Array.isArray(services)).toBeTruthy();
    expect(services.length).toBeGreaterThan(0);
  });
  
  test('should create a new appointment', async () => {
    const appointmentData = {
      email: testUser.email,
      phone: '1234567890',
      service: '1',
      date: '2025-01-01',
      time: '10:00',
      notes: 'Test appointment'
    };
    
    const response = await fetch('http://localhost:8000/api/appointments', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        ...getAuthHeaders()
      },
      body: JSON.stringify(appointmentData)
    });
    
    expect(response.ok).toBeTruthy();
    
    const appointment = await response.json();
    expect(appointment).toHaveProperty('status', 'success');
    expect(appointment.appointment).toHaveProperty('email', appointmentData.email);
  });
});