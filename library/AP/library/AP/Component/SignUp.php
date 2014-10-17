<?php

class AP_Component_SignUp extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $signUpParams = $this->getParams()->getArray('signUpParams', array());

        $viewResponse->set('signUpParams', $signUpParams);
    }
}

