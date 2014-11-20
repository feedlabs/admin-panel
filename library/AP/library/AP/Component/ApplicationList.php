<?php

class AP_Component_ApplicationList extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $client = AP_Helper::getClient();
        $applicationList = $client->application->getList();

        $viewResponse->set('applicationList', $applicationList);
    }

    public function ajax_deleteApplication(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $applicationId = $params->getString('applicationId');

        $client = AP_Helper::getClient();
        $client->application->delete($applicationId);

        $handler->message('Success: Application delete.');
        $response->reloadComponent();
    }
}
