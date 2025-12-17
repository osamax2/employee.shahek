import axios from 'axios';
import * as Device from 'expo-device';
import { AuthService } from './AuthService';
import { API_BASE_URL } from './config';

/**
 * HeartbeatService - Sends periodic heartbeat signals to keep device status updated
 * This ensures that when the app is running, the backend knows the device is active
 * When the app is uninstalled/closed, heartbeats stop and device goes offline
 */
class HeartbeatServiceClass {
  constructor() {
    this.heartbeatInterval = null;
    this.intervalDuration = 90000; // 90 seconds (1.5 minutes)
    this.isRunning = false;
  }

  /**
   * Start sending heartbeat signals
   */
  start() {
    if (this.isRunning) {
      console.log('Heartbeat service already running');
      return;
    }

    console.log('Starting heartbeat service...');
    this.isRunning = true;

    // Send first heartbeat immediately
    this.sendHeartbeat();

    // Then send periodically
    this.heartbeatInterval = setInterval(() => {
      this.sendHeartbeat();
    }, this.intervalDuration);
  }

  /**
   * Stop sending heartbeat signals
   */
  stop() {
    if (!this.isRunning) {
      return;
    }

    console.log('Stopping heartbeat service...');
    this.isRunning = false;

    if (this.heartbeatInterval) {
      clearInterval(this.heartbeatInterval);
      this.heartbeatInterval = null;
    }
  }

  /**
   * Send a heartbeat to the server
   */
  async sendHeartbeat() {
    try {
      const token = await AuthService.getAccessToken();
      
      if (!token) {
        console.log('No access token, skipping heartbeat');
        return;
      }

      // Get device ID
      const deviceId = await Device.getDeviceIdAsync();
      if (!deviceId) {
        console.log('No device ID, skipping heartbeat');
        return;
      }

      const heartbeatData = {
        device_id: deviceId,
        timestamp: new Date().toISOString(),
      };

      const response = await axios.post(
        `${API_BASE_URL}/device/heartbeat`,
        heartbeatData,
        {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json',
          },
          timeout: 10000, // 10 second timeout
        }
      );

      console.log('Heartbeat sent successfully:', response.data);

    } catch (error) {
      if (error.response) {
        console.error('Heartbeat failed:', {
          status: error.response.status,
          data: error.response.data,
        });

        // If unauthorized, try to refresh token
        if (error.response.status === 401) {
          try {
            await AuthService.refreshAccessToken();
            // Retry sending heartbeat after token refresh
            await this.sendHeartbeat();
          } catch (refreshError) {
            console.error('Token refresh failed during heartbeat:', refreshError);
            // Stop heartbeat service if we can't authenticate
            this.stop();
          }
        }
      } else {
        console.error('Heartbeat error:', error.message);
      }
    }
  }

  /**
   * Get the status of the heartbeat service
   */
  isActive() {
    return this.isRunning;
  }

  /**
   * Update the heartbeat interval
   * @param {number} intervalSeconds - Interval in seconds
   */
  setInterval(intervalSeconds) {
    this.intervalDuration = intervalSeconds * 1000;
    
    // Restart if currently running
    if (this.isRunning) {
      this.stop();
      this.start();
    }
  }
}

// Export singleton instance
export const HeartbeatService = new HeartbeatServiceClass();
