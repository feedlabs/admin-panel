<?php

class AP_Component_AuthRequired extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $hadViewer = $this->_params->getBoolean('hadViewer', false);

        $viewResponse->set('hadViewer', $hadViewer);
    }
}
