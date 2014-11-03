<?php

class AP_Component_ApplicationFeedList extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $application = $this->getParams()->getArray('application');

        // todo: load application info over API
        // todo: load application feeds over API

        $viewResponse->set('application', $application);
    }

    public function ajax_deleteFeed(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $feedId = (string) $params->get('feedId');

        // delete feed

        $handler->message('Success: Feed delete.');
        $response->reloadComponent();
    }
}
