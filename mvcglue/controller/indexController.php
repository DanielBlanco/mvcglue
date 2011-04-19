<?php
/**
 * This is the main controller.
 */
Class indexController Extends baseController {

    /**
     * Index action.
     */
    public function index() {
        $this->view->show('index');
    }
}


