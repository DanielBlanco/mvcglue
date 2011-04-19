<?php
/**
 * Extends PDO and logs all queries that are executed and how long
 * they take, including queries issued via prepared statements
 */
class LoggedPDO extends PDO {
    public static $log = array();

    public function __construct($dsn, $username = null, $password = null, $extra_params=array()) {
        parent::__construct($dsn, $username, $password, $extra_params);
    }

    /**
     * Print out the log when we're destructed. I'm assuming this will
     * be at the end of the page. If not you might want to remove this
     * destructor and manually call LoggedPDO::printLog();
     */
    public function __destruct() {
        #self::printLog();
    }

    public function query($query) {
        $start = microtime(true);
        $result = parent::query($query);
        $time = microtime(true) - $start;
        LoggedPDO::$log[] = array('query' => $query,
            'time' => round($time * 1000, 3));
        return $result;
    }

    /**
     * @return LoggedPDOStatement
     */
    public function prepare($query) {
        return new LoggedPDOStatement(parent::prepare($query));
    }

    public static function printLog() {
        $totalTime = 0;
        echo '<table border=1><tr><th>Query</th><th>Time (ms)</th></tr>';
        foreach(self::$log as $entry) {
            $totalTime += $entry['time'];
            echo '<tr><td>' . $entry['query'] . '</td><td>' . $entry['time'] . '</td></tr>';
        }
        echo '<tr><th>' . count(self::$log) . ' queries</th><th>' . $totalTime . '</th></tr>';
        echo '</table>';
    }
}

