<?php

class AP_Component_ChangePassword extends AP_Component_Abstract {

    public function checkAccessible(CM_Frontend_Environment $environment) {
        $this->_checkViewer($environment);
    }
}
