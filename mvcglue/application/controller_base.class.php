<?php

Abstract Class baseController {

  /*
   * @registry object
   */
    protected $registry;
    protected $view;

    function __construct($registry) {
        $this->registry = $registry;
        $this->view = $this->registry->template;
    }

    /**
     * @all controllers must contain an index method
     */
    abstract function index();

    /**
     * Adds a variable to View.
     */
    public function addViewVar($var, $value) {
      $this->registry->template->$var = $value;
    }
}


