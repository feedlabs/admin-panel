<?php

abstract class AP_Site_Abstract extends CM_Site_Abstract {

    public function __construct() {
        parent::__construct();
        $this->_setModule('AP');
    }
}
