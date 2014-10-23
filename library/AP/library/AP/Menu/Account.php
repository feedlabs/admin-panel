<?php

class AP_Menu_Account extends CM_Menu {

    public function __construct() {
        parent::__construct(array(
            array('label' => 'Settings', 'page' => 'AP_Page_Settings', 'icon' => 'home'),
        ));
    }
}
