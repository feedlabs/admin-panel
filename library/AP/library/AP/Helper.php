<?php

use \Feedlabs\Feedify\Resource\Application;
use \Feedlabs\Feedify\Resource\Feed;

class AP_Helper {

    /**
     * @param string $applicationId
     * @return Application
     */
    public static function getApplication($applicationId) {
        $client = new \Feedlabs\Feedify\Client('1', '2');
        return $client->getApplication($applicationId);
    }

    /**
     * @param string $applicationId
     * @param string $feedId
     * @return Feed
     */
    public static function getFeed($applicationId, $feedId) {
        $client = new \Feedlabs\Feedify\Client('1', '2');
        return $client->getFeed($applicationId, $feedId);
    }
}
