<?php

class AP_FormAction_Feed_Create extends CM_FormAction_Abstract {

    protected function _getRequiredFields() {
        return array('application', 'name');
    }

    protected function _process(CM_Params $params, CM_Response_View_Form $response, CM_Form_Abstract $form) {
        $application = (string) $params->get('application');
        $name = $params->getString('name');

        // todo: save to feedify

        $response->addMessage('Success: New Feed has been added.');
        $response->reloadComponent();
    }
}
