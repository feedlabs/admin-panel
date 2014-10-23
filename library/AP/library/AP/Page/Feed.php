<?php

class AP_Page_Feed extends CM_Page_Abstract {

    public function ajax_deleteEntry(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $entryId = (string) $params->get('entryId');

        // delete entry

        $response->reloadComponent();
    }
}
