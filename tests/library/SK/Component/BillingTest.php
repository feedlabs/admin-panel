<?php

class SK_Component_BillingTest extends SKTest_TestCase {

    public function setUp() {
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::SEGPAY);
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ROCKETGATE);
    }

    public function testGuest() {
        $cmp = new SK_Component_Billing();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $textContent = $page->getText();
        $this->assertContains('.faq.disclaimer', $textContent);
        $this->assertContains('.faq.5.answer', $textContent);
        $this->assertContains('.faq.31.question', $textContent);
        $this->assertContains('.faq.31.answer', $textContent);
        $this->assertContains('.faq.2.question', $textContent);
        $this->assertContains('.faq.2.answer', $textContent);
        $this->assertContains('.faq.6.question', $textContent);
        $this->assertContains('.faq.6.answer', $textContent);
        $this->assertContains('.faq.7.question', $textContent);
        $this->assertContains('.faq.7.answer', $textContent);
    }
}
