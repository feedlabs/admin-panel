<?php

class AP_Component_ApplicationList extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $applicationList = [
            '1' => ['id' => '1', 'createStamp' => '1415020135', 'name' => 'test1', 'description' => 'jsadfghhjsadgfsajh'],
            '2' => ['id' => '2', 'createStamp' => '1415020135', 'name' => 'test2', 'description' => 'jsadfghhjsadgfsajh'],
            '3' => ['id' => '3', 'createStamp' => '1415020135', 'name' => 'test3', 'description' => 'jsadfghhjsadgfsajh'],
        ];

        // todo: load applicationList info over API

        $viewResponse->set('applicationList', $applicationList);
    }
}
