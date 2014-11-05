<?php

class AP_Component_ApplicationView extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $applicationId = $this->getParams()->getString('application');
        $application = AP_Helper::getApplication($applicationId);

        $viewResponse->set('application', $application);
    }

    public function ajax_deleteApplication(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $application = AP_Helper::getApplication($params->getString('applicationId'));

        // delete application

        $handler->message('Success: Application delete.');
        $response->redirect('AP_Page_ApplicationOverview');
    }
}
