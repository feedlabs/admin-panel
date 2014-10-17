<?php

class SK_Component_PaymentSelectionTest extends SKTest_TestCase {

    /** @var  SK_Model_PaymentOption */
    private $_paymentOptionCreditCard1;

    /** @var  SK_Model_PaymentOption */
    private $_paymentOptionCreditCard2;

    /** @var  SK_Model_PaymentOption */
    private $_paymentOptionCheck;

    /** @var  SK_ServiceBundleSet */
    private $_serviceBundleSet;

    protected function setUp() {
        $paymentProvider1 = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        $paymentProvider2 = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ROCKETGATE);
        $this->_paymentOptionCreditCard1 = SKTest_TH::createPaymentOption($paymentProvider1, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $this->_paymentOptionCreditCard2 = SKTest_TH::createPaymentOption($paymentProvider2, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $this->_paymentOptionCheck = SKTest_TH::createPaymentOption($paymentProvider1, SK_Model_PaymentOption::PAYMENT_TYPE_CHECK);
        $paymentOptionSet1 = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOptionSet1->add($this->_paymentOptionCreditCard1);
        $paymentOptionSet1->add($this->_paymentOptionCreditCard2);
        $paymentOptionSet1->setPercentage($this->_paymentOptionCreditCard1, 40);
        $paymentOptionSet1->setPercentage($this->_paymentOptionCreditCard2, 60);

        $paymentOptionSet2 = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CHECK);
        $paymentOptionSet2->add($this->_paymentOptionCheck);
        $paymentOptionSet2->setPercentage($this->_paymentOptionCheck, 100);

        $this->_serviceBundleSet = SKTest_TH::createServiceBundleSet();
        $this->_serviceBundleSet->add(SKTest_TH::createServiceBundle());
        $this->_serviceBundleSet->setEnabled(true);
    }

    protected function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testGuest() {
        $cmp = new SK_Component_PaymentSelection();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_PaymentSelection(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('input[name="bundleId"]'));
    }

    public function testCascade() {
        $viewer = $this->_createViewer();
        $viewer->getBillingCascade()->addPaymentOption($this->_paymentOptionCreditCard1);
        $viewer->getBillingCascade()->addPaymentOption($this->_paymentOptionCheck);
        $cmp = new SK_Component_PaymentSelection(
            array(
                'useCascade'    => true,
                'serviceBundle' => $this->_serviceBundleSet->getServiceBundles()->getItem(0),
            ), $viewer);
        $page = $this->_renderComponent($cmp, $viewer);
        $this->assertSame(3, $page->find('.paymentOption')->count());
        $this->assertSame(2, $page->find('.paymentOption.greyedOut')->count());
    }
}
