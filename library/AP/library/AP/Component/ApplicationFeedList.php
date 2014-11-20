<?php

class AP_Component_ApplicationFeedList extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $application = $this->getParams()->get('application');

        $viewResponse->set('application', $application);
        $viewResponse->set('feedList', $application->getFeedList());
    }

    public function ajax_deleteFeed(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $applicationId = $params->getString('applicationId');
        $feedId = (string) $params->get('feedId');

        $client = AP_Helper::getClient();
        $client->feed->delete($applicationId, $feedId);

        $handler->message('Success: Feed delete.');
        $application = AP_Helper::getApplication($applicationId);
        $response->reloadComponent(['application' => $application]);
    }
}
