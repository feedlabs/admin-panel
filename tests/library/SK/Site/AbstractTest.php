<?php

class SK_Site_AbstractTest extends SKTest_TestCase {

    public static function tearDownAfterClass() {
        CM_Db_Db::truncate('sk_affiliateProvider');
        parent::tearDownAfterClass();
    }

    public function testGetEmailAddressSupport() {
        CM_Config::get()->SK_Site_Abstract = new stdClass();
        CM_Config::get()->SK_Site_Abstract->emailAddressSupport = 'support@foo.bar';
        /** @var SK_Site_Abstract $site */
        $site = $this->getMockForAbstractClass('SK_Site_Abstract');
        $this->assertSame('support@foo.bar', $site->getEmailAddressSupport());
    }

    public function testPreprocessPageResponseViewer() {
        $site = $this->getMockSite('SK_Site_Abstract');
        $affiliateProvider = new SK_AffiliateProvider_Internal();
        /** @var $affiliate SK_Model_Affiliate */
        $affiliate = SK_Model_Affiliate::createStatic(array('label' => 'Foo', 'provider' => $affiliateProvider));
        $viewer = SKTest_TH::createUser();
        $request = new CM_Request_Get('/foo?af=' . $affiliate->getCode(), array('host' => 'www.example.com'), null, $viewer);
        $response = new CM_Response_Page($request);

        $site->preprocessPageResponse($response);
        $this->assertCount(1, SK_Model_Affiliate::findByUserId($viewer->getId()));
        $this->assertContains($affiliate, SK_Model_Affiliate::findByUserId($viewer->getId()));
        $this->assertCount(0, SK_Model_Affiliate::findByRequest($request));
    }

    public function testPreprocessPageResponseGuest() {
        $site = $this->getMockSite('SK_Site_Abstract');
        $affiliateProvider = new SK_AffiliateProvider_Internal();
        /** @var $affiliate SK_Model_Affiliate */
        $affiliate = SK_Model_Affiliate::createStatic(array('label' => 'Foo', 'provider' => $affiliateProvider));
        $request = new CM_Request_Get('/foo?af=' . $affiliate->getCode(), array('host' => 'www.example.com'));
        $response = new CM_Response_Page($request);

        $site->preprocessPageResponse($response);
        $this->assertEquals(array($affiliate), SK_Model_Affiliate::findByRequest($request));
    }

    public function testFindOfficialUser() {
        /** @var SK_Site_Abstract $site */
        $site = $this->getMockSite('SK_Site_Abstract', null, array('name' => 'foo'));
        $this->assertNull($site->findOfficialUser());

        $officialUser = SKTest_TH::createUser(null, 'bar');
        /** @var SK_Site_Abstract $site */
        $site = $this->getMockSite('SK_Site_Abstract', null, array('name' => 'bar', 'officialUser' => 'bar'));
        $this->assertEquals($officialUser, $site->findOfficialUser());
    }
}
