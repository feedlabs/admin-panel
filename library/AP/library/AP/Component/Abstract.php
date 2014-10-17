<?php

abstract class AP_Component_Abstract extends CM_Component_Abstract {

    /** @var AP_Params $_params */
    protected $_params;

    /**
     * @throws CM_Exception_AuthRequired
     * @throws AP_Exception_PremiumRequired
     * @throws CM_Exception_Nonexistent
     */
    public function checkAccessible(CM_Frontend_Environment $environment) {
    }

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
    }
}
