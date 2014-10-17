<?php

class AP_Site_AdminPanel extends AP_Site_Abstract {

    public function getMenus() {
        return array(
            'browse'  => new AP_Menu_Browse(),
            'account' => new AP_Menu_Account(),
            'about'   => new AP_Menu_About(),
        );
    }
}
