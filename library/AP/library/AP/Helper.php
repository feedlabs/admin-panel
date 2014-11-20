<?php

use Feedlabs\Feedify\Client;
use \Feedlabs\Feedify\Resource\Application;
use \Feedlabs\Feedify\Resource\Feed;

class AP_Helper {

    /** @var Client */
    private static $_client;

    /**
     * @return Client
     */
    public static function getClient() {
        if (!static::$_client) {
            static::$_client = new Client('1');
        }
        return static::$_client;
    }

    /**
     * @param string $applicationId
     * @return Application
     */
    public static function getApplication($applicationId) {
        $client = static::getClient();
        return $client->application->get($applicationId);
    }

    /**
     * @param string $applicationId
     * @param string $feedId
     * @return Feed
     */
    public static function getFeed($applicationId, $feedId) {
        $client = static::getClient();
        return $client->feed->get($applicationId, $feedId);
    }
}
