import AsyncStorage from '@react-native-async-storage/async-storage';
import * as Device from 'expo-device';

class StorageServiceClass {
  async getDeviceId() {
    try {
      let deviceId = await AsyncStorage.getItem('device_id');
      
      if (!deviceId) {
        // Generate a unique device ID (email-safe: only alphanumeric and hyphens)
        const timestamp = Date.now().toString();
        const random = Math.random().toString(36).substr(2, 9);
        // Remove special characters from osName and modelId for email compatibility
        const osName = (Device.osName || 'unknown').replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
        const modelId = (Device.modelId || 'device').replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
        
        deviceId = `${osName}${modelId}${timestamp}${random}`;
        
        console.log('Generated new device ID:', deviceId);
        console.log('Device ID length:', deviceId.length);
        
        await AsyncStorage.setItem('device_id', deviceId);
      } else {
        console.log('Using existing device ID:', deviceId);
        console.log('Device ID length:', deviceId.length);
      }
      
      return deviceId;
    } catch (error) {
      console.error('Error getting device ID:', error);
      // Fallback ID (email-safe and > 6 chars)
      return 'unknown' + Date.now();
    }
  }

  async addToQueue(queueName, item) {
    try {
      const queue = await this.getQueue(queueName);
      queue.push(item);
      await AsyncStorage.setItem(queueName, JSON.stringify(queue));
    } catch (error) {
      console.error('Error adding to queue:', error);
    }
  }

  async getQueue(queueName) {
    try {
      const queueJson = await AsyncStorage.getItem(queueName);
      return queueJson ? JSON.parse(queueJson) : [];
    } catch (error) {
      console.error('Error getting queue:', error);
      return [];
    }
  }

  async removeFromQueue(queueName, index) {
    try {
      const queue = await this.getQueue(queueName);
      queue.splice(index, 1);
      await AsyncStorage.setItem(queueName, JSON.stringify(queue));
    } catch (error) {
      console.error('Error removing from queue:', error);
    }
  }

  async clearQueue(queueName) {
    try {
      await AsyncStorage.setItem(queueName, JSON.stringify([]));
    } catch (error) {
      console.error('Error clearing queue:', error);
    }
  }

  async setItem(key, value) {
    try {
      await AsyncStorage.setItem(key, JSON.stringify(value));
    } catch (error) {
      console.error('Error setting item:', error);
    }
  }

  async getItem(key) {
    try {
      const value = await AsyncStorage.getItem(key);
      return value ? JSON.parse(value) : null;
    } catch (error) {
      console.error('Error getting item:', error);
      return null;
    }
  }

  async removeItem(key) {
    try {
      await AsyncStorage.removeItem(key);
    } catch (error) {
      console.error('Error removing item:', error);
    }
  }
}

export const StorageService = new StorageServiceClass();
