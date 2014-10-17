<?php

class SK_Component_FaqTest extends SKTest_TestCase {

    public function setUp() {
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::SEGPAY);
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ROCKETGATE);
    }

    public function testGuest() {
        $cmp = new SK_Component_Faq();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('Miscellaneous', $page->find('h2:eq(3)')->getText());
    }
}
