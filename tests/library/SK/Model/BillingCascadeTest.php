<?php

class SK_Model_BillingCascadeTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testAddPaymentOption() {
        $cascade = SK_Model_BillingCascade::create(SKTest_TH::createUser());
        $this->assertTrue($cascade->getPaymentOptionList()->isEmpty());

        $paymentOption1 = $this->getMockBuilder('SK_Model_PaymentOption')->setMethods(array('getId'))->disableOriginalConstructor()->getMockForAbstractClass();
        $paymentOption1->expects($this->any())->method('getId')->will($this->returnValue(1));
        /** @var SK_Model_PaymentOption $paymentOption1 */
        $paymentOption2 = $this->getMockBuilder('SK_Model_PaymentOption')->setMethods(array('getId'))->disableOriginalConstructor()->getMockForAbstractClass();
        $paymentOption2->expects($this->any())->method('getId')->will($this->returnValue(2));
        /** @var SK_Model_PaymentOption $paymentOption2 */

        $cascade->addPaymentOption($paymentOption1);

        $this->assertContains($paymentOption1->getId(), $cascade->getPaymentOptionData());
        $this->assertNotContains($paymentOption2->getId(), $cascade->getPaymentOptionData());

        $cascade->addPaymentOption($paymentOption2);
        $this->assertContainsAll(array($paymentOption1->getId(), $paymentOption2->getId()), $cascade->getPaymentOptionData());

        $this->assertCount(2, $cascade->getPaymentOptionData());
        $cascade->addPaymentOption($paymentOption1);
        $this->assertCount(2, $cascade->getPaymentOptionData());
    }

    public function testGetPaymentOptionList() {
        $paymentProvider = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        $paymentOption = SKTest_TH::createPaymentOption($paymentProvider, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $cascade = SK_Model_BillingCascade::create(SKTest_TH::createUser());
        $cascade->addPaymentOption($paymentOption);

        $this->assertContains($paymentOption, $cascade->getPaymentOptionList());
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $cascade = SK_Model_BillingCascade::create($user);
        $this->assertEquals($user, $cascade->getUser());
        $this->assertNull($cascade->getServiceBundle());
        $this->assertSame(time(), $cascade->getCreated());
        $this->assertTrue($cascade->getPaymentOptionList()->isEmpty());
        $this->assertNull($cascade->getPaymentOptionSelected());
    }

    public function testSetServiceBundle() {
        $user = SKTest_TH::createUser();
        $cascade = SK_Model_BillingCascade::create($user);
        $this->assertNull($cascade->getServiceBundle());

        $serviceBundle = SKTest_TH::createServiceBundle();
        $cascade->setServiceBundle($serviceBundle);

        $this->assertEquals($serviceBundle, $cascade->getServiceBundle());
    }

    public function testFindByUser() {
        $user = SKTest_TH::createUser();
        $cascade = SK_Model_BillingCascade::create($user);

        $this->assertEquals($cascade, SK_Model_BillingCascade::findByUser($user));
        $this->assertNull(SK_Model_BillingCascade::findByUser(SKTest_TH::createUser()));
    }

    public function testSetPaymentOptionSelected() {
        $paymentProvider = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        $paymentOption1 = SKTest_TH::createPaymentOption($paymentProvider, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption2 = SKTest_TH::createPaymentOption($paymentProvider, SK_Model_PaymentOption::PAYMENT_TYPE_CHECK);
        $cascade = SK_Model_BillingCascade::create(SKTest_TH::createUser());
        $this->assertNull($cascade->getPaymentOptionSelected());

        $cascade->setPaymentOptionSelected($paymentOption1);

        $this->assertEquals($paymentOption1, $cascade->getPaymentOptionSelected());

        $cascade->setPaymentOptionSelected($paymentOption2);
        $this->assertEquals($paymentOption2, $cascade->getPaymentOptionSelected());
    }

    public function testDeleteOlder() {
        SK_Model_BillingCascade::create(SKTest_TH::createUser());
        SK_Model_BillingCascade::create(SKTest_TH::createUser());
        SKTest_TH::timeForward(11);
        SK_Model_BillingCascade::create(SKTest_TH::createUser());
        $this->assertRow('sk_model_billingcascade', null, 3);
        SK_Model_BillingCascade::deleteOlder(10);
        $this->assertRow('sk_model_billingcascade', null, 1);
    }
}
