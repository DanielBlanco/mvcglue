<?php
/**
 * PDOStatement decorator that logs when a PDOStatement is
 * executed, and the time it took to run
 * @see LoggedPDO
 */
class LoggedPDOStatement {
/**
 * The PDOStatement we decorate
 */
    private $statement;

    public function __construct(PDOStatement $statement) {
        $this->statement = $statement;
    }

    /**
     * When execute is called record the time it takes and
     * then log the query
     * @return PDO result set
     */
    public function execute() {
        $start = microtime(true);
        $result = $this->statement->execute();
        $time = microtime(true) - $start;
        LoggedPDO::$log[] = array('query' => '[PS] ' . $this->statement->queryString,
            'time' => round($time * 1000, 3));
        return $result;
    }
    /**
     * Other than execute pass all other calls to the PDOStatement object
     * @param string $function_name
     * @param array $parameters arguments
     */
    public function __call($function_name, $parameters) {
        return call_user_func_array(array($this->statement, $function_name), $parameters);
    }
}