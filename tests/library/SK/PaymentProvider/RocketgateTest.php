<?php

class SK_PaymentProvider_RocketgateTest extends SKTest_TestCase {

    /** @var SK_ServiceBundle */
    private $_serviceBundle;

    /** @var SK_PaymentProvider_Rocketgate */
    private $_paymentProviderRocketgate;

    /** @var SK_User */
    private $_user;

    /** @var SK_Model_PaymentOption */
    private $_paymentOption;

    /** @var CM_Site_Abstract */
    private $_site;

    /** @var CM_Frontend_Render */
    private $_render;

    protected function setUp() {
        $this->_serviceBundle = SKTest_TH::createServiceBundle(19.95, 30, 29.95, 30);
        $this->_paymentProviderRocketgate = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ROCKETGATE);
        $this->_paymentOption = SKTest_TH::createPaymentOption($this->_paymentProviderRocketgate);
        $this->_user = SKTest_TH::createUser();;
        $this->_site = $this->getMockSite('CM_Site_Abstract', null, null, array('getUrl', 'getModules'));
        $this->_site->expects($this->any())->method('getUrl')->will($this->returnValue('http://www.example.com'));
        $this->_site->expects($this->any())->method('getModules')->will($this->returnValue(array('SK', 'CM')));
        $this->_render = new CM_Frontend_Render(new CM_Frontend_Environment($this->_site, $this->_user));
    }

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testGetCheckoutUrl() {
        $serviceBundleSet = SKTest_TH::createServiceBundleSet();

        $url = $this->_paymentProviderRocketgate->getCheckoutUrl($this->_user, $this->_serviceBundle, $this->_render, $this->_paymentOption, $serviceBundleSet);
        $this->assertSame('http://www.example.com/payment/rocketgate?serviceBundle=' . $this->_serviceBundle->getId() . '&paymentOption=' .
            $this->_paymentOption->getId() . '&serviceBundleSet=' . $serviceBundleSet->getId(), $url);

        $url = $this->_paymentProviderRocketgate->getCheckoutUrl($this->_user, $this->_serviceBundle, $this->_render, $this->_paymentOption);
        $this->assertSame('http://www.example.com/payment/rocketgate?serviceBundle=' . $this->_serviceBundle->getId() . '&paymentOption=' .
            $this->_paymentOption->getId(), $url);
    }

    public function testGetFormUrl() {
        $data = array('user' => $this->_user->getId(), 'serviceBundle' => $this->_serviceBundle->getId(), 'site' => $this->_site->getType());
        $dataEncoded = SK_PaymentProvider_Abstract::encodeData($data);
        $url = $this->_paymentProviderRocketgate->getFormUrl($this->_user, $this->_serviceBundle, $this->_render, $this->_paymentOption);
        $urlCss = $this->_render->getUrlResource('library-css', 'all.css');
        $urlExpected =
            'https://secure.rocketgate.com/hostedpage/servlet/HostedPagePurchase?merch=' . $this->_paymentProviderRocketgate->getMerchantId() .
            '&id=' . SK_Site_Abstract::getInstallationName() . $this->_user->getId() . '&username=' . $this->_user->getDisplayName() . '&email=' .
            urlencode($this->_user->getEmail()) . '&amount=' . $this->_serviceBundle->getPrice() . '&rebill-amount=' .
            $this->_serviceBundle->getRecurringPrice() . '&rebill-start=' . $this->_serviceBundle->getPeriod() . '&rebill-freq=' .
            $this->_serviceBundle->getRecurringPeriod() .
            '&method=CC&purchase=TRUE&success=http%3A%2F%2Fwww.example.com%2Fpayment%2Frocketgate%2Fiframe-complete%3Fstatus%3Dsuccess&fail=http%3A%2F%2Fwww.example.com%2Fpayment%2Fdenial&tos=http%3A%2F%2Fwww.example.com%2Fabout%2Fterms&style=' .
            urlencode($urlCss) . '&avs=YES&scrub=YES&lang=EN&prodid=' . $this->_serviceBundle->getId() . '&siteid=' . $this->_site->getType() .
            '&udf02=' . urlencode($dataEncoded);

        $this->assertContains($urlExpected, $url);
    }
}
