<?php

class AP_FormAction_Application_Save extends CM_FormAction_Abstract {

    protected function _getRequiredFields() {
        return array('applicationId', 'name');
    }

    protected function _process(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        $applicationId = $params->getString('applicationId');
        $name = $params->getString('name');
        $description = $params->has('description') ? $params->getString('description') : null;

        $client = AP_Helper::getClient();
        $client->application->update($applicationId, $name, $description);

        $response->addMessage('Success: Application saved.');
    }
}
