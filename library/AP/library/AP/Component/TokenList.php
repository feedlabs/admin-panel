<?php

class AP_Component_TokenList extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $client = new \Feedlabs\Feedify\Client('1','2');
        $tokenList = $client->getTokenList();

        $viewResponse->set('tokenList', $tokenList);
    }

    public function ajax_deleteToken(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $token = $params->getString('token');

        // todo: delete token over API

        $handler->message('Success: Token delete.');
        $response->reloadComponent();
    }
}
