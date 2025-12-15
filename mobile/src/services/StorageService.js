import AsyncStorage from '@react-native-async-storage/async-storage';
import * as Device from 'expo-device';

class StorageServiceClass {
  async getDeviceId() {
    try {
      let deviceId = await AsyncStorage.getItem('device_id');
      
      if (!deviceId) {
        // Generate a unique device ID
        deviceId = `${Device.osName}-${Device.modelId}-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        await AsyncStorage.setItem('device_id', deviceId);
      }
      
      return deviceId;
    } catch (error) {
      console.error('Error getting device ID:', error);
      return 'unknown-device';
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
