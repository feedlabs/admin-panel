<?php

class AP_FormAction_ChangePassword_Process extends CM_FormAction_Abstract {

    protected function _getRequiredFields() {
        return array('old_password', 'new_password', 'new_password_confirm');
    }

    protected function _checkData(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        /** @var AP_Model_User $user */
        $user = $response->getViewer(true);

        if (!AP_App_Auth::checkLogin($user->getUsername(), $params->getString('old_password'))) {
            $response->addError('Incorrect old password.', 'old_password');
        } else {
            if ($params->getString('new_password') != $params->getString('new_password_confirm')) {
                $response->addError('Passwords do not match.', 'new_password');
            }
        }
    }

    protected function _process(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        /** @var AP_Model_User $user */
        $user = $response->getViewer(true);
        $user->setPassword($params->getString('new_password'));
        $response->addMessage('Success: Password changed.');
        $response->popinComponent();
    }
}
