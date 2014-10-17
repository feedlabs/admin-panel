<?php

class SK_PaymentProvider_WtsTest extends SKTest_TestCase {

    /** @var SK_ServiceBundle */
    private $_serviceBundle;

    /** @var SK_PaymentProvider_Wts */
    private $_paymentProviderWts;

    protected function setUp() {
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::WTS);
        $this->_serviceBundle = SKTest_TH::createServiceBundle(19.95, 30, 29.95, 30);
        $this->_paymentProviderWts = new SK_PaymentProvider_Wts();
    }

    protected function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testGetCheckoutUrl() {
        $user = SKTest_TH::createUser();
        $site = $this->getMockForAbstractClass('CM_Site_Abstract', array(), '', true, true, true, array('getUrl', 'getModules'));
        $site->expects($this->any())->method('getUrl')->will($this->returnValue('http://www.example.com'));
        $site->expects($this->any())->method('getModules')->will($this->returnValue(array('SK', 'CM')));
        /** @var CM_Site_Abstract $site */
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($site, $user));
        $paymentOption = SKTest_TH::createPaymentOption($this->_paymentProviderWts);
        $serviceBundleSet = SKTest_TH::createServiceBundleSet();

        $checkOutUrl = $this->_paymentProviderWts->getCheckoutUrl($user, $this->_serviceBundle, $render, $paymentOption, $serviceBundleSet);
        $this->assertSame('http://www.example.com/payment/wts?serviceBundle=' . $this->_serviceBundle->getId() . '&paymentOption=' .
            $paymentOption->getId() . '&serviceBundleSet=' . $serviceBundleSet->getId(), $checkOutUrl);

        $checkOutUrl = $this->_paymentProviderWts->getCheckoutUrl($user, $this->_serviceBundle, $render, $paymentOption);
        $this->assertSame('http://www.example.com/payment/wts?serviceBundle=' . $this->_serviceBundle->getId() . '&paymentOption=' .
            $paymentOption->getId(), $checkOutUrl);
    }
}
