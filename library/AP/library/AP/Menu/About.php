<?php

class AP_Menu_About extends CM_Menu {

    public function __construct() {
        parent::__construct(array(
            array('label' => 'About', 'page' => 'AP_Page_About', 'icon' => 'home'),
            array('label' => 'Contact', 'page' => 'AP_Page_Contact', 'icon' => 'home'),
            array('label' => 'Feedback', 'page' => 'AP_Page_Feedback', 'icon' => 'home'),
            array('label' => 'Terms', 'page' => 'AP_Page_Terms', 'icon' => 'home'),
        ));
    }
}
