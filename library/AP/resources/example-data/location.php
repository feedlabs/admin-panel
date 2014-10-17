<?php

/** @var AP_App_Cli $this */
$this->_getStreamOutput()->writeln('Creating location...');

$country = CM_Model_Location::createCountry('United Kingdom', 'GB');
$london = CM_Model_Location::createCity($country, 'London', 51.32, 0.05, '114984');
$liverpool = CM_Model_Location::createCity($country, 'Liverpool', 53.25, -3.00, '33446');
CM_Model_Location::createAggregation();
