import * as Location from 'expo-location';
import * as Battery from 'expo-battery';
import * as Device from 'expo-device';
import * as Network from 'expo-network';
import axios from 'axios';
import { AuthService } from './AuthService';
import { StorageService } from './StorageService';
import { API_BASE_URL } from './config';

class LocationServiceClass {
  constructor() {
    this.retryQueue = [];
    this.isProcessingQueue = false;
    this.retryDelay = 5000; // Start with 5 seconds
    this.maxRetryDelay = 300000; // Max 5 minutes
  }

  async sendCurrentLocation() {
    try {
      const location = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.High,
      });

      await this.sendLocation(location);
    } catch (error) {
      console.error('Error getting current location:', error);
    }
  }

  async processBackgroundLocations(locations) {
    for (const location of locations) {
      await this.sendLocation(location);
    }
  }

  async sendLocation(location) {
    try {
      const { latitude, longitude, accuracy, speed, heading } = location.coords;
      const timestamp = new Date(location.timestamp).toISOString();

      // Get additional device info
      const batteryLevel = await this.getBatteryLevel();
      const deviceInfo = await this.getDeviceInfo();

      const locationData = {
        lat: latitude,
        lng: longitude,
        accuracy: accuracy || 0,
        timestamp,
        speed: speed || null,
        heading: heading || null,
        battery: batteryLevel,
        device_os: deviceInfo.os,
        device_version: deviceInfo.version,
      };

      // Check network connectivity
      const networkState = await Network.getNetworkStateAsync();
      
      if (!networkState.isConnected || !networkState.isInternetReachable) {
        // Offline - cache the location
        await this.cacheLocation(locationData);
        console.log('Location cached (offline)');
        return;
      }

      // Try to send the location
      await this.sendLocationToServer(locationData);

      // If successful, try to process any queued locations
      if (this.retryQueue.length > 0 && !this.isProcessingQueue) {
        this.processRetryQueue();
      }

    } catch (error) {
      console.error('Error sending location:', error);
      // Cache the location for retry
      await this.cacheLocation(location);
    }
  }

  async sendLocationToServer(locationData) {
    try {
      const token = await AuthService.getAccessToken();

      if (!token) {
        throw new Error('No access token available');
      }

      // Log the data we're sending for debugging
      console.log('Sending location data:', JSON.stringify(locationData, null, 2));

      const response = await axios.post(
        `${API_BASE_URL}/location`,
        locationData,
        {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json',
          },
          timeout: 10000, // 10 second timeout
        }
      );

      console.log('Location sent successfully:', response.data);
      return response.data;

    } catch (error) {
      // Log detailed error information
      if (error.response) {
        console.error('Location POST failed:', {
          status: error.response.status,
          data: error.response.data,
          headers: error.response.headers
        });
      }
      
      if (error.response?.status === 401) {
        // Token expired, try to refresh
        try {
          await AuthService.refreshAccessToken();
          // Retry sending location
          return await this.sendLocationToServer(locationData);
        } catch (refreshError) {
          console.error('Token refresh failed:', refreshError);
          throw refreshError;
        }
      }
      throw error;
    }
  }

  async cacheLocation(locationData) {
    try {
      await StorageService.addToQueue('location_queue', locationData);
      this.retryQueue.push(locationData);
    } catch (error) {
      console.error('Error caching location:', error);
    }
  }

  async processRetryQueue() {
    if (this.isProcessingQueue || this.retryQueue.length === 0) {
      return;
    }

    this.isProcessingQueue = true;

    try {
      // Get queued items from storage
      const queue = await StorageService.getQueue('location_queue');
      
      if (queue.length === 0) {
        this.isProcessingQueue = false;
        return;
      }

      console.log(`Processing ${queue.length} cached locations`);

      for (let i = 0; i < queue.length; i++) {
        const locationData = queue[i];
        
        try {
          await this.sendLocationToServer(locationData);
          // Remove from queue if successful
          await StorageService.removeFromQueue('location_queue', i);
          console.log(`Cached location sent (${i + 1}/${queue.length})`);
        } catch (error) {
          console.error('Failed to send cached location:', error);
          // Stop processing if we hit an error (likely still offline or server issue)
          break;
        }

        // Small delay between requests
        await new Promise(resolve => setTimeout(resolve, 1000));
      }

    } catch (error) {
      console.error('Error processing retry queue:', error);
    } finally {
      this.isProcessingQueue = false;
      
      // Schedule next retry with exponential backoff
      const remainingQueue = await StorageService.getQueue('location_queue');
      if (remainingQueue.length > 0) {
        setTimeout(() => this.processRetryQueue(), this.retryDelay);
        // Increase delay for next retry (exponential backoff)
        this.retryDelay = Math.min(this.retryDelay * 2, this.maxRetryDelay);
      } else {
        // Reset delay on success
        this.retryDelay = 5000;
      }
    }
  }

  async getBatteryLevel() {
    try {
      const level = await Battery.getBatteryLevelAsync();
      return Math.round(level * 100);
    } catch (error) {
      console.error('Error getting battery level:', error);
      return null;
    }
  }

  async getDeviceInfo() {
    return {
      os: Device.osName || 'unknown',
      version: Device.osVersion || 'unknown',
      model: Device.modelName || 'unknown',
    };
  }
}

export const LocationService = new LocationServiceClass();
