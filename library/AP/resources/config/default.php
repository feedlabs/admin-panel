<?php

return function (CM_Config_Node $config) {

    $config->installationName = '';

    $config->CM_Model_User->class = 'AP_Model_User';

    $config->CM_Response_Page->catch = array(
        'CM_Exception_Nonexistent'       => '/error/not-found',
        'CM_Exception_InvalidParam'      => '/error/not-found',
        'CM_Exception_AuthRequired'      => '/error/auth-required',
        'CM_Exception_NotAllowed'        => '/error/not-allowed',
    );

    $config->CM_Response_View_Abstract->catch = array(
        'CM_Exception_Nonexistent',
        'CM_Exception_AuthRequired',
        'CM_Exception_NotAllowed',
        'CM_Exception_Blocked',
        'CM_Exception_ActionLimit',
    );

    $config->CM_Response_RPC->catch = array(
        'CM_Exception_AuthRequired',
    );
};
