<?php

return [
    'online_threshold_minutes' => env('EMPLOYEE_ONLINE_THRESHOLD_MINUTES', 10),
    'map_auto_refresh_seconds' => env('MAP_AUTO_REFRESH_SECONDS', 30),
    'rate_limit_per_minute' => env('RATE_LIMIT_PER_MINUTE', 60),
];
