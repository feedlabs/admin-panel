<?php

class SK_AffiliateProvider_OfferitTest extends SKTest_TestCase {

    protected function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testProvider() {
        $provider = new SK_AffiliateProvider_Offerit();
        $provider->setName('foo');
        $provider->setQueryParam('bar');

        $this->assertSame(SK_AffiliateProvider_Offerit::getTypeStatic(), $provider->getId());
        $this->assertSame('foo', $provider->getName());
        $this->assertSame('bar', $provider->getQueryParam());
    }

    public function testNewAffiliateCode() {
        /** @var SK_Site_Abstract $site */
        $site = $this->getMockSite('SK_Site_Abstract');
        $affiliateProvider = new SK_AffiliateProvider_Offerit();
        /** @var $affiliate SK_Model_Affiliate */
        $request = new CM_Request_Get('/foo?' . $affiliateProvider->getQueryParam() . '=bar123', array('host' => 'www.example.com'));
        $response = new CM_Response_Page($request);

        $this->assertNull(SK_Model_Affiliate::findByCode('bar123', $affiliateProvider));
        $site->preprocessPageResponse($response);
        $this->assertEquals('bar123', SK_Model_Affiliate::findByCode('bar123', $affiliateProvider)->getCode());
    }
}
