<?php

return [
    // Device is considered offline if no data received in this time
    // Reduced from 10 to 3 minutes for faster offline detection
    'online_threshold_minutes' => env('EMPLOYEE_ONLINE_THRESHOLD_MINUTES', 3),
    
    // Map refresh interval in dashboard
    'map_auto_refresh_seconds' => env('MAP_AUTO_REFRESH_SECONDS', 30),
    
    // API rate limiting
    'rate_limit_per_minute' => env('RATE_LIMIT_PER_MINUTE', 60),
    
    // How often mobile app should send heartbeat (in seconds)
    'heartbeat_interval_seconds' => env('HEARTBEAT_INTERVAL_SECONDS', 90), // 1.5 minutes
];
