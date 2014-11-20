<?php

class AP_FormAction_SignIn_Process extends CM_FormAction_Abstract {

    protected function _getRequiredFields() {
        return array('login', 'password');
    }

    protected function _checkData(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        if ($response->getRequest()->getIpBlocked()) {
            $response->addError($response->getRender()->getTranslation('Your IP address is blocked.'));
        }
    }

    protected function _process(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        try {
            $user = AP_Model_User::authenticate($params->getString('login'), $params->getString('password'));
        } catch (CM_Exception_AuthFailed $e) {
            $response->addError($e->getMessagePublic($response->getRender()), 'password');
            return;
        }

        $response->getRequest()->getSession()->setUser($user);

        $response->reloadPage();
    }
}
