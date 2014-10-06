<?php

return function (CM_Config_Node $config) {

    $config->CM_Site_Abstract->class = 'AP_Site_AdminPanel';
    $config->AP_Site_AdminPanel->name = 'Feedlabs | Admin Panel';
};
