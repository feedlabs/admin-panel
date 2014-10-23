<?php

class AP_Page_FeedOverview extends CM_Page_Abstract {

    public function ajax_deleteFeed(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $feedId = (string) $params->get('feedId');

        // delete feed

        $response->reloadComponent();
    }
}
