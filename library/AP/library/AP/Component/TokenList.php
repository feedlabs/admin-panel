<?php

class AP_Component_TokenList extends AP_Component_Abstract {

    public function ajax_deleteToken(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $token = $params->getString('token');

        // delete token

        $handler->message('Success: Token delete.');
        $response->reloadComponent();
    }
}
