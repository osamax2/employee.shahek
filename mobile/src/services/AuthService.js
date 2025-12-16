import axios from 'axios';
import * as SecureStore from 'expo-secure-store';
import { API_BASE_URL } from './config';

class AuthServiceClass {
  constructor() {
    this.accessToken = null;
    this.refreshToken = null;
  }

  async login(email, password, deviceName = null) {
    try {
      const loginData = {
        email,
        password,
      };
      
      // Include device name if provided for auto-registration
      if (deviceName) {
        loginData.device_name = deviceName;
      }
      
      console.log('Attempting login to:', API_BASE_URL + '/auth/login');
      console.log('Login data:', { email, device_name: deviceName });
      
      const response = await axios.post(`${API_BASE_URL}/auth/login`, loginData, {
        timeout: 15000, // 15 seconds
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      });

      console.log('Login response status:', response.status);
      console.log('Login response data:', response.data);

      const { access_token, refresh_token, employee } = response.data;

      if (!access_token) {
        throw new Error('No access token received from server');
      }

      // Store tokens securely
      await SecureStore.setItemAsync('access_token', access_token);
      await SecureStore.setItemAsync('refresh_token', refresh_token);
      await SecureStore.setItemAsync('employee', JSON.stringify(employee));

      this.accessToken = access_token;
      this.refreshToken = refresh_token;

      console.log('Login successful for employee:', employee.name);
      return employee;
    } catch (error) {
      console.error('Login error details:');
      console.error('Error message:', error.message);
      console.error('Response status:', error.response?.status);
      console.error('Response data:', error.response?.data);
      console.error('Request config:', error.config?.url);
      
      // Specific error messages
      if (error.code === 'ECONNABORTED') {
        throw new Error('Connection timeout. Please check your internet connection.');
      }
      
      if (error.message.includes('Network Error')) {
        throw new Error('Network error. Please check your internet connection and API URL.');
      }
      
      if (error.response) {
        const status = error.response.status;
        const message = error.response.data?.message || error.message;
        
        if (status === 401) {
          throw new Error('Invalid credentials: ' + message);
        } else if (status === 422) {
          throw new Error('Validation error: ' + message);
        } else if (status >= 500) {
          throw new Error('Server error. Please try again later.');
        }
        
        throw new Error(message);
      }
      
      throw new Error('Login failed: ' + error.message);
    }
  }

  async refreshAccessToken() {
    try {
      let refreshToken;
      try {
        refreshToken = await SecureStore.getItemAsync('refresh_token');
      } catch (decryptError) {
        console.error('Failed to decrypt refresh token, clearing:', decryptError.message);
        await SecureStore.deleteItemAsync('refresh_token');
        throw new Error('Token decryption failed, please login again');
      }
      
      if (!refreshToken) {
        throw new Error('No refresh token available');
      }

      const response = await axios.post(`${API_BASE_URL}/auth/refresh`, {
        refresh_token: refreshToken,
      });

      const { access_token } = response.data;

      await SecureStore.setItemAsync('access_token', access_token);
      this.accessToken = access_token;

      return access_token;
    } catch (error) {
      console.error('Token refresh error:', error);
      // If refresh fails, user needs to login again
      await this.logout();
      throw error;
    }
  }

  async getAccessToken() {
    if (this.accessToken) {
      return this.accessToken;
    }

    try {
      this.accessToken = await SecureStore.getItemAsync('access_token');
    } catch (decryptError) {
      console.error('Failed to decrypt access token, clearing:', decryptError.message);
      await SecureStore.deleteItemAsync('access_token');
      this.accessToken = null;
    }
    return this.accessToken;
  }

  async isAuthenticated() {
    const token = await this.getAccessToken();
    return !!token;
  }

  async logout() {
    await SecureStore.deleteItemAsync('access_token');
    await SecureStore.deleteItemAsync('refresh_token');
    await SecureStore.deleteItemAsync('employee');
    this.accessToken = null;
    this.refreshToken = null;
  }

  async getEmployee() {
    try {
      const employeeJson = await SecureStore.getItemAsync('employee');
      return employeeJson ? JSON.parse(employeeJson) : null;
    } catch (decryptError) {
      console.error('Failed to decrypt employee data, clearing:', decryptError.message);
      await SecureStore.deleteItemAsync('employee');
      return null;
    }
  }
}

export const AuthService = new AuthServiceClass();
