<?php

class AP_FormAction_Feed_Save extends CM_FormAction_Abstract {

    protected function _getRequiredFields() {
        return array('applicationId', 'feedId', 'name');
    }

    protected function _process(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        $applicationId = $params->getString('applicationId');
        $feedId = $params->getString('feedId');
        $name = $params->getString('name');
        $description = $params->has('description') ? $params->getString('description') : null;
        $tagList = $params->has('tagList') ? $params->getArray('tagList') : null;

        $client = AP_Helper::getClient();
        $client->feed->update($applicationId, $feedId, $name, $description, $tagList);

        $response->addMessage('Success: Feed saved.');
    }
}
