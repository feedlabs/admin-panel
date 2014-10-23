<?php

class AP_Menu_Browse extends CM_Menu {

    public function __construct() {
        parent::__construct(array(
            array('label' => 'Home', 'page' => 'AP_Page_Overview', 'icon' => 'home'),
            array('label' => 'Application', 'page' => 'AP_Page_ApplicationOverview', 'icon' => 'home', 'submenu' => array(
                array('label' => 'Application', 'page' => 'AP_Page_Application', 'viewable' => false),
            )),
            array('label' => 'Feed', 'page' => 'AP_Page_FeedOverview', 'icon' => 'home', 'submenu' => array(
                array('label' => 'Feed', 'page' => 'AP_Page_Feed', 'viewable' => false),
            )),
        ));
    }
}
