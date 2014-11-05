<?php

class AP_Component_FeedEntryList extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        /** @var \Feedlabs\Feedify\Resource\Feed $feed */
        $application = $this->getParams()->get('application');
        $feed = $this->getParams()->get('feed');

        $viewResponse->set('application', $application);
        $viewResponse->set('feed', $feed);
        $viewResponse->set('entryList', $feed->getEntryList());
    }

    public function ajax_deleteEntry(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $application = AP_Helper::getApplication($params->getString('applicationId'));
        $feed = AP_Helper::getFeed($params->getString('applicationId'), $params->getString('feedId'));
        $entryId = (string) $params->get('entryId');

        // delete entry

        $handler->message('Success: Entry delete.');
        $response->reloadComponent(['application' => $application, 'feed' => $feed]);
    }
}
