<?php
class AdminLogger {
    private static $logFile = 'admin_activity.log';
    
    public static function log($admin_id, $activity_type, $description) {
        $logEntry = sprintf(
            "[%s] Admin ID: %d | Activity: %s | Description: %s\n",
            date('Y-m-d H:i:s'),
            $admin_id,
            $activity_type,
            $description
        );
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND);
    }
    
    public static function getLogs($limit = 100) {
        if (!file_exists(self::$logFile)) {
            return [];
        }
        
        $lines = file(self::$logFile);
        $lines = array_reverse($lines);
        return array_slice($lines, 0, $limit);
    }
}
?>
