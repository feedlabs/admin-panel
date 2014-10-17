<?php

class SK_Component_Payment_CcbillTest extends SKTest_TestCase {

    /** @var SK_Model_PaymentOption */
    protected $_paymentOption;

    protected function setUp() {
        $paymentProvider = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        $this->_paymentOption = SKTest_TH::createPaymentOption($paymentProvider);
    }

    protected function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testGuest() {
        $cmp = new SK_Component_Payment_Ccbill();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $serviceBundle = SKTest_TH::createServiceBundle();
        $serviceBundleSet = SKTest_TH::createServiceBundleSet();
        $serviceBundleSet->add($serviceBundle);

        $cmp = new SK_Component_Payment_Ccbill(array(
            'serviceBundle'    => $serviceBundle,
            'paymentOption'    => $this->_paymentOption,
            'serviceBundleSet' => $serviceBundleSet
        ), $viewer);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.SK_Component_Payment_Ccbill'));
        $this->assertTrue($page->has('.SK_Component_ServiceBundleView'));
        $this->assertTrue($page->has('.payment-form'));
    }
}
