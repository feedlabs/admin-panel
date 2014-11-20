<?php

class AP_Component_Navigation extends AP_Component_Abstract {

    public function ajax_logout(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $redirectPage = 'AP_Page_Index';

        $response->getRequest()->getSession()->deleteUser();
        $response->getRequest()->getSession()->setLifetime();

        $response->redirect($redirectPage, null, true);
    }
}
