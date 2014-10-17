<?php

class AP_FormAction_Logout_Process extends CM_FormAction_Abstract {

    protected function _process(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        $redirectPage = $params->get('redirectPage');

        $response->getRequest()->getSession()->deleteUser();
        $response->getRequest()->getSession()->setLifetime();

        $response->redirect($redirectPage, null, true);
    }
}
