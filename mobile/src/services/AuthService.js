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
      
      const response = await axios.post(`${API_BASE_URL}/auth/login`, loginData);

      const { access_token, refresh_token, employee } = response.data;

      // Store tokens securely
      await SecureStore.setItemAsync('access_token', access_token);
      await SecureStore.setItemAsync('refresh_token', refresh_token);
      await SecureStore.setItemAsync('employee', JSON.stringify(employee));

      this.accessToken = access_token;
      this.refreshToken = refresh_token;

      return employee;
    } catch (error) {
      console.error('Login error:', error.response?.data || error.message);
      throw new Error(error.response?.data?.message || 'Login failed');
    }
  }

  async refreshAccessToken() {
    try {
      const refreshToken = await SecureStore.getItemAsync('refresh_token');
      
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

    this.accessToken = await SecureStore.getItemAsync('access_token');
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
    const employeeJson = await SecureStore.getItemAsync('employee');
    return employeeJson ? JSON.parse(employeeJson) : null;
  }
}

export const AuthService = new AuthServiceClass();
