<?php

class AP_Menu_Main extends CM_Menu {

    public function __construct() {
        parent::__construct(array(
            array('label' => 'Overview', 'page' => 'AP_Page_Index'),
            array('label' => 'Application', 'page' => 'AP_Page_Application'),
            array('label' => 'Feed', 'page' => 'AP_Page_Feed'),
        ));
    }
}
