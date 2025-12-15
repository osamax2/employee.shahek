// Configuration constants
export const API_BASE_URL = 'https://employee.shahek.org/api';

export const LOCATION_CONFIG = {
  UPDATE_INTERVAL: parseInt(process.env.LOCATION_UPDATE_INTERVAL || '300000', 10), // 5 minutes
  DISTANCE_FILTER: parseInt(process.env.LOCATION_DISTANCE_FILTER || '100', 10), // 100 meters
  ACCURACY: process.env.LOCATION_ACCURACY || 'high',
};

export const COMPANY_CONFIG = {
  NAME: process.env.COMPANY_NAME || 'Your Company',
  LOGO_URL: process.env.COMPANY_LOGO_URL || null,
};
