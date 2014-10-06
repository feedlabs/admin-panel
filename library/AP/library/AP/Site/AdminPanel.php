<?php

class AP_Site_AdminPanel extends AP_Site_Abstract {

    public function __construct() {
        parent::__construct();
        $this->_setModule('AP');
    }

    public function getMenus() {
        return array(
            'main'    => new AP_Menu_Main(),
            'account' => new AP_Menu_Account(),
        );
    }
}
