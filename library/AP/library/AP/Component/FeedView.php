<?php

class AP_Component_FeedView extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $applicationId = $this->getParams()->getString('application');
        $application = AP_Helper::getApplication($applicationId);
        $feedId = $this->getParams()->getString('feed');
        $feed = AP_Helper::getFeed($applicationId, $feedId);

        $viewResponse->set('application', $application);
        $viewResponse->set('feed', $feed);
    }

    public function ajax_deleteFeed(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $applicationId = $params->getString('applicationId');
        $feedId = $params->getString('feedId');

        $client = AP_Helper::getClient();
        $client->feed->delete($applicationId, $feedId);

        $handler->message('Success: Feed delete.');
        $response->redirect('AP_Page_FeedOverview');
    }
}
