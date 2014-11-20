<?php

return function (CM_Config_Node $config) {

    $config->AP_Site_AdminPanel->url = 'http://www.admin-panel.dev';
    $config->AP_Site_AdminPanel->urlCdn = 'http://origin-www.admin-panel.dev';

    $config->CM_Mail->send = false;
    $config->CM_Redis_Client->server = array('host' => '127.0.0.1', 'port' => 6379);
    $config->CM_Stream_Adapter_Message_SocketRedis->servers = array(
        array('httpHost' => 'localhost', 'httpPort' => 8085, 'sockjsUrls' => array('http://www.admin-panel.dev:8090')),
    );
    $config->CM_Elasticsearch_Client->servers = array(
        array('host' => '127.0.0.1', 'port' => 9200),
    );
    $config->CM_Memcache_Client->servers = array(
        array('host' => '127.0.0.1', 'port' => 11211),
    );
    $config->services['database-master'] = array(
        'class'     => 'CM_Db_Client',
        'arguments' => array(array(
            'host'             => '127.0.0.1',
            'port'             => 3306,
            'username'         => 'root',
            'password'         => '',
            'db'               => 'adminPanel',
            'reconnectTimeout' => 300
        ))
    );
    $config->CM_Jobdistribution_JobWorker->servers = array(
        array('host' => 'localhost', 'port' => 4730),
    );
    $config->CM_Jobdistribution_Job_Abstract->servers = array(
        array('host' => 'localhost', 'port' => 4730),
    );

    $config->services['usercontent'] = array(
        'class'     => 'CM_Service_UserContent',
        'arguments' => array(array(
            'default' => array(
                'filesystem' => 'filesystem-usercontent',
                'url'        => 'http://origin-www.admin-panel.dev/userfiles',
            )
        )));
};
