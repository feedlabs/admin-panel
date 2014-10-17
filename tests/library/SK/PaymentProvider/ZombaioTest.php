<?php

class SK_PaymentProvider_ZombaioTest extends SKTest_TestCase {

    /** @var SK_ServiceBundle */
    private $_serviceBundle;

    /** @var SK_PaymentProvider_Zombaio */
    private $_paymentProviderZombaio;

    protected function setUp() {
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ZOMBAIO);
        $this->_serviceBundle = SKTest_TH::createServiceBundle(35, 10);
        $this->_paymentProviderZombaio = new SK_PaymentProvider_Zombaio();
        $this->_paymentProviderZombaio->setProviderBundleId($this->_serviceBundle,
            CM_Params::encode(array('group' => rand(1, 1000), 'package' => rand(1, 1000)), true));
    }

    protected function tearDown() {
        SKTest_TH::clearEnv();
        SKTest_TH::deleteServiceBundle($this->_serviceBundle);
    }

    /**
     * @expectedException        CM_Exception_NotImplemented
     * @expectedExceptionMessage The payment provider `Zombaio` is not supported any more!
     */
    public function testGetCheckoutUrl() {
        $user = SKTest_TH::createUser();
        $site = $this->getMockForAbstractClass('CM_Site_Abstract', array(), '', true, true, true, array('getUrl', 'getModules'));
        $site->expects($this->any())->method('getUrl')->will($this->returnValue('http://www.example.com'));
        $site->expects($this->any())->method('getModules')->will($this->returnValue(array('SK', 'CM')));
        /** @var CM_Site_Abstract $site */
        $render = new CM_Frontend_Render(new CM_Frontend_Environment($site, $user));
        $paymentOption = SKTest_TH::createPaymentOption($this->_paymentProviderZombaio);
        $serviceBundleSet = SKTest_TH::createServiceBundleSet();

        $this->_paymentProviderZombaio->getCheckoutUrl($user, $this->_serviceBundle, $render, $paymentOption, $serviceBundleSet);
    }
}
