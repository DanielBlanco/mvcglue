<?php
/**
 * Controller for 404 page.
 */
Class error404Controller Extends baseController {

    /**
     * Index action.
     */
    public function index() {

        /*** Set the message ***/
        $this->registry->template->message = '404 - Page not found!!!';

        /*** load the 404 template ***/
        $this->registry->template->show('error404');
    }

}

