<?php

// Production-ready error handling
class ProductionErrorHandler {
    public static function handleException($exception) {
        // Log the error
        self::logError($exception);
        
        // Show user-friendly error page
        http_response_code(500);
        include __DIR__ . '/../views/errors/500.php';
        exit;
    }
    
    public static function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        self::logError(new ErrorException($errstr, 0, $errno, $errfile, $errline));
        return true;
    }
    
    private static function logError($exception) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
        ];
        
        $logFile = __DIR__ . '/../storage/logs/production.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logEntry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

// Set up error handling for production
if (ConfigSimple::getInstance()->get('app.env') === 'production') {
    set_exception_handler(['ProductionErrorHandler', 'handleException']);
    set_error_handler(['ProductionErrorHandler', 'handleError']);
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_STRICT);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../storage/logs/php_errors.log');
}
?>
