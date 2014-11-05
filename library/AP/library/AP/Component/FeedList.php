<?php

class AP_Component_FeedList extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $client = new \Feedlabs\Feedify\Client('1', '2');
        $applicationList = $client->getApplicationList();

        $viewResponse->set('applicationList', $applicationList);
    }
}
