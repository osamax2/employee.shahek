import axios from 'axios';
import * as Device from 'expo-device';
import Constants from 'expo-constants';
import { Platform } from 'react-native';
import { AuthService } from './AuthService';
import { StorageService } from './StorageService';
import { API_BASE_URL } from './config';

class DeviceServiceClass {
  constructor() {
    this.isRegistered = false;
    this.deviceInfo = null;
  }

  /**
   * Register the device with the server
   */
  async registerDevice() {
    try {
      const deviceId = await StorageService.getDeviceId();
      const deviceInfo = await this.getDeviceInfo();

      const token = await AuthService.getAccessToken();
      if (!token) {
        throw new Error('No access token available for device registration');
      }

      const deviceData = {
        device_id: deviceId,
        device_name: deviceInfo.deviceName,
        device_model: deviceInfo.modelName,
        device_manufacturer: deviceInfo.manufacturer,
        os_name: deviceInfo.osName,
        os_version: deviceInfo.osVersion,
        app_version: deviceInfo.appVersion,
      };

      console.log('Registering device with server...');
      console.log('API URL:', `${API_BASE_URL}/device/register`);
      console.log('Device data:', deviceData);

      const response = await axios.post(
        `${API_BASE_URL}/device/register`,
        deviceData,
        {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json',
          },
          timeout: 10000,
        }
      );

      this.isRegistered = true;
      console.log('Device registered successfully:', response.data);
      return response.data;

    } catch (error) {
      console.error('Device registration error details:');
      console.error('Status:', error.response?.status);
      console.error('Data:', error.response?.data);
      console.error('Message:', error.message);
      
      if (error.response?.status === 401) {
        // Token expired, try to refresh
        try {
          await AuthService.refreshAccessToken();
          // Retry device registration
          return await this.registerDevice();
        } catch (refreshError) {
          console.error('Token refresh failed during device registration:', refreshError);
          throw new Error('Authentication failed - please restart the app');
        }
      }
      
      if (error.code === 'ECONNABORTED') {
        throw new Error('Connection timeout - please check your internet connection');
      }
      
      if (error.message.includes('Network Error')) {
        throw new Error('Network error - please check API_BASE_URL in config');
      }
      
      throw new Error(error.response?.data?.message || 'Failed to register device with server');
    }
  }

  /**
   * Send heartbeat to update device status
   */
  async sendHeartbeat() {
    try {
      const deviceId = await StorageService.getDeviceId();
      const token = await AuthService.getAccessToken();

      if (!token) {
        console.warn('No access token available for heartbeat');
        return;
      }

      await axios.post(
        `${API_BASE_URL}/device/heartbeat`,
        { device_id: deviceId },
        {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json',
          },
          timeout: 5000,
        }
      );

      console.log('Heartbeat sent successfully');
    } catch (error) {
      console.error('Heartbeat error:', error.message);
      // Don't throw error for heartbeat failures
    }
  }

  /**
   * Get detailed device information
   */
  async getDeviceInfo() {
    if (this.deviceInfo) {
      return this.deviceInfo;
    }
    
    this.deviceInfo = {
      deviceName: Device.deviceName || 'Unknown Device',
      modelName: Device.modelName || 'Unknown Model',
      manufacturer: Device.manufacturer || 'Unknown',
      osName: Device.osName || Platform.OS,
      osVersion: Device.osVersion || 'Unknown',
      appVersion: Constants.expoConfig?.version || '1.0.0',
      brand: Device.brand || 'Unknown',
      designName: Device.designName || 'Unknown',
      productName: Device.productName || 'Unknown',
      isDevice: Device.isDevice,
    };
    
    return this.deviceInfo;
  }

  /**
   * Check if device is registered
   */
  async isDeviceRegistered() {
    try {
      const token = await AuthService.getAccessToken();
      if (!token) {
        return false;
      }

      const response = await axios.get(
        `${API_BASE_URL}/device/me`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json',
          },
          timeout: 5000,
        }
      );

      return response.data.success && response.data.devices.length > 0;
    } catch (error) {
      console.error('Check device registration error:', error.message);
      return false;
    }
  }

  /**
   * Start periodic heartbeat
   */
  startHeartbeat(intervalMs = 300000) { // 5 minutes default
    setInterval(() => {
      this.sendHeartbeat();
    }, intervalMs);
  }
}

export const DeviceService = new DeviceServiceClass();
