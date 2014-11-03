<?php

class AP_Component_FeedEntryList extends AP_Component_Abstract {
    public function ajax_deleteEntry(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $entryId = (string) $params->get('entryId');

        // delete entry

        $handler->message('Success: FeedEntry delete.');
        $response->reloadComponent();
    }
}
