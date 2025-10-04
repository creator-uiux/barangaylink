<?php
/**
 * PHP Hooks/Functions - Equivalent to React Hooks
 * Provides functionality similar to React hooks but for PHP
 */

/**
 * Real-time functionality - PHP equivalent of useRealTime hook
 * @return array
 */
function useRealTime() {
    $currentTime = new DateTime();
    
    return [
        'currentTime' => $currentTime,
        'formattedTime' => $currentTime->format('g:i A'),
        'formattedDate' => $currentTime->format('l, F j, Y'),
        'formattedDateTime' => $currentTime->format('D, M j, g:i A'),
        'timeAgo' => function($datetime) {
            return timeAgo($datetime);
        }
    ];
}

/**
 * Performance optimization functions - PHP equivalent of usePerformance hook
 * @return array
 */
function usePerformance() {
    return [
        'isEqual' => function($a, $b) {
            return $a === $b || (is_array($a) && is_array($b) && array_diff_assoc($a, $b) === [] && array_diff_assoc($b, $a) === []);
        },
        'debounce' => function($callback, $delay) {
            // PHP doesn't have built-in debounce, but we can simulate with session-based timing
            static $lastCall = 0;
            $now = microtime(true);
            if ($now - $lastCall >= $delay / 1000) {
                $lastCall = $now;
                return call_user_func($callback);
            }
            return null;
        },
        'throttle' => function($callback, $delay) {
            static $lastCall = 0;
            $now = microtime(true);
            if ($now - $lastCall >= $delay / 1000) {
                $lastCall = $now;
                return call_user_func($callback);
            }
            return null;
        }
    ];
}

/**
 * Local storage functionality - PHP equivalent using sessions
 * @return array
 */
function useLocalStorage() {
    return [
        'getItem' => function($key, $defaultValue = null) {
            return $_SESSION[$key] ?? $defaultValue;
        },
        'setItem' => function($key, $value) {
            $_SESSION[$key] = $value;
            return true;
        },
        'removeItem' => function($key) {
            unset($_SESSION[$key]);
            return true;
        }
    ];
}

/**
 * Refresh data functionality - PHP equivalent of useRefreshData
 * @param int $intervalMs Interval in milliseconds (not used in PHP, but kept for compatibility)
 * @return int
 */
function useRefreshData($intervalMs = 30000) {
    // In PHP, we can use a timestamp-based approach
    static $refreshTrigger = 0;
    $refreshTrigger = time();
    return $refreshTrigger;
}

/**
 * State management - PHP equivalent of useState
 * Uses session to maintain state across requests
 * @param string $key State key
 * @param mixed $initialValue Initial value
 * @return array [value, setter]
 */
function useState($key, $initialValue = null) {
    if (!isset($_SESSION['state'][$key])) {
        $_SESSION['state'][$key] = $initialValue;
    }
    
    return [
        $_SESSION['state'][$key],
        function($newValue) use ($key) {
            $_SESSION['state'][$key] = $newValue;
        }
    ];
}

/**
 * Effect hook equivalent - PHP version
 * Executes callback and stores dependencies to check for changes
 * @param callable $callback
 * @param array $dependencies
 */
function useEffect($callback, $dependencies = []) {
    $key = md5(serialize($dependencies));
    
    if (!isset($_SESSION['effects'][$key])) {
        $_SESSION['effects'][$key] = $dependencies;
        call_user_func($callback);
    } else {
        $prevDeps = $_SESSION['effects'][$key];
        if ($prevDeps !== $dependencies) {
            $_SESSION['effects'][$key] = $dependencies;
            call_user_func($callback);
        }
    }
}

/**
 * Callback hook equivalent - PHP version
 * Memoizes a callback function
 * @param callable $callback
 * @param array $dependencies
 * @return callable
 */
function useCallback($callback, $dependencies = []) {
    static $memoized = [];
    $key = md5(serialize($dependencies));
    
    if (!isset($memoized[$key])) {
        $memoized[$key] = $callback;
    }
    
    return $memoized[$key];
}

/**
 * Memo hook equivalent - PHP version
 * Memoizes a computed value
 * @param callable $factory
 * @param array $dependencies
 * @return mixed
 */
function useMemo($factory, $dependencies = []) {
    static $memoized = [];
    $key = md5(serialize($dependencies));
    
    if (!isset($memoized[$key])) {
        $memoized[$key] = call_user_func($factory);
    }
    
    return $memoized[$key];
}

/**
 * Ref hook equivalent - PHP version
 * Creates a mutable reference
 * @param mixed $initialValue
 * @return array
 */
function useRef($initialValue = null) {
    static $refs = [];
    static $counter = 0;
    
    $id = 'ref_' . $counter++;
    if (!isset($refs[$id])) {
        $refs[$id] = ['current' => $initialValue];
    }
    
    return $refs[$id];
}

/**
 * Reducer hook equivalent - PHP version
 * @param callable $reducer
 * @param mixed $initialState
 * @return array [state, dispatch]
 */
function useReducer($reducer, $initialState) {
    static $state = null;
    
    if ($state === null) {
        $state = $initialState;
    }
    
    $dispatch = function($action) use ($reducer, &$state) {
        $state = call_user_func($reducer, $state, $action);
    };
    
    return [$state, $dispatch];
}

/**
 * Context equivalent - PHP version using sessions
 * @param string $contextName
 * @param mixed $value
 */
function createContext($contextName, $value = null) {
    if ($value !== null) {
        $_SESSION['contexts'][$contextName] = $value;
    }
    
    return $_SESSION['contexts'][$contextName] ?? null;
}

/**
 * Use context equivalent - PHP version
 * @param string $contextName
 * @return mixed
 */
function useContext($contextName) {
    return $_SESSION['contexts'][$contextName] ?? null;
}

/**
 * Custom hook for form handling
 * @param array $initialValues
 * @return array
 */
function useForm($initialValues = []) {
    $formData = $_POST + $initialValues;
    
    return [
        'values' => $formData,
        'handleChange' => function($name, $value) use (&$formData) {
            $formData[$name] = $value;
        },
        'handleSubmit' => function($callback) use ($formData) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                return call_user_func($callback, $formData);
            }
            return false;
        },
        'reset' => function() use ($initialValues, &$formData) {
            $formData = $initialValues;
        }
    ];
}

/**
 * Custom hook for pagination
 * @param array $items
 * @param int $itemsPerPage
 * @return array
 */
function usePagination($items, $itemsPerPage = 10) {
    $currentPage = (int)($_GET['page'] ?? 1);
    $totalItems = count($items);
    $totalPages = ceil($totalItems / $itemsPerPage);
    $offset = ($currentPage - 1) * $itemsPerPage;
    $currentItems = array_slice($items, $offset, $itemsPerPage);
    
    return [
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'totalItems' => $totalItems,
        'currentItems' => $currentItems,
        'hasNext' => $currentPage < $totalPages,
        'hasPrev' => $currentPage > 1,
        'nextPage' => min($currentPage + 1, $totalPages),
        'prevPage' => max($currentPage - 1, 1)
    ];
}

/**
 * Custom hook for search functionality
 * @param array $items
 * @param string $searchKey
 * @param array $searchFields
 * @return array
 */
function useSearch($items, $searchKey = 'search', $searchFields = []) {
    $searchTerm = $_GET[$searchKey] ?? '';
    
    if (empty($searchTerm)) {
        return $items;
    }
    
    return array_filter($items, function($item) use ($searchTerm, $searchFields) {
        foreach ($searchFields as $field) {
            if (isset($item[$field]) && stripos($item[$field], $searchTerm) !== false) {
                return true;
            }
        }
        return false;
    });
}
?>