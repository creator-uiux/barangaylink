<?php
/**
 * Real-time Data Service
 * Fetches news, weather, and emergency information from various APIs
 */

require_once __DIR__ . '/../config.php';

class RealtimeDataService {
    private $weatherApiKey;
    private $newsApiKey;
    private $cacheDir;
    
    public function __construct() {
        // Weather API - Get your free key from https://openweathermap.org/api
        $this->weatherApiKey = '8968c0215a254fbddf1eb89c43f759fd'; // Replace with your OpenWeatherMap API key
        
        // News API - Get your free key from https://newsapi.org/register
        $this->newsApiKey = '5b69bc728ccb49aabb7ac2faba847e22'; // Replace with your NewsAPI key
        
        $this->cacheDir = __DIR__ . '/../cache/';
        
        // Test API keys and disable if invalid
        $this->validateApiKeys();
        
        // Create cache directory if it doesn't exist
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Validate API keys and disable if invalid
     */
    private function validateApiKeys() {
        // Test weather API key
        if (!empty($this->weatherApiKey)) {
            $testUrl = "http://api.openweathermap.org/data/2.5/weather?q=London&appid=" . $this->weatherApiKey;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $testUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request only
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 401 || $httpCode === 403) {
                error_log('Weather API key is invalid, using simulation mode');
                $this->weatherApiKey = null;
            }
        }
        
        // Test news API key
        if (!empty($this->newsApiKey)) {
            $testUrl = "https://newsapi.org/v2/top-headlines?country=us&apiKey=" . $this->newsApiKey;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $testUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request only
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 401 || $httpCode === 403) {
                error_log('News API key is invalid, using simulation mode');
                $this->newsApiKey = null;
            }
        }
    }
    
    /**
     * Get all real-time alerts combining weather, news, and emergency data
     */
    public function getAllAlerts($location = 'Manila, Philippines') {
        $alerts = [];
        
        // Get weather alerts
        $weatherAlerts = $this->getWeatherAlerts($location);
        $alerts = array_merge($alerts, $weatherAlerts);
        
        // Get news alerts
        $newsAlerts = $this->getNewsAlerts();
        $alerts = array_merge($alerts, $newsAlerts);
        
        // Get emergency alerts
        $emergencyAlerts = $this->getEmergencyAlerts();
        $alerts = array_merge($alerts, $emergencyAlerts);
        
        // Sort by priority and timestamp
        usort($alerts, function($a, $b) {
            $priorityOrder = ['emergency' => 4, 'warning' => 3, 'info' => 2, 'resolved' => 1];
            $aPriority = $priorityOrder[$a['type']] ?? 0;
            $bPriority = $priorityOrder[$b['type']] ?? 0;
            
            if ($aPriority === $bPriority) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            }
            return $bPriority - $aPriority;
        });
        
        return $alerts;
    }
    
    /**
     * Get weather alerts and current conditions
     */
    private function getWeatherAlerts($location) {
        $cacheFile = $this->cacheDir . 'weather_' . md5($location) . '.json';
        $cacheTime = 15 * 60; // 15 minutes cache
        
        // Check cache first
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            if ($cached) {
                return $cached;
            }
        }
        
        $alerts = [];
        
        try {
            // Simulate weather data (replace with actual API call)
            $weatherData = $this->getWeatherData($location);
            
            if ($weatherData) {
                // Weather alerts based on conditions
                if ($weatherData['condition'] === 'Thunderstorm') {
                    $alerts[] = [
                        'id' => 'weather_' . time(),
                        'type' => 'emergency',
                        'title' => 'Thunderstorm Alert',
                        'message' => 'Severe thunderstorm detected. Stay indoors and avoid open areas.',
                        'timestamp' => date('Y-m-d H:i:s'),
                        'status' => 'active',
                        'source' => 'weather',
                        'priority' => 'high'
                    ];
                }
                
                if ($weatherData['temperature'] > 35) {
                    $alerts[] = [
                        'id' => 'weather_heat_' . time(),
                        'type' => 'warning',
                        'title' => 'Heat Advisory',
                        'message' => 'High temperature alert: ' . $weatherData['temperature'] . '°C. Stay hydrated and avoid prolonged sun exposure.',
                        'timestamp' => date('Y-m-d H:i:s'),
                        'status' => 'active',
                        'source' => 'weather',
                        'priority' => 'medium'
                    ];
                }
                
                if ($weatherData['wind_speed'] > 50) {
                    $alerts[] = [
                        'id' => 'weather_wind_' . time(),
                        'type' => 'warning',
                        'title' => 'Strong Wind Warning',
                        'message' => 'Strong winds detected: ' . $weatherData['wind_speed'] . ' km/h. Secure loose objects and avoid outdoor activities.',
                        'timestamp' => date('Y-m-d H:i:s'),
                        'status' => 'active',
                        'source' => 'weather',
                        'priority' => 'medium'
                    ];
                }
                
                // Current weather info
                $alerts[] = [
                    'id' => 'weather_current_' . time(),
                    'type' => 'info',
                    'title' => 'Current Weather',
                    'message' => $weatherData['condition'] . ', ' . $weatherData['temperature'] . '°C, Humidity: ' . $weatherData['humidity'] . '%',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'status' => 'active',
                    'source' => 'weather',
                    'priority' => 'low'
                ];
            }
            
            // Cache the results
            file_put_contents($cacheFile, json_encode($alerts));
            
        } catch (Exception $e) {
            error_log('Weather API error: ' . $e->getMessage());
        }
        
        return $alerts;
    }
    
    /**
     * Get news alerts from various sources
     */
    private function getNewsAlerts() {
        $cacheFile = $this->cacheDir . 'news.json';
        $cacheTime = 30 * 60; // 30 minutes cache
        
        // Check cache first
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            if ($cached) {
                return $cached;
            }
        }
        
        $alerts = [];
        
        try {
            // Simulate news data (replace with actual API call)
            $newsData = $this->getNewsData();
            
            foreach ($newsData as $news) {
                $alerts[] = [
                    'id' => 'news_' . $news['id'],
                    'type' => $news['priority'] === 'high' ? 'emergency' : 'info',
                    'title' => $news['title'],
                    'message' => $news['summary'],
                    'timestamp' => $news['published_at'],
                    'status' => 'active',
                    'source' => 'news',
                    'priority' => $news['priority'],
                    'url' => $news['url'] ?? null
                ];
            }
            
            // Cache the results
            file_put_contents($cacheFile, json_encode($alerts));
            
        } catch (Exception $e) {
            error_log('News API error: ' . $e->getMessage());
        }
        
        return $alerts;
    }
    
    /**
     * Get emergency alerts from local authorities
     */
    private function getEmergencyAlerts() {
        $alerts = [];
        
        // Check for emergency alerts in database or external sources
        try {
            if (USE_DATABASE) {
                require_once __DIR__ . '/db_utils.php';
                $db = getDB();
                $stmt = $db->query("SELECT * FROM emergency_alerts WHERE status = 'active' AND (expires_at IS NULL OR expires_at > NOW()) ORDER BY priority DESC, created_at DESC");
                $dbAlerts = $stmt->fetchAll();
                
                foreach ($dbAlerts as $alert) {
                    $alerts[] = [
                        'id' => 'emergency_' . $alert['id'],
                        'type' => $alert['type'],
                        'title' => $alert['title'],
                        'message' => $alert['message'],
                        'timestamp' => $alert['created_at'],
                        'status' => $alert['status'],
                        'source' => $alert['source'],
                        'priority' => $alert['priority'],
                        'url' => $alert['url']
                    ];
                }
            }
        } catch (Exception $e) {
            error_log('Emergency alerts database error: ' . $e->getMessage());
        }
        
        return $alerts;
    }
    
    /**
     * Get real weather data from OpenWeatherMap API
     */
    private function getWeatherData($location) {
        if (empty($this->weatherApiKey) || $this->weatherApiKey === 'YOUR_OPENWEATHER_API_KEY') {
            // Fallback to simulation if no API key
            return $this->getSimulatedWeatherData($location);
        }
        
        try {
            $url = "http://api.openweathermap.org/data/2.5/weather?q=" . urlencode($location) . "&appid=" . $this->weatherApiKey . "&units=metric";
            
            // Use cURL for better error handling
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'BarangayLink/1.0');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($response === false || $httpCode !== 200) {
                throw new Exception('Failed to fetch weather data. HTTP Code: ' . $httpCode);
            }
            
            $data = json_decode($response, true);
            if (!$data || isset($data['error']) || $data['cod'] !== 200) {
                throw new Exception('Invalid weather data received: ' . ($data['message'] ?? 'Unknown error'));
            }
            
            return [
                'condition' => $data['weather'][0]['main'],
                'description' => $data['weather'][0]['description'],
                'temperature' => round($data['main']['temp']),
                'humidity' => $data['main']['humidity'],
                'wind_speed' => round($data['wind']['speed'] * 3.6), // Convert m/s to km/h
                'location' => $data['name'],
                'country' => $data['sys']['country']
            ];
            
        } catch (Exception $e) {
            error_log('Weather API error: ' . $e->getMessage());
            return $this->getSimulatedWeatherData($location);
        }
    }
    
    /**
     * Fallback simulated weather data
     */
    private function getSimulatedWeatherData($location) {
        $conditions = ['Sunny', 'Cloudy', 'Rainy', 'Thunderstorm', 'Foggy'];
        $condition = $conditions[array_rand($conditions)];
        
        return [
            'condition' => $condition,
            'description' => strtolower($condition),
            'temperature' => rand(25, 40),
            'humidity' => rand(40, 90),
            'wind_speed' => rand(5, 80),
            'location' => $location,
            'country' => 'PH'
        ];
    }
    
    /**
     * Get real news data from NewsAPI
     */
    private function getNewsData() {
        if (empty($this->newsApiKey) || $this->newsApiKey === 'YOUR_NEWSAPI_KEY') {
            // Fallback to simulation if no API key
            return $this->getSimulatedNewsData();
        }
        
        try {
            // Get top headlines from Philippines
            $url = "https://newsapi.org/v2/top-headlines?country=ph&apiKey=" . $this->newsApiKey;
            
            // Use cURL for better error handling
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'BarangayLink/1.0');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'X-API-Key: ' . $this->newsApiKey
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($response === false || $httpCode !== 200) {
                throw new Exception('Failed to fetch news data. HTTP Code: ' . $httpCode);
            }
            
            $data = json_decode($response, true);
            if (!$data || $data['status'] !== 'ok') {
                throw new Exception('Invalid news data received: ' . ($data['message'] ?? 'Unknown error'));
            }
            
            $newsItems = [];
            foreach ($data['articles'] as $article) {
                $newsItems[] = [
                    'id' => md5($article['url']),
                    'title' => $article['title'],
                    'summary' => $article['description'] ?? substr($article['content'], 0, 200) . '...',
                    'published_at' => $article['publishedAt'],
                    'priority' => $this->determineNewsPriority($article['title']),
                    'url' => $article['url'],
                    'source' => $article['source']['name']
                ];
            }
            
            return $newsItems;
            
        } catch (Exception $e) {
            error_log('News API error: ' . $e->getMessage());
            return $this->getSimulatedNewsData();
        }
    }
    
    /**
     * Determine news priority based on keywords
     */
    private function determineNewsPriority($title) {
        $highPriorityKeywords = ['emergency', 'disaster', 'flood', 'typhoon', 'earthquake', 'fire', 'accident', 'crash'];
        $mediumPriorityKeywords = ['traffic', 'construction', 'maintenance', 'power', 'water', 'service'];
        
        $titleLower = strtolower($title);
        
        foreach ($highPriorityKeywords as $keyword) {
            if (strpos($titleLower, $keyword) !== false) {
                return 'high';
            }
        }
        
        foreach ($mediumPriorityKeywords as $keyword) {
            if (strpos($titleLower, $keyword) !== false) {
                return 'medium';
            }
        }
        
        return 'low';
    }
    
    /**
     * Fallback simulated news data
     */
    private function getSimulatedNewsData() {
        $newsItems = [
            [
                'id' => 1,
                'title' => 'Local Traffic Advisory',
                'summary' => 'Road construction on Main Street causing delays. Alternative routes recommended.',
                'published_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'priority' => 'medium',
                'url' => '#',
                'source' => 'Local News'
            ],
            [
                'id' => 2,
                'title' => 'Community Health Update',
                'summary' => 'Free health screening available at the barangay health center this weekend.',
                'published_at' => date('Y-m-d H:i:s', strtotime('-4 hours')),
                'priority' => 'low',
                'url' => '#',
                'source' => 'Community Bulletin'
            ],
            [
                'id' => 3,
                'title' => 'Power Interruption Notice',
                'summary' => 'Scheduled power maintenance on October 15, 2025 from 8:00 AM to 12:00 PM.',
                'published_at' => date('Y-m-d H:i:s', strtotime('-6 hours')),
                'priority' => 'high',
                'url' => '#',
                'source' => 'Utility Company'
            ]
        ];
        
        return $newsItems;
    }
    
    /**
     * Get alerts for a specific category
     */
    public function getAlertsByCategory($category) {
        $allAlerts = $this->getAllAlerts();
        return array_filter($allAlerts, function($alert) use ($category) {
            return $alert['source'] === $category;
        });
    }
    
    /**
     * Get high priority alerts only
     */
    public function getHighPriorityAlerts() {
        $allAlerts = $this->getAllAlerts();
        return array_filter($allAlerts, function($alert) {
            return in_array($alert['type'], ['emergency', 'warning']) || 
                   ($alert['priority'] ?? 'low') === 'high';
        });
    }
    
    /**
     * Clear cache
     */
    public function clearCache() {
        $files = glob($this->cacheDir . '*.json');
        foreach ($files as $file) {
            unlink($file);
        }
    }
}

/**
 * Helper function to get real-time alerts
 */
function getRealtimeAlerts($location = 'Manila, Philippines') {
    $service = new RealtimeDataService();
    return $service->getAllAlerts($location);
}

/**
 * Helper function to get alerts by category
 */
function getAlertsByCategory($category, $location = 'Manila, Philippines') {
    $service = new RealtimeDataService();
    return $service->getAlertsByCategory($category);
}

/**
 * Helper function to get high priority alerts
 */
function getHighPriorityAlerts($location = 'Manila, Philippines') {
    $service = new RealtimeDataService();
    return $service->getHighPriorityAlerts();
}
?>
