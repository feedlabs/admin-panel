<?php

abstract class AP_Component_Abstract extends CM_Component_Abstract {

    /** @var CM_Params $_params */
    protected $_params;

    /**
     * @param CM_Frontend_Environment $environment
     */
    public function checkAccessible(CM_Frontend_Environment $environment) {
    }

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
    }
}
