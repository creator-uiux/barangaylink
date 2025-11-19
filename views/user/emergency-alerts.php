<?php
/**
 * Emergency Alerts - PERFECTLY SYNCHRONIZED with components/EmergencyAlerts.tsx
 * EXACT MATCH in design, layout, and functionality
 */

$db = getDB();

// Get all active alerts
$alerts = fetchAll("SELECT * FROM emergency_alerts WHERE is_active = 1 ORDER BY created_at DESC");
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-600 to-orange-600 rounded-lg p-6 text-white">
        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-lg rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-1">Emergency Alerts</h2>
                    <p class="text-red-100">Real-time community updates and emergency notifications</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-sm text-green-300">Live</span>
            </div>
        </div>
    </div>

    <!-- Weather Widget -->
    <div class="bg-blue-50 border-2 border-blue-200 text-blue-900 rounded-lg p-6 transition-all hover:shadow-lg" id="weather-widget">
        <div class="flex items-start space-x-4">
            <!-- Icon -->
            <div class="p-3 rounded-lg bg-blue-500 text-white flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                </svg>
            </div>
            
            <!-- Content -->
            <div class="flex-1">
                <!-- Title and Badge -->
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h3 class="text-xl font-bold mb-1">Current Weather Update</h3>
                        <div class="flex items-center space-x-2 text-sm">
                            <span class="px-2 py-1 rounded-full text-xs bg-blue-500 text-white font-semibold">
                                INFO
                            </span>
                            <span class="text-gray-600" id="weather-time-ago">
                                Just now
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Weather Details -->
                <div class="mb-3">
                    <div class="flex items-center space-x-4 mb-3">
                        <div>
                            <p class="text-4xl font-bold text-blue-900" id="weather-temp">--°C</p>
                            <p class="text-base text-gray-800" id="weather-condition">Loading...</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 mb-3 text-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm" id="weather-location">Manila, Philippines</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                            </svg>
                            <div>
                                <p class="text-xs text-gray-600">Humidity</p>
                                <p class="font-semibold text-gray-900" id="weather-humidity">--%</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                            <div>
                                <p class="text-xs text-gray-600">Wind Speed</p>
                                <p class="font-semibold text-gray-900" id="weather-windspeed">-- km/h</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">
                        Posted by: Weather Service
                    </span>
                    <span class="text-gray-500 flex items-center space-x-1">
                        <div class="w-2 h-2 bg-green-500 rounded-full" id="weather-status-dot"></div>
                        <span id="weather-status-text">Live Data</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Alerts -->
    <?php if (count($alerts) === 0): ?>
        <div class="bg-white rounded-lg p-12 border border-gray-200 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No Active Alerts</h3>
            <p class="text-gray-600">
                There are currently no emergency alerts or important announcements. Check back later for updates.
            </p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($alerts as $alert): 
                $severity = strtolower($alert['severity'] ?? 'info');
                
                // Determine colors based on severity
                if ($severity === 'emergency' || $severity === 'critical') {
                    $bgColor = 'bg-red-50';
                    $borderColor = 'border-red-300';
                    $textColor = 'text-red-900';
                    $badgeColor = 'bg-red-600 text-white';
                    $iconColor = 'text-red-600';
                } elseif ($severity === 'warning') {
                    $bgColor = 'bg-yellow-50';
                    $borderColor = 'border-yellow-200';
                    $textColor = 'text-yellow-900';
                    $badgeColor = 'bg-yellow-500 text-white';
                    $iconColor = 'text-yellow-600';
                } else {
                    $bgColor = 'bg-blue-50';
                    $borderColor = 'border-blue-200';
                    $textColor = 'text-blue-900';
                    $badgeColor = 'bg-blue-500 text-white';
                    $iconColor = 'text-blue-600';
                }
            ?>
                <div class="rounded-lg p-6 border-2 <?php echo $bgColor . ' ' . $borderColor . ' ' . $textColor; ?> transition-all hover:shadow-lg">
                    <div class="flex items-start space-x-4">
                        <div class="p-3 rounded-lg <?php echo $badgeColor; ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <?php if ($severity === 'emergency' || $severity === 'critical'): ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                <?php elseif ($severity === 'warning'): ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <?php else: ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <?php endif; ?>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="text-xl font-bold mb-1"><?php echo htmlspecialchars($alert['title']); ?></h3>
                                    <div class="flex items-center space-x-2 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo $badgeColor; ?>">
                                            <?php echo strtoupper($severity); ?>
                                        </span>
                                        <span class="text-gray-600">
                                            <?php 
                                            $createdTime = strtotime($alert['created_at']);
                                            $timeDiff = time() - $createdTime;
                                            if ($timeDiff < 60) {
                                                echo 'Just now';
                                            } elseif ($timeDiff < 3600) {
                                                echo floor($timeDiff / 60) . ' minutes ago';
                                            } elseif ($timeDiff < 86400) {
                                                echo floor($timeDiff / 3600) . ' hours ago';
                                            } else {
                                                echo floor($timeDiff / 86400) . ' days ago';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-800 leading-relaxed mb-3">
                                <?php echo htmlspecialchars($alert['message']); ?>
                            </p>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">
                                    Posted by: <?php echo htmlspecialchars($alert['created_by'] ?? 'Barangay Admin'); ?>
                                </span>
                                <span class="text-gray-500">
                                    <?php echo date('M j, Y g:i A', $createdTime); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Info Box -->
    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
        <h3 class="text-lg font-bold text-gray-900 mb-3">About Emergency Alerts</h3>
        <div class="space-y-2 text-sm text-gray-700">
            <div class="flex items-start space-x-2">
                <div class="w-2 h-2 bg-red-600 rounded-full mt-1.5 flex-shrink-0"></div>
                <p><strong class="text-gray-900">Emergency/Critical:</strong> Urgent situations requiring immediate attention (typhoons, fires, etc.)</p>
            </div>
            <div class="flex items-start space-x-2">
                <div class="w-2 h-2 bg-yellow-500 rounded-full mt-1.5 flex-shrink-0"></div>
                <p><strong class="text-gray-900">Warning:</strong> Important notices that may affect your daily routine (power interruptions, road closures)</p>
            </div>
            <div class="flex items-start space-x-2">
                <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 flex-shrink-0"></div>
                <p><strong class="text-gray-900">Info:</strong> General community updates and announcements (events, programs, activities)</p>
            </div>
        </div>
    </div>

    <!-- Emergency Contacts -->
    <div class="bg-white rounded-lg p-6 border border-gray-200">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Emergency Contacts</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <!-- Barangay Hall -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                <h4 class="font-semibold text-gray-900 mb-1">Barangay Hall</h4>
                <p class="text-xl font-bold text-blue-600 mb-1">(02) 1234-5678</p>
                <p class="text-sm text-gray-600">General inquiries and concerns</p>
            </div>
            
            <!-- Emergency Hotline -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                <h4 class="font-semibold text-gray-900 mb-1">Emergency Hotline</h4>
                <p class="text-xl font-bold text-blue-600 mb-1">911</p>
                <p class="text-sm text-gray-600">Police, Fire, Medical emergencies</p>
            </div>
            
            <!-- Health Center -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                <h4 class="font-semibold text-gray-900 mb-1">Barangay Health Center</h4>
                <p class="text-xl font-bold text-blue-600 mb-1">(02) 8765-4321</p>
                <p class="text-sm text-gray-600">Health and medical assistance</p>
            </div>
            
            <!-- NDRRMC -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                <h4 class="font-semibold text-gray-900 mb-1">NDRRMC Hotline</h4>
                <p class="text-xl font-bold text-blue-600 mb-1">(02) 8911-5061</p>
                <p class="text-sm text-gray-600">Disaster response and management</p>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Weather Widget JavaScript - EXACT MATCH with components/EmergencyAlerts.tsx
 */

// Weather code descriptions (matching weatherApi.ts)
const weatherCodeDescriptions = {
    0: 'Clear Sky',
    1: 'Mainly Clear',
    2: 'Partly Cloudy',
    3: 'Overcast',
    45: 'Foggy',
    48: 'Depositing Rime Fog',
    51: 'Light Drizzle',
    53: 'Moderate Drizzle',
    55: 'Dense Drizzle',
    56: 'Light Freezing Drizzle',
    57: 'Dense Freezing Drizzle',
    61: 'Slight Rain',
    63: 'Moderate Rain',
    65: 'Heavy Rain',
    66: 'Light Freezing Rain',
    67: 'Heavy Freezing Rain',
    71: 'Slight Snow',
    73: 'Moderate Snow',
    75: 'Heavy Snow',
    77: 'Snow Grains',
    80: 'Slight Rain Showers',
    81: 'Moderate Rain Showers',
    82: 'Violent Rain Showers',
    85: 'Slight Snow Showers',
    86: 'Heavy Snow Showers',
    95: 'Thunderstorm',
    96: 'Thunderstorm with Slight Hail',
    99: 'Thunderstorm with Heavy Hail'
};

// Fetch weather data from Open-Meteo API
async function fetchWeatherData() {
    try {
        const latitude = 14.5995;  // Manila, Philippines
        const longitude = 120.9842;
        const baseUrl = 'https://api.open-meteo.com/v1/forecast';
        
        const url = new URL(baseUrl);
        url.searchParams.append('latitude', latitude);
        url.searchParams.append('longitude', longitude);
        url.searchParams.append('current', 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code');
        url.searchParams.append('timezone', 'Asia/Singapore');
        
        const response = await fetch(url.toString());
        
        if (!response.ok) {
            throw new Error(`Weather API failed: ${response.status}`);
        }
        
        const data = await response.json();
        const current = data.current;
        
        return {
            temp: Math.round(current.temperature_2m),
            condition: weatherCodeDescriptions[current.weather_code] || 'Unknown',
            humidity: Math.round(current.relative_humidity_2m),
            windSpeed: Math.round(current.wind_speed_10m),
            location: 'Manila, Philippines',
            weatherCode: current.weather_code,
            isReal: true
        };
    } catch (error) {
        console.error('Failed to fetch weather data:', error);
        
        // Return fallback data
        return {
            temp: 28,
            condition: 'Partly Cloudy',
            humidity: 65,
            windSpeed: 12,
            location: 'Manila, Philippines',
            weatherCode: 2,
            isReal: false
        };
    }
}

// Update weather display
function updateWeatherDisplay(weatherData) {
    document.getElementById('weather-temp').textContent = weatherData.temp + '°C';
    document.getElementById('weather-condition').textContent = weatherData.condition;
    document.getElementById('weather-location').textContent = weatherData.location;
    document.getElementById('weather-humidity').textContent = weatherData.humidity + '%';
    document.getElementById('weather-windspeed').textContent = weatherData.windSpeed + ' km/h';
    
    // Update status indicator
    const statusDot = document.getElementById('weather-status-dot');
    const statusText = document.getElementById('weather-status-text');
    
    if (weatherData.isReal) {
        statusDot.className = 'w-2 h-2 bg-green-500 rounded-full';
        statusText.textContent = 'Live Data';
    } else {
        statusDot.className = 'w-2 h-2 bg-yellow-500 rounded-full';
        statusText.textContent = 'Cached Data';
    }
}

// Time ago function
function updateTimeAgo() {
    const now = new Date();
    document.getElementById('weather-time-ago').textContent = 'Just now';
}

// Load weather data
async function loadWeather() {
    const weatherData = await fetchWeatherData();
    updateWeatherDisplay(weatherData);
    updateTimeAgo();
}

// Initialize
loadWeather();

// Refresh every 10 minutes
setInterval(loadWeather, 10 * 60 * 1000);

// Update time ago every 30 seconds
setInterval(updateTimeAgo, 30000);
</script>
