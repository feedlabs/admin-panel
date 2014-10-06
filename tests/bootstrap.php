<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$bootloader = new CM_Bootloader_Testing(dirname(__DIR__) . '/');
$bootloader->load();

$suite = new CMTest_TestSuite();
$suite->bootstrap();
