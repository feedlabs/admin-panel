#!/usr/bin/env php
<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
$bootloader = new CM_Bootloader(dirname(__DIR__) . '/');
$bootloader->load();

echo "test\n";

AP_Model_User::_createStatic([

]);
