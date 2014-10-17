<?php

class SK_Model_PaymentOptionSetTest extends SKTest_TestCase {

    protected function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $site = CM_Site_Abstract::factory();
        $label = 'foo';
        $paymentType = SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD;
        $enabled = true;
        $position = 1;

        $paymentOption = SK_Model_PaymentOptionSet::create($site, $label, $paymentType, $enabled, $position);

        $this->assertSame($site->getId(), $paymentOption->getSite()->getId());
        $this->assertSame($label, $paymentOption->getLabel());
        $this->assertSame($paymentType, $paymentOption->getPaymentType());
        $this->assertSame($enabled, $paymentOption->getEnabled());
        $this->assertSame($position, $paymentOption->getPosition());
    }

    public function testAddOption() {
        $paymentOptionSet = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption = SKTest_TH::createPaymentOption(null, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);

        $optionList = $paymentOptionSet->getOptionList();
        $this->assertSame(0, $optionList->getCount());

        $paymentOptionSet->add($paymentOption);

        $optionList = $paymentOptionSet->getOptionList();
        $this->assertSame(1, $optionList->getCount());
        $this->assertContains($paymentOption, $optionList);
    }

    public function testRemoveOption() {
        $paymentOptionSet = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption1 = SKTest_TH::createPaymentOption(SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption2 = SKTest_TH::createPaymentOption(SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ROCKETGATE), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOptionSet->add($paymentOption1);
        $paymentOptionSet->add($paymentOption2);

        $optionList = $paymentOptionSet->getOptionList();
        $this->assertSame(2, $optionList->getCount());
        $this->assertContains($paymentOption1, $optionList);
        $this->assertContains($paymentOption2, $optionList);

        $paymentOptionSet->remove($paymentOption1);

        $optionList = $paymentOptionSet->getOptionList();
        $this->assertSame(1, $optionList->getCount());
        $this->assertNotContains($paymentOption1, $optionList);
        $this->assertContains($paymentOption2, $optionList);
    }

    public function testContainsOption() {
        $paymentOptionSet = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption1 = SKTest_TH::createPaymentOption(SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption2 = SKTest_TH::createPaymentOption(SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ROCKETGATE), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOptionSet->add($paymentOption1);
        $paymentOptionSet->add($paymentOption2);

        $optionList = $paymentOptionSet->getOptionList();
        $this->assertSame(2, $optionList->getCount());
        $this->assertTrue($optionList->contains($paymentOption1));
        $this->assertTrue($optionList->contains($paymentOption2));
    }

    public function testUpdatePosition() {
        $site = $this->getMockSite();
        $site2 = $this->getMockSite();
        $paymentOptionSet1 = SKTest_TH::createPaymentOptionSet($site, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOptionSet2 = SKTest_TH::createPaymentOptionSet($site, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOptionSet3 = SKTest_TH::createPaymentOptionSet($site, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD, null, false);
        $paymentOptionSet4 = SKTest_TH::createPaymentOptionSet($site2, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOptionSet5 = SKTest_TH::createPaymentOptionSet($site2, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOptionSet6 = SKTest_TH::createPaymentOptionSet($site2, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD, null, false);

        $optionSetList = SK_Model_PaymentOptionSet::getPagingBySite($site);
        $this->assertEquals(array($paymentOptionSet1, $paymentOptionSet2), $optionSetList->getItems());
        $this->assertSame(1, $optionSetList->getItem(0)->getPosition());
        $this->assertSame(2, $optionSetList->getItem(1)->getPosition());

        $optionSetList = SK_Model_PaymentOptionSet::getPagingBySite($site, true);
        $this->assertEquals(array($paymentOptionSet1, $paymentOptionSet2, $paymentOptionSet3), $optionSetList->getItems());
        $this->assertSame(1, $optionSetList->getItem(0)->getPosition());
        $this->assertSame(2, $optionSetList->getItem(1)->getPosition());
        $this->assertSame(3, $optionSetList->getItem(2)->getPosition());

        $optionSetList = SK_Model_PaymentOptionSet::getPagingBySite($site2);
        $this->assertEquals(array($paymentOptionSet4, $paymentOptionSet5), $optionSetList->getItems());
        $this->assertSame(1, $optionSetList->getItem(0)->getPosition());
        $this->assertSame(2, $optionSetList->getItem(1)->getPosition());

        $optionSetList = SK_Model_PaymentOptionSet::getPagingBySite($site2, true);
        $this->assertEquals(array($paymentOptionSet4, $paymentOptionSet5, $paymentOptionSet6), $optionSetList->getItems());
        $this->assertSame(1, $optionSetList->getItem(0)->getPosition());
        $this->assertSame(2, $optionSetList->getItem(1)->getPosition());
        $this->assertSame(3, $optionSetList->getItem(2)->getPosition());

        $paymentOptionSet2->updatePosition(1);
        $paymentOptionSet5->updatePosition(1);

        $optionSetList = SK_Model_PaymentOptionSet::getPagingBySite($site);
        $this->assertEquals(array($paymentOptionSet2, $paymentOptionSet1), $optionSetList->getItems());
        $this->assertSame(1, $optionSetList->getItem(0)->getPosition());
        $this->assertSame(2, $optionSetList->getItem(1)->getPosition());

        $optionSetList = SK_Model_PaymentOptionSet::getPagingBySite($site, true);
        $this->assertEquals(array($paymentOptionSet2, $paymentOptionSet1, $paymentOptionSet3), $optionSetList->getItems());
        $this->assertSame(1, $optionSetList->getItem(0)->getPosition());
        $this->assertSame(2, $optionSetList->getItem(1)->getPosition());
        $this->assertSame(3, $optionSetList->getItem(2)->getPosition());

        $optionSetList = SK_Model_PaymentOptionSet::getPagingBySite($site2);
        $this->assertEquals(array($paymentOptionSet5, $paymentOptionSet4), $optionSetList->getItems());
        $this->assertSame(1, $optionSetList->getItem(0)->getPosition());
        $this->assertSame(2, $optionSetList->getItem(1)->getPosition());

        $optionSetList = SK_Model_PaymentOptionSet::getPagingBySite($site2, true);
        $this->assertEquals(array($paymentOptionSet5, $paymentOptionSet4, $paymentOptionSet6), $optionSetList->getItems());
        $this->assertSame(1, $optionSetList->getItem(0)->getPosition());
        $this->assertSame(2, $optionSetList->getItem(1)->getPosition());
        $this->assertSame(3, $optionSetList->getItem(2)->getPosition());

        $paymentOptionSet2->updatePosition(3);
        $paymentOptionSet5->updatePosition(3);

        $optionSetList = SK_Model_PaymentOptionSet::getPagingBySite($site);
        $this->assertEquals(array($paymentOptionSet1, $paymentOptionSet2), $optionSetList->getItems());
        $this->assertSame(1, $optionSetList->getItem(0)->getPosition());
        $this->assertSame(3, $optionSetList->getItem(1)->getPosition());

        $optionSetList = SK_Model_PaymentOptionSet::getPagingBySite($site, true);
        $this->assertEquals(array($paymentOptionSet1, $paymentOptionSet3, $paymentOptionSet2), $optionSetList->getItems());
        $this->assertSame(1, $optionSetList->getItem(0)->getPosition());
        $this->assertSame(2, $optionSetList->getItem(1)->getPosition());
        $this->assertSame(3, $optionSetList->getItem(2)->getPosition());

        $optionSetList = SK_Model_PaymentOptionSet::getPagingBySite($site2);
        $this->assertEquals(array($paymentOptionSet4, $paymentOptionSet5), $optionSetList->getItems());
        $this->assertSame(1, $optionSetList->getItem(0)->getPosition());
        $this->assertSame(3, $optionSetList->getItem(1)->getPosition());

        $optionSetList = SK_Model_PaymentOptionSet::getPagingBySite($site2, true);
        $this->assertEquals(array($paymentOptionSet4, $paymentOptionSet6, $paymentOptionSet5), $optionSetList->getItems());
        $this->assertSame(1, $optionSetList->getItem(0)->getPosition());
        $this->assertSame(2, $optionSetList->getItem(1)->getPosition());
        $this->assertSame(3, $optionSetList->getItem(2)->getPosition());
    }

    public function testGetPaymentTypeName() {
        $paymentOptionSet1 = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CHECK);
        $this->assertSame('ACH / Check (US)', $paymentOptionSet1->getPaymentTypeName());
    }

    public function testGetOptionList() {
        $paymentOptionSet = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption1 = SKTest_TH::createPaymentOption(SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption2 = SKTest_TH::createPaymentOption(SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ROCKETGATE), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOptionSet->add($paymentOption1);
        $paymentOptionSet->add($paymentOption2);

        $optionList = $paymentOptionSet->getOptionList();
        $this->assertSame(2, $optionList->getCount());
        $this->assertContains($paymentOption1, $optionList);
        $this->assertContains($paymentOption2, $optionList);
    }

    public function testGetSetPercentage() {
        $paymentOptionSet = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption1 = SKTest_TH::createPaymentOption(SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption2 = SKTest_TH::createPaymentOption(SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ROCKETGATE), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOptionSet->add($paymentOption1);
        $paymentOptionSet->add($paymentOption2);

        $this->assertSame(0, $paymentOptionSet->getPercentage($paymentOption1));
        $this->assertSame(0, $paymentOptionSet->getPercentage($paymentOption2));

        $paymentOptionSet->setPercentage($paymentOption1, 30);
        $paymentOptionSet->setPercentage($paymentOption2, 70);

        $this->assertSame(30, $paymentOptionSet->getPercentage($paymentOption1));
        $this->assertSame(70, $paymentOptionSet->getPercentage($paymentOption2));
    }

    public function testGetPagingAll() {
        $paymentOptionSetList = SK_Model_PaymentOptionSet::getPagingAll();
        $this->assertSame(0, $paymentOptionSetList->getCount());

        $paymentOptionSet1 = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOptionSet2 = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CHECK);

        $paymentOptionSetList = SK_Model_PaymentOptionSet::getPagingAll();
        $this->assertSame(2, $paymentOptionSetList->getCount());
        $this->assertContains($paymentOptionSet1, $paymentOptionSetList);
        $this->assertContains($paymentOptionSet2, $paymentOptionSetList);
    }

    public function testGetPagingEnabled() {
        $paymentOptionSetList = SK_Model_PaymentOptionSet::getPagingEnabled();
        $this->assertSame(0, $paymentOptionSetList->getCount());

        $paymentOptionSet1 = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD, null, false);
        $paymentOptionSet2 = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CHECK, null, false);

        $paymentOptionSetList = SK_Model_PaymentOptionSet::getPagingEnabled();
        $this->assertSame(0, $paymentOptionSetList->getCount());

        $paymentOptionSet1->setEnabled(true);
        $paymentOptionSetList = SK_Model_PaymentOptionSet::getPagingEnabled();

        $this->assertSame(1, $paymentOptionSetList->getCount());
        $this->assertContains($paymentOptionSet1, $paymentOptionSetList);
        $this->assertNotContains($paymentOptionSet2, $paymentOptionSetList);
    }

    public function testOnDelete() {
        $paymentOptionSet = SKTest_TH::createPaymentOptionSet(null, SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOption = SKTest_TH::createPaymentOption(SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL), SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD);
        $paymentOptionSet->add($paymentOption);

        $this->assertRow('sk_model_paymentoptionset_paymentoption', array('paymentOptionSet' => $paymentOptionSet->getId()), 1);

        $paymentOptionSet->delete();

        $this->assertNotRow('sk_model_paymentoptionset_paymentoption', array('paymentOptionSet' => $paymentOptionSet->getId()));
    }
}
