<?php

class AP_Component_ApplicationView extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $applicationId = $this->getParams()->getString('application');

        $application = ['id' => '1', 'createStamp' => '1415020135', 'name' => 'test1', 'description' => 'jsadfghhjsadgfsajh'];

        // todo: load application info over API
        // todo: load application feeds over API

        $viewResponse->set('application', $application);
    }
}
