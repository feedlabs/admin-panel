<?php

class AP_Menu_About extends CM_Menu {

    public function __construct() {
        parent::__construct(array(
            array('label' => 'About', 'page' => 'AP_Page_About'),
            array('label' => 'Contact', 'page' => 'AP_Page_Contact'),
            array('label' => 'Feedback', 'page' => 'AP_Page_Feedback'),
            array('label' => 'Terms', 'page' => 'AP_Page_Terms'),
        ));
    }
}
