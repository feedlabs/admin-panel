<?php

class AP_FormAction_Token_Create extends CM_FormAction_Abstract {

    protected function _getRequiredFields() {
        return array('name');
    }

    protected function _process(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        $name = $params->getString('name');

        $client = AP_Helper::getClient();
        $client->token->create($name);

        $response->addMessage('Success: New Token has been added.');
        $response->reloadComponent();
    }
}
