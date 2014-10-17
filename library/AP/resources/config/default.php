<?php

return function (CM_Config_Node $config) {

    $config->installationName = '';

    $config->AP_PaymentProvider_Abstract->withoutRemoteConnection = false;
    $config->AP_PaymentProvider_Abstract->supportEmailAddress = 'support@example.com';

    $config->AP_Page_About_Affiliate->affiliateUrl = 'http://www.cummission.com';

    $config->AP_EntityProvider_Abstract->eMailSuffix = 'example.com';

    $config->AP_ModelAsset_User_Reviews->processPendingEnabled = true;

    $config->AP_EntityProvider_Abstract->processAllEnable = false;

    $config->AP_Entertainment_Schedule_Abstract->executingEnabled = true;

    $config->SKService_AdultCentro_Client_Abstract->authUser = '';
    $config->SKService_AdultCentro_Client_Abstract->urlBase = '';
    $config->SKService_AdultCentro_Client_Abstract->password = '';
    $config->SKService_AdultCentro_Client_Abstract->secureForHash = '';

    $config->AP_AffiliateProvider_Internal->enableTracking = false;
    $config->AP_AffiliateProvider_Offerit->enableTracking = false;
    $config->AP_AffiliateProvider_UserTemplate->enableTracking = false;

    $config->SKService_Offerit_Client->url = '';
    $config->SKService_Offerit_Client->chargebackCost = 25.00;
    $config->SKService_Offerit_Client->voidCost = null;
    $config->SKService_Offerit_Client->refundCost = null;

    $config->SKService_Rocketgate_Client_Abstract->testMode = false;

    $config->SKService_Wts_Client_Sftp->backupFiles = false;

    $config->SKService_Streamate_Redirect_Client->providers = array();
    $config->SKService_Streamate_Abstract->url = '';

    $config->AP_Entertainment_UserTemplate->entertainerCountMin = 50000;

    $config->CM_Model_User->class = 'AP_Model_User';

    $config->CM_Usertext_Usertext->class = 'AP_Model_Usertext_Usertext';

    $config->CM_Response_Page->catch = array(
        'CM_Exception_Nonexistent'       => '/error/not-found',
        'CM_Exception_InvalidParam'      => '/error/not-found',
        'CM_Exception_AuthRequired'      => '/error/auth-required',
        'CM_Exception_NotAllowed'        => '/error/not-allowed',
        'AP_Exception_PremiumRequired'   => '/account/premium',
        'AP_Exception_InsufficientFunds' => '/account/coins',
    );

    $config->CM_Response_View_Abstract->catch = array(
        'CM_Exception_Nonexistent',
        'CM_Exception_AuthRequired',
        'CM_Exception_NotAllowed',
        'CM_Exception_Blocked',
        'CM_Exception_ActionLimit',
        'AP_Exception_PremiumRequired',
        'AP_Exception_InsufficientFunds',
    );

    $config->CM_Response_RPC->catch = array(
        'CM_Exception_AuthRequired',
    );

    $config->CM_Adprovider->zones = array(
        'square-medium'           => array('adapter' => 'CM_AdproviderAdapter_Openx', 'zoneId' => 4),
        'square-medium-lower'     => array('adapter' => 'CM_AdproviderAdapter_Openx', 'zoneId' => 14),
        'square-medium-bottom'    => array('adapter' => 'CM_AdproviderAdapter_Openx', 'zoneId' => 8),
        'leaderboard'             => array('adapter' => 'CM_AdproviderAdapter_Openx', 'zoneId' => 6),
        'doodle'                  => array('adapter' => 'CM_AdproviderAdapter_Openx', 'zoneId' => 10),
        'live-girls'              => array('adapter' => 'CM_AdproviderAdapter_Openx', 'zoneId' => 19),
        'floating'                => array('adapter' => 'CM_AdproviderAdapter_Openx', 'zoneId' => 18),
        'square-medium-photoview' => array('adapter' => 'CM_AdproviderAdapter_Openx', 'zoneId' => 23),
    );

    $config->CM_AdproviderAdapter_Abstract->class = 'CM_AdproviderAdapter_Openx';
    $config->CM_AdproviderAdapter_Openx->host = 'ads.fuckbook.com';

    $config->services['tracking-googleanalytics'] = array(
        'class'     => 'SKService_GoogleAnalytics_Client',
        'arguments' => array('my-web-property-id')
    );

    $config->duplicateConversationsInMongoDB = false;
};
