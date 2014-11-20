<?php

class AP_Component_TokenList extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $client = AP_Helper::getClient();
        $tokenList = $client->token->getList();

        $viewResponse->set('tokenList', $tokenList);
    }

    public function ajax_deleteToken(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $token = $params->getString('token');

        $client = AP_Helper::getClient();
        $client->token->delete($token);

        $handler->message('Success: Token delete.');
        $response->reloadComponent();
    }
}
