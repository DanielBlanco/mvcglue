<?php
/**
 * Singleton to obtain DB connection.
 */
class db {

  /*** Declare instance ***/
    private static $instance = NULL;

    /**
     *
     * the constructor is set to private so
     * so nobody can create a new instance using new
     *
     */
    private function __construct() {
    }

    /**
     *
     * Return DB instance or create intitial connection
     *
     * @return object (PDO)
     *
     * @access public
     *
     */
    public static function getInstance($dsn, $user, $password, $new_connection=false) {

        if (!self::$instance || $new_connection) {
            self::$instance = new LoggedPDO($dsn, $user, $password);
            self::$instance->setAttribute(LoggedPDO::ATTR_ERRMODE, LoggedPDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }

    /**
     *
     * Like the constructor, we make __clone private
     * so nobody can clone the instance
     *
     */
    private function __clone() {
    }

} /*** end of class ***/


