<?php

class AP_FormAction_AccountUser_Save extends CM_FormAction_Abstract {

    protected function _getRequiredFields() {
        return array('email');
    }

    protected function _checkData(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        $user = $response->getViewer(true);
        $email = $params->getString('email');
        if ($email != $user->getEmail()) {
            if (AP_Model_User::findEmail($email)) {
                $response->addError('This email address is already used.', 'email');
            }
        }
    }

    protected function _process(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        $email = $params->getString('email');
        /** @var AP_Model_User $viewer */
        $viewer = $response->getViewer(true);
        $viewer->setEmail($email);

        // todo: update userinfo over API

        $response->addMessage('Success: Account infos save.');
        $response->reloadComponent();
    }
}
