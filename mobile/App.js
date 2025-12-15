import React, { useEffect, useState } from 'react';
import {
  StyleSheet,
  Text,
  View,
  Image,
  ScrollView,
  Alert,
  Platform,
  ActivityIndicator,
} from 'react-native';
import * as Location from 'expo-location';
import * as TaskManager from 'expo-task-manager';
import * as BackgroundFetch from 'expo-background-fetch';
import * as SecureStore from 'expo-secure-store';
import * as Device from 'expo-device';
import * as Battery from 'expo-battery';
import { LocationService } from './src/services/LocationService';
import { AuthService } from './src/services/AuthService';
import { StorageService } from './src/services/StorageService';

const LOCATION_TASK_NAME = 'background-location-task';

// Define the background location task
TaskManager.defineTask(LOCATION_TASK_NAME, async ({ data, error }) => {
  if (error) {
    console.error('Background location task error:', error);
    return;
  }
  if (data) {
    const { locations } = data;
    await LocationService.processBackgroundLocations(locations);
  }
});

export default function App() {
  const [isInitializing, setIsInitializing] = useState(true);
  const [locationStatus, setLocationStatus] = useState('Initializing...');
  const [batteryLevel, setBatteryLevel] = useState(null);

  useEffect(() => {
    initializeApp();
  }, []);

  const initializeApp = async () => {
    try {
      // Check if user is authenticated
      const isAuthenticated = await AuthService.isAuthenticated();
      
      if (!isAuthenticated) {
        // For demo purposes, auto-login with device ID
        // In production, you'd show a login screen
        await performAutoLogin();
      }

      // Initialize location tracking
      await initializeLocationTracking();
      
      // Start background location updates
      await startBackgroundLocationTracking();
      
      // Monitor battery level
      monitorBattery();
      
      setIsInitializing(false);
    } catch (error) {
      console.error('App initialization error:', error);
      Alert.alert('Initialization Error', error.message);
      setIsInitializing(false);
    }
  };

  const performAutoLogin = async () => {
    try {
      const deviceId = await StorageService.getDeviceId();
      // Using device ID as both email and password for demo
      // In production, implement proper authentication
      const email = `${deviceId}@device.local`;
      const password = deviceId;
      
      await AuthService.login(email, password);
      console.log('Auto-login successful with device ID:', deviceId);
    } catch (error) {
      console.error('Auto-login failed:', error);
      throw new Error('Authentication failed. Please contact your administrator.');
    }
  };

  const initializeLocationTracking = async () => {
    try {
      // Request foreground permissions first
      let { status } = await Location.requestForegroundPermissionsAsync();
      
      if (status !== 'granted') {
        setLocationStatus('Location permission denied');
        Alert.alert(
          'Location Permission Required',
          'This app requires location access to function. Please enable location permissions in your device settings.',
          [{ text: 'OK' }]
        );
        return;
      }

      setLocationStatus('Foreground permission granted');

      // Request background permissions
      const { status: backgroundStatus } = await Location.requestBackgroundPermissionsAsync();
      
      if (backgroundStatus !== 'granted') {
        setLocationStatus('Background permission needed');
        Alert.alert(
          'Background Location Required',
          Platform.OS === 'ios'
            ? 'Please go to Settings and select "Always" for location access to enable continuous tracking.'
            : 'Please enable "Allow all the time" for location access in your device settings.',
          [{ text: 'OK' }]
        );
      } else {
        setLocationStatus('All permissions granted');
      }

      // Send initial location
      await LocationService.sendCurrentLocation();
      
    } catch (error) {
      console.error('Location initialization error:', error);
      setLocationStatus('Error: ' + error.message);
    }
  };

  const startBackgroundLocationTracking = async () => {
    try {
      const isRegistered = await TaskManager.isTaskRegisteredAsync(LOCATION_TASK_NAME);
      
      if (!isRegistered) {
        await Location.startLocationUpdatesAsync(LOCATION_TASK_NAME, {
          accuracy: Location.Accuracy.High,
          distanceInterval: 100, // meters
          timeInterval: 300000, // 5 minutes
          foregroundService: {
            notificationTitle: 'Company Active',
            notificationBody: 'Location tracking is active for work purposes',
            notificationColor: '#4287f5',
          },
          showsBackgroundLocationIndicator: true,
          pausesUpdatesAutomatically: false,
        });
        
        console.log('Background location tracking started');
        setLocationStatus('Tracking active');
      }

      // Also set up periodic background fetch for heartbeat
      await BackgroundFetch.registerTaskAsync(LOCATION_TASK_NAME, {
        minimumInterval: 300, // 5 minutes
        stopOnTerminate: false,
        startOnBoot: true,
      });
      
    } catch (error) {
      console.error('Background location start error:', error);
    }
  };

  const monitorBattery = async () => {
    try {
      const level = await Battery.getBatteryLevelAsync();
      setBatteryLevel(Math.round(level * 100));
      
      // Update battery level every 5 minutes
      setInterval(async () => {
        const level = await Battery.getBatteryLevelAsync();
        setBatteryLevel(Math.round(level * 100));
      }, 300000);
    } catch (error) {
      console.error('Battery monitoring error:', error);
    }
  };

  if (isInitializing) {
    return (
      <View style={styles.container}>
        <ActivityIndicator size="large" color="#4287f5" />
        <Text style={styles.loadingText}>Initializing...</Text>
      </View>
    );
  }

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <View style={styles.logoContainer}>
        <View style={styles.logoPlaceholder}>
          <Text style={styles.logoText}>COMPANY LOGO</Text>
        </View>
      </View>

      <Text style={styles.title}>Coming Soon</Text>
      
      <Text style={styles.subtitle}>Employee Tracking System</Text>

      <View style={styles.statusContainer}>
        <Text style={styles.statusLabel}>Status:</Text>
        <Text style={styles.statusValue}>{locationStatus}</Text>
        {batteryLevel !== null && (
          <>
            <Text style={styles.statusLabel}>Battery:</Text>
            <Text style={styles.statusValue}>{batteryLevel}%</Text>
          </>
        )}
      </View>

      <View style={styles.privacyNotice}>
        <Text style={styles.privacyTitle}>Privacy Notice</Text>
        <Text style={styles.privacyText}>
          This application collects and transmits your location data to support workplace
          operations and compliance requirements.
        </Text>
        <Text style={styles.privacyText}>
          {'\n'}By using this application, you acknowledge that:
        </Text>
        <Text style={styles.privacyBullet}>
          • Your location is being tracked during work hours
        </Text>
        <Text style={styles.privacyBullet}>
          • Data is transmitted securely via HTTPS
        </Text>
        <Text style={styles.privacyBullet}>
          • Only necessary data is collected (location, timestamp, device info)
        </Text>
        <Text style={styles.privacyBullet}>
          • You have provided explicit consent as per your employment agreement
        </Text>
        <Text style={styles.privacyText}>
          {'\n'}For questions or concerns, please contact your HR department.
        </Text>
      </View>

      <Text style={styles.footerText}>
        Device ID: {Device.osName} {Device.osVersion}
      </Text>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flexGrow: 1,
    backgroundColor: '#f5f5f5',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 20,
  },
  logoContainer: {
    marginBottom: 30,
    marginTop: 40,
  },
  logoPlaceholder: {
    width: 150,
    height: 150,
    backgroundColor: '#4287f5',
    borderRadius: 75,
    alignItems: 'center',
    justifyContent: 'center',
  },
  logoText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: 'bold',
    textAlign: 'center',
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 10,
  },
  subtitle: {
    fontSize: 18,
    color: '#666',
    marginBottom: 30,
  },
  statusContainer: {
    backgroundColor: '#fff',
    padding: 15,
    borderRadius: 10,
    marginBottom: 20,
    width: '100%',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  statusLabel: {
    fontSize: 14,
    color: '#666',
    marginTop: 5,
  },
  statusValue: {
    fontSize: 16,
    fontWeight: '600',
    color: '#4287f5',
    marginBottom: 5,
  },
  privacyNotice: {
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 10,
    marginBottom: 20,
    width: '100%',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  privacyTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 10,
  },
  privacyText: {
    fontSize: 14,
    color: '#555',
    lineHeight: 20,
  },
  privacyBullet: {
    fontSize: 14,
    color: '#555',
    lineHeight: 24,
    paddingLeft: 10,
  },
  loadingText: {
    marginTop: 10,
    fontSize: 16,
    color: '#666',
  },
  footerText: {
    fontSize: 12,
    color: '#999',
    marginTop: 20,
  },
});
