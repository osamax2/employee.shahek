import React from 'react';
import { View, Text, StyleSheet, Button, Alert } from 'react-native';
import * as Location from 'expo-location';

export default function App() {
  const [location, setLocation] = React.useState(null);

  const requestLocation = async () => {
    try {
      const { status } = await Location.requestForegroundPermissionsAsync();
      if (status !== 'granted') {
        Alert.alert('Permission denied');
        return;
      }

      const loc = await Location.getCurrentPositionAsync({});
      setLocation(loc);
      Alert.alert('Success', `Lat: ${loc.coords.latitude}, Lng: ${loc.coords.longitude}`);
    } catch (error) {
      Alert.alert('Error', error.message);
    }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Employee Tracking - TEST</Text>
      <Text style={styles.subtitle}>Server: employee.shahek.org</Text>
      
      <Button title="Get Location" onPress={requestLocation} />
      
      {location && (
        <View style={styles.location}>
          <Text>Latitude: {location.coords.latitude}</Text>
          <Text>Longitude: {location.coords.longitude}</Text>
          <Text>Accuracy: {location.coords.accuracy}m</Text>
        </View>
      )}
      
      <Text style={styles.status}>
        ✅ Server OK: https://employee.shahek.org/public/admin/dashboard
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 20,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  subtitle: {
    fontSize: 16,
    color: '#666',
    marginBottom: 30,
  },
  location: {
    marginTop: 20,
    padding: 15,
    backgroundColor: '#f0f0f0',
    borderRadius: 8,
  },
  status: {
    position: 'absolute',
    bottom: 50,
    fontSize: 12,
    color: 'green',
  },
});
