<?php

class SK_Model_PaymentOptionTest extends SKTest_TestCase {

    protected function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $paymentType = SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD;
        $paymentProvider = new SK_PaymentProvider_CCBill();
        $paymentOption = SK_Model_PaymentOption::create($paymentProvider, $paymentType);

        $this->assertSame($paymentType, $paymentOption->getPaymentType());
        $this->assertEquals($paymentProvider, $paymentOption->getPaymentProvider());
    }

    public function testGetAll() {
        $paymentOption1 = SK_Model_PaymentOption::create(new SK_PaymentProvider_CCBill(), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption2 = SK_Model_PaymentOption::create(new SK_PaymentProvider_CCBill(), SK_Model_PaymentOption::PAYMENT_TYPE_CHECK);
        $paymentOption3 = SK_Model_PaymentOption::create(new SK_PaymentProvider_Rocketgate(), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);

        $pagingAll = SK_Model_PaymentOption::getAll();

        $this->assertSame(3, $pagingAll->getCount());
        $this->assertContains($paymentOption1, $pagingAll);
        $this->assertContains($paymentOption2, $pagingAll);
        $this->assertContains($paymentOption3, $pagingAll);
    }

    public function testGetUsed() {
        $paymentOption1 = SK_Model_PaymentOption::create(new SK_PaymentProvider_CCBill(), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption2 = SK_Model_PaymentOption::create(new SK_PaymentProvider_CCBill(), SK_Model_PaymentOption::PAYMENT_TYPE_CHECK);
        $paymentOption3 = SK_Model_PaymentOption::create(new SK_PaymentProvider_Rocketgate(), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);

        $pagingUsed = SK_Model_PaymentOption::getDisplayed();

        $this->assertSame(0, $pagingUsed->getCount());

        $paymentOptionSet = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOptionSet->add($paymentOption1);
        $paymentOptionSet->add($paymentOption3);

        $pagingUsed = SK_Model_PaymentOption::getDisplayed();
        $this->assertSame(2, $pagingUsed->getCount());
        $this->assertContains($paymentOption1, $pagingUsed);
        $this->assertContains($paymentOption3, $pagingUsed);
        $this->assertNotContains($paymentOption2, $pagingUsed);
    }

    public function testGetPaymentTypeName() {
        $this->assertSame('ACH / Check (US)', SK_Model_PaymentOption::getPaymentTypeName(SK_Model_PaymentOption::PAYMENT_TYPE_CHECK));
    }

    public function testFindByTypeAndProvider() {
        $paymentOption1 = SK_Model_PaymentOption::create(new SK_PaymentProvider_CCBill(), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption2 = SK_Model_PaymentOption::create(new SK_PaymentProvider_CCBill(), SK_Model_PaymentOption::PAYMENT_TYPE_CHECK);

        $option = SK_Model_PaymentOption::findByTypeAndProvider(new SK_PaymentProvider_CCBill(), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $this->assertEquals($paymentOption1, $option);

        $option = SK_Model_PaymentOption::findByTypeAndProvider(new SK_PaymentProvider_CCBill(), SK_Model_PaymentOption::PAYMENT_TYPE_CHECK);
        $this->assertEquals($paymentOption2, $option);

        $option = SK_Model_PaymentOption::findByTypeAndProvider(new SK_PaymentProvider_Rocketgate(), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $this->assertNull($option);
    }

    /**
     * @expectedException CM_Exception_Nonexistent
     * @expectedExceptionMessage has no data
     */
    public function testDelete() {
        $paymentOption = SK_Model_PaymentOption::create(new SK_PaymentProvider_CCBill(), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);

        $newPaymentOption = new SK_Model_PaymentOption($paymentOption->getId());
        $newPaymentOption->delete();
        new SK_Model_PaymentOption($paymentOption->getId());
    }
}
