<?php

class AP_FormAction_Application_Create extends CM_FormAction_Abstract {

    protected function _getRequiredFields() {
        return array('name');
    }

    protected function _process(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        // todo: add new application over API

        $response->addMessage('Success: Application created.');
    }
}
