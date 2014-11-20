<?php

class AP_FormAction_Feed_Create extends CM_FormAction_Abstract {

    protected function _getRequiredFields() {
        return array('applicationId', 'name');
    }

    protected function _process(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        $applicationId = $params->getString('applicationId');
        $name = $params->getString('name');
        $description = $params->has('description') ? $params->getString('description') : null;
        $tagList = $params->has('tagList') ? $params->getArray('tagList') : null;

        $client = AP_Helper::getClient();
        $client->feed->create($applicationId, $name, $description, $tagList);

        $response->addMessage('Success: New Feed has been added.');
    }
}
