<?php

abstract class APTest_TestCase extends CMTest_TestCase {

    public function runBare() {
            $siteDefault = $this->getMockSite('AP_Site_Abstract', null, array(
                'url'                    => 'http://www.default.dev',
                'urlCdn'                 => 'http://cdn.default.dev',
                'name'                   => 'Default',
            ));
            CM_Config::get()->CM_Site_Abstract->class = get_class($siteDefault);
        parent::runBare();
    }
}
