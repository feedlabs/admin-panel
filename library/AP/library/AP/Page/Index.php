<?php

class AP_Page_Index extends CM_Page_Abstract {

    public function prepareResponse(CM_Frontend_Environment $environment, CM_Response_Page $response) {
        if ($environment->getViewer()) {
            $response->redirect('AP_Page_Overview');
            return;
        }
    }

    public function getLayout(CM_Frontend_Environment $environment, $layoutName = null) {
        if (null === $layoutName) {
            $layoutName = 'Index';
        }
        return parent::getLayout($environment, $layoutName);
    }
}
