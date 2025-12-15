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
  TouchableOpacity,
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
import { DeviceService } from './src/services/DeviceService';

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
  const [permissionGranted, setPermissionGranted] = useState(false);
  const [showPermissionPrompt, setShowPermissionPrompt] = useState(false);

  useEffect(() => {
    checkInitialPermissions();
  }, []);

  const checkInitialPermissions = async () => {
    try {
      // Check current permission status
      const { status: foregroundStatus } = await Location.getForegroundPermissionsAsync();
      const { status: backgroundStatus } = await Location.getBackgroundPermissionsAsync();

      if (foregroundStatus === 'granted' && backgroundStatus === 'granted') {
        // All permissions granted, proceed with initialization
        setPermissionGranted(true);
        initializeApp();
      } else {
        // Show permission prompt
        setShowPermissionPrompt(true);
        setIsInitializing(false);
      }
    } catch (error) {
      console.error('Permission check error:', error);
      setShowPermissionPrompt(true);
      setIsInitializing(false);
    }
  };

  const requestPermissionsAndStart = async () => {
    setIsInitializing(true);
    setShowPermissionPrompt(false);
    
    try {
      // Request permissions with clear explanation
      const result = await requestLocationPermissions();
      
      if (result) {
        setPermissionGranted(true);
        await initializeApp();
      } else {
        setIsInitializing(false);
        setShowPermissionPrompt(true);
      }
    } catch (error) {
      console.error('Permission request error:', error);
      Alert.alert('Error', 'Failed to request permissions. Please try again.');
      setIsInitializing(false);
      setShowPermissionPrompt(true);
    }
  };

  const requestLocationPermissions = async () => {
    try {
      // Request foreground permissions
      const { status: foregroundStatus } = await Location.requestForegroundPermissionsAsync();
      
      if (foregroundStatus !== 'granted') {
        Alert.alert(
          'Location Permission Required',
          'This app requires location access to function properly. Location tracking is necessary for work monitoring as per your employment agreement.',
          [{ text: 'OK' }]
        );
        return false;
      }

      // Request background permissions
      const { status: backgroundStatus } = await Location.requestBackgroundPermissionsAsync();
      
      if (backgroundStatus !== 'granted') {
        Alert.alert(
          'Background Location Required',
          Platform.OS === 'ios'
            ? 'Please go to Settings and select "Always" for location access to enable continuous tracking.'
            : 'Please enable "Allow all the time" for location access in your device settings to enable continuous tracking.',
          [{ text: 'OK' }]
        );
        return false;
      }

      return true;
    } catch (error) {
      console.error('Location permission request error:', error);
      return false;
    }
  };

  const initializeApp = async () => {
    try {
      setLocationStatus('Authenticating...');
      
      // Check if user is authenticated
      const isAuthenticated = await AuthService.isAuthenticated();
      
      if (!isAuthenticated) {
        // For demo purposes, auto-login with device ID
        // In production, you'd show a login screen
        await performAutoLogin();
      }

      // Verify we have a token after login
      const token = await AuthService.getAccessToken();
      if (!token) {
        throw new Error('Authentication failed - no access token');
      }

      setLocationStatus('Registering device...');
      
      // Register device with server
      try {
        await DeviceService.registerDevice();
        console.log('Device registered with server');
        
        // Start device heartbeat
        DeviceService.startHeartbeat(300000); // 5 minutes
      } catch (deviceError) {
        console.warn('Device registration failed:', deviceError);
        // Continue anyway - not critical for basic functionality
      }

      setLocationStatus('Initializing location tracking...');
      
      // Initialize location tracking
      await initializeLocationTracking();
      
      // Start background location updates
      await startBackgroundLocationTracking();
      
      // Monitor battery level
      monitorBattery();
      
      setLocationStatus('Active');
      setIsInitializing(false);
    } catch (error) {
      console.error('App initialization error:', error);
      Alert.alert(
        'Initialization Error',
        error.message || 'Failed to initialize app. Please try again.',
        [
          { text: 'Retry', onPress: () => initializeApp() },
          { text: 'Cancel', style: 'cancel' }
        ]
      );
      setLocationStatus('Error: ' + error.message);
      setIsInitializing(false);
    }
  };

  const performAutoLogin = async () => {
    try {
      const deviceId = await StorageService.getDeviceId();
      console.log('Generated device ID:', deviceId);
      console.log('Device ID length:', deviceId.length);
      
      // Get device name for employee registration
      const deviceInfo = await DeviceService.getDeviceInfo();
      const deviceName = deviceInfo.deviceName || `Device ${Device.modelName || 'Unknown'}`;
      
      console.log('Device info:', {
        deviceId,
        deviceName,
        model: deviceInfo.modelName,
        os: deviceInfo.osName,
      });
      
      // Using device ID as both email and password for auto-registration
      // The server will automatically create an employee with device name
      // Use .com instead of .local for better email validation
      const email = `${deviceId}@device.com`;
      const password = deviceId;
      
      console.log('Attempting auto-login...');
      console.log('Email:', email);
      console.log('Password length:', password.length);
      console.log('Device name:', deviceName);
      console.log('Full login request:', { email, password: '***', device_name: deviceName });
      
      await AuthService.login(email, password, deviceName);
      console.log('Auto-login successful');
    } catch (error) {
      console.error('Auto-login failed:', error);
      console.error('Error details:', {
        message: error.message,
        stack: error.stack,
      });
      throw new Error('Authentication failed: ' + error.message);
    }initializeLocationTracking = async () => {
    try {
      console.log('Initializing location tracking...');
      
      // Get current location to verify permissions are working
      const location = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.High,
      });
      
      console.log('Current location:', {
        latitude: location.coords.latitude,
        longitude: location.coords.longitude,
        accuracy: location.coords.accuracy,
      });
      
      // Send initial location to server
      try {
        await LocationService.sendLocation(location);
        console.log('Initial location sent to server');
      } catch (locationError) {
        console.warn('Failed to send initial location:', locationError);
      }
      
      setLocationStatus('Location initialized');
    } catch (error) {
      console.error('Location initialization error:', error);
      throw new Error('Failed to get initial location: ' + error.message);
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
        <Text style={styles.loadingText}>{locationStatus || 'Initializing...'}</Text>
      </View>
    );
  }

  if (showPermissionPrompt) {
    return (
      <View style={styles.container}>
        <View style={styles.logoContainer}>
          <View style={styles.logoPlaceholder}>
            <Text style={styles.logoText}>COMPANY LOGO</Text>
          </View>
        </View>

        <Text style={styles.title}>Location Permission Required</Text>
        
        <View style={styles.permissionNotice}>
          <Text style={styles.permissionTitle}>⚠️ GPS Tracking Notice</Text>
          <Text style={styles.permissionText}>
            This app requires GPS location access to function properly.
          </Text>
          <Text style={styles.permissionText}>
            {'\n'}The app will request permission to:
          </Text>
          <Text style={styles.privacyBullet}>
            ✓ Access your location when the app is in use
          </Text>
          <Text style={styles.privacyBullet}>
            ✓ Access your location in the background
          </Text>
          <Text style={styles.permissionText}>
            {'\n'}This is required for:
          </Text>
          <Text style={styles.privacyBullet}>
            • Employee work tracking
          </Text>
          <Text style={styles.privacyBullet}>
            • Compliance monitoring
          </Text>
          <Text style={styles.privacyBullet}>
            • Operational requirements
          </Text>
          <Text style={styles.permissionText}>
            {'\n'}By continuing, you agree to location tracking as per your employment agreement.
          </Text>
        </View>

        <TouchableOpacity 
          style={styles.continueButton}
          onPress={requestPermissionsAndStart}
        >
          <Text style={styles.continueButtonText}>Continue & Grant Permissions</Text>
        </TouchableOpacity>
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
  permissionNotice: {
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 10,
    marginBottom: 20,
    marginHorizontal: 20,
    width: '90%',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  permissionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#ff6b6b',
    marginBottom: 15,
    textAlign: 'center',
  },
  permissionText: {
    fontSize: 14,
    color: '#555',
    lineHeight: 20,
    marginBottom: 5,
  },
  continueButton: {
    backgroundColor: '#4287f5',
    paddingVertical: 15,
    paddingHorizontal: 40,
    borderRadius: 25,
    marginTop: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 4,
    elevation: 5,
  },
  continueButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
    textAlign: 'center',
  },
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
