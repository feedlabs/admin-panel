<?php

class AP_FormAction_Feed_Save extends CM_FormAction_Abstract {

    protected function _getRequiredFields() {
        return array('applicationId', 'name');
    }

    protected function _process(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        $application = AP_Helper::getApplication($params->getString('applicationId'));

        // todo: update application over API

        $response->addMessage('Success: Feed saved.');
    }
}
