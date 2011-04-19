<?php

Class Template {

  /*
   * @the registry
   * @access private
   */
    private $registry;

  /*
   * @Variables array
   * @access private
   */
    private $vars = array();

    /**
     *
     * @constructor
     *
     * @access public
     *
     * @return void
     *
     */
    function __construct($registry) {
        $this->registry = $registry;
    }


    /**
     *
     * @set undefined vars
     *
     * @param string $index
     *
     * @param mixed $value
     *
     * @return void
     *
     */
    public function __set($index, $value) {
        $this->vars[$index] = $value;
    }


    function show($name) {
        $_path = __SITE_PATH . '/views' . '/' . $name . '.php';
        $_head_path = __SITE_PATH . '/views' . '/' . $name . '.head.php';
        $_i18n_path = __SITE_PATH . '/views' . '/' . $name . '.i18n.php';

        if (file_exists($_path) == false) {
            throw new Exception('Template not found in '. $_path);
            return false;
        }

        // Load variables
        foreach ($this->vars as $key => $value) {
            $$key = $value;
        }

        ob_start();
        if (file_exists($_i18n_path)) {
            include_once ($_i18n_path);
        }
        if (isset($layout)) {
            $_layout_path = __SITE_PATH . '/views/layouts/' . $layout . '.php';
            if (file_exists($_layout_path)) {
                include ($_layout_path);
            } else {
                include ($_path);
            }
        } else {
            include ($_path);
        }
        ob_end_flush();
    }

    function is_var_set($var_name) {
        return isset($this->vars[$var_name]);
    }
}
