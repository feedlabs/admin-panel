<?php

class AP_Component_FeedList extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $client = AP_Helper::getClient();
        $applicationList = $client->application->getList();

        $viewResponse->set('applicationList', $applicationList);
    }
}
