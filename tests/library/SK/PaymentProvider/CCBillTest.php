<?php

class SK_PaymentProvider_CCBillTest extends SKTest_TestCase {

    /** @var SK_ServiceBundle */
    private $_serviceBundle, $_serviceBundleSingleBilling;

    /** @var SK_PaymentProvider_CCBill */
    private $_paymentProvider;

    /** @var CM_Site_Abstract */
    private $_siteDefault, $_site2;

    /** @var SK_Model_PaymentOption */
    private $_paymentOption;

    protected function setUp() {
        $this->_siteDefault = $this->getMockForAbstractClass('CM_Site_Abstract', array(), '', true, true, true, array('getType'));
        $this->_siteDefault->expects($this->any())->method('getType')->will($this->returnValue(1));

        $this->_site2 = $this->getMockForAbstractClass('CM_Site_Abstract', array(), '', true, true, true, array('getType'));
        $this->_site2->expects($this->any())->method('getType')->will($this->returnValue(2));

        $paymentProvider = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        $this->_serviceBundle = SKTest_TH::createServiceBundle(19.95, 30, 29.95, 30);
        $this->_serviceBundleSingleBilling = SKTest_TH::createServiceBundle(19.95, 30);
        $this->_paymentProvider = new SK_PaymentProvider_CCBill();
        $this->_paymentProvider->setProviderBundleId($this->_serviceBundle, rand(1, 1000));
        $this->_paymentProvider->setProviderBundleId($this->_serviceBundleSingleBilling, rand(1, 1000));
        $this->_paymentProvider->setFields(array('clientAccnum' => '1000'));
        $this->_paymentProvider->setFields(array('clientSubacc' => '1001'));
        $this->_paymentProvider->setFields(array('clientAccnum' => '2000'), $this->_site2);
        $this->_paymentProvider->setFields(array('clientSubacc' => '2001'), $this->_site2);

        $this->_paymentOption = SKTest_TH::createPaymentOption($paymentProvider, SK_Model_PaymentOption::PAYMENT_TYPE_CHECK);
    }

    protected function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testGetCheckoutUrl() {
        $user = SKTest_TH::createUser();
        $site = $this->getMockForAbstractClass('CM_Site_Abstract', array(), '', true, true, true, array('getUrl', 'getModules'));
        $site->expects($this->any())->method('getUrl')->will($this->returnValue('http://www.example.com'));
        $site->expects($this->any())->method('getModules')->will($this->returnValue(array('SK', 'CM')));
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($site, $user));
        $serviceBundleSet = SKTest_TH::createServiceBundleSet();

        $checkOutUrl = $this->_paymentProvider->getCheckoutUrl($user, $this->_serviceBundle, $render, $this->_paymentOption, $serviceBundleSet);
        $this->assertSame('http://www.example.com/payment/ccbill?serviceBundle=' . $this->_serviceBundle->getId() . '&paymentOption=' .
            $this->_paymentOption->getId() . '&serviceBundleSet=' . $serviceBundleSet->getId(), $checkOutUrl);

        $checkOutUrl = $this->_paymentProvider->getCheckoutUrl($user, $this->_serviceBundle, $render, $this->_paymentOption);
        $this->assertSame('http://www.example.com/payment/ccbill?serviceBundle=' . $this->_serviceBundle->getId() . '&paymentOption=' .
            $this->_paymentOption->getId(), $checkOutUrl);
    }

    public function testGetIframeUrl() {
        $user = SKTest_TH::createUser();
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($this->_siteDefault, $user));
        $data = SK_PaymentProvider_Abstract::encodeData(array(
            'user'          => $user->getId(),
            'serviceBundle' => $this->_serviceBundle->getId(),
            'site'          => $this->_siteDefault->getType(),
        ));

        $iFrameUrl = $this->_paymentProvider->getFormUrl($user, $this->_serviceBundle, $render, $this->_paymentOption);
        parse_str(parse_url($iFrameUrl, PHP_URL_QUERY), $query);

        $this->assertEquals(1000, $query['clientAccnum']);
        $this->assertEquals(1001, $query['clientSubacc']);
        $this->assertEquals($this->_serviceBundle->getPrice(), $query['formPrice']);
        $this->assertEquals($this->_serviceBundle->getPeriod(), $query['formPeriod']);
        $this->assertEquals($this->_serviceBundle->getRecurringPrice(), $query['formRecurringPrice']);
        $this->assertEquals($this->_serviceBundle->getRecurringPeriod(), $query['formRecurringPeriod']);
        $this->assertSame($data, $query['data']);
    }

    public function testGetIframeUrlSecondSite() {
        $user = SKTest_TH::createUser();
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($this->_site2, $user));
        $data = SK_PaymentProvider_Abstract::encodeData(array(
            'user'          => $user->getId(),
            'serviceBundle' => $this->_serviceBundle->getId(),
            'site'          => $this->_site2->getType(),
        ));

        $iFrameUrl = $this->_paymentProvider->getFormUrl($user, $this->_serviceBundle, $render, $this->_paymentOption);
        parse_str(parse_url($iFrameUrl, PHP_URL_QUERY), $query);

        $this->assertEquals(2000, $query['clientAccnum']);
        $this->assertEquals(2001, $query['clientSubacc']);
        $this->assertEquals($this->_serviceBundle->getPrice(), $query['formPrice']);
        $this->assertEquals($this->_serviceBundle->getPeriod(), $query['formPeriod']);
        $this->assertEquals($this->_serviceBundle->getRecurringPrice(), $query['formRecurringPrice']);
        $this->assertEquals($this->_serviceBundle->getRecurringPeriod(), $query['formRecurringPeriod']);
        $this->assertSame($data, $query['data']);
    }

    public function testGetIframeUrlSingleBilling() {
        $user = SKTest_TH::createUser();
        $site = $this->getMockForAbstractClass('CM_Site_Abstract', array(), '', true, true, true, array('getUrl', 'getModules', 'getType'));
        $site->expects($this->any())->method('getUrl')->will($this->returnValue('http://www.example.com'));
        $site->expects($this->any())->method('getModules')->will($this->returnValue(array('SK', 'CM')));
        $site->expects($this->any())->method('getType')->will($this->returnValue(999));
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($site, $user));
        $data = array('user' => $user->getId(), 'serviceBundle' => $this->_serviceBundleSingleBilling->getId(), 'site' => 999);
        $data = SK_PaymentProvider_Abstract::encodeData($data);

        $iFrameUrl = $this->_paymentProvider->getFormUrl($user, $this->_serviceBundleSingleBilling, $render, $this->_paymentOption);
        parse_str(parse_url($iFrameUrl, PHP_URL_QUERY), $query);

        $this->assertEquals($this->_paymentProvider->getClientAccnum(), $query['clientAccnum']);
        $this->assertEquals($this->_paymentProvider->getClientSubaccSingleBillings(), $query['clientSubacc']);
        $this->assertEquals($this->_serviceBundle->getPrice(), $query['formPrice']);
        $this->assertEquals(3, $query['formPeriod']);
        $this->assertSame($data, $query['data']);
    }

    public function testGetClientAccnum() {
        $this->assertSame('1000', $this->_paymentProvider->getClientAccnum());
        $this->assertSame('2000', $this->_paymentProvider->getClientAccnum($this->_site2));
    }

    public function testGetClientSubacc() {
        $this->assertSame('1001', $this->_paymentProvider->getClientSubacc());
        $this->assertSame('2001', $this->_paymentProvider->getClientSubacc($this->_site2));
    }
}
