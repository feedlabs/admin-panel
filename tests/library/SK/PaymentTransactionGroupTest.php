<?php

class SK_PaymentTransactionGroupTest extends SKTest_TestCase {

    public function setUp() {
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_CCBill::CCBILL);
    }

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, '16289361287123', $user->getId());
        $this->assertEquals(SK_PaymentProvider_Abstract::getAll()->getItem(0)->getId(), $transactionGroup->getPaymentProvider()->getId());
        $this->assertEquals('16289361287123', $transactionGroup->getKey());
        $this->assertEquals($user, $transactionGroup->getUser());
        try {
            $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, '16289361287123', $user->getId());
            $this->fail('Created duplicate transactionGroup');
        } catch (CM_Exception_Duplicate $ex) {
            $this->assertTrue(true);
        }
    }

    public function testFind() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, '253617312', $user->getId());
        $group = SK_PaymentTransactionGroup::find('253617312', $paymentProvider);
        $this->assertEquals($transactionGroup, $group);
    }

    public function testConstructor() {
        try {
            $group = new SK_PaymentTransactionGroup(12321);
            $this->fail('Should throw an exception');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }

    public function testDeleteProfile() {
        $user = SKTest_TH::createUser();
        $userId = $user->getId();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, '253617312', $user->getId());
        $user->delete();
        $group = new SK_PaymentTransactionGroup($transactionGroup->getId());
        $this->assertEquals($userId, $transactionGroup->getUserId());
        $this->assertNull($group->getUser());
    }

    public function testGetSetStopStamp() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'foo', $user->getId());
        $this->assertNull($transactionGroup->getStopStamp());

        $transactionGroup->setStopStamp(time());
        $this->assertEquals(time(), $transactionGroup->getStopStamp(), null, 1);

        $transactionGroup->setStopStamp(null);
        $this->assertNull($transactionGroup->getStopStamp());
    }

    public function testGetSetCancelStamp() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'foo', $user->getId());
        $this->assertNull($transactionGroup->getCancelStamp());

        $transactionGroup->setCancelStamp(time());
        $this->assertEquals(time(), $transactionGroup->getCancelStamp(), null, 1);

        $transactionGroup->setCancelStamp(null);
        $this->assertNull($transactionGroup->getCancelStamp());
    }

    public function testStopInactive() {
        $serviceBundleRecurring = SKTest_TH::createServiceBundle(35, 10, 35, 20);
        $serviceBundle = SKTest_TH::createServiceBundle(35, 10);
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $group1 = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'a', 1);
        $group2 = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'b', 1);
        $group3 = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'c', 1);
        $paymentTransaction1 = SKTest_TH::createPaymentTransaction(1, $paymentProvider, 'a1', 1, $serviceBundleRecurring, $group1);
        $paymentTransaction2 = SKTest_TH::createPaymentTransaction(1, $paymentProvider, 'b1', 1, $serviceBundle, $group2);

        SK_PaymentTransactionGroup::stopInactive();
        $this->_assertStopStamp(null, $group1);
        $this->_assertStopStamp(null, $group2);
        $this->_assertStopStamp(($stopStamp3 = time()), $group3);

        SKTest_TH::timeDaysForward(10); // day 10
        SK_PaymentTransactionGroup::stopInactive();
        $this->_assertStopStamp(null, $group1);
        $this->_assertStopStamp(null, $group2);
        $this->_assertStopStamp($stopStamp3, $group3);

        SKTest_TH::timeDaysForward(2); // day 12
        SK_PaymentTransactionGroup::stopInactive();
        $this->_assertStopStamp($paymentTransaction1->getCreated() + 10 * 86400, $group1);
        $this->_assertStopStamp($paymentTransaction2->getCreated() + 10 * 86400, $group2);
        $this->_assertStopStamp($stopStamp3, $group3);

        $paymentTransaction3 = SKTest_TH::createPaymentTransaction(1, $paymentProvider, 'a2', 1, $serviceBundleRecurring, $group1);
        SK_PaymentTransactionGroup::stopInactive();
        $this->_assertStopStamp(null, $group1);
        $this->_assertStopStamp($paymentTransaction2->getCreated() + 10 * 86400, $group2);
        $this->_assertStopStamp($stopStamp3, $group3);

        SKTest_TH::timeDaysForward(20); // day 32
        SK_PaymentTransactionGroup::stopInactive();
        $this->_assertStopStamp(null, $group1);
        $this->_assertStopStamp($paymentTransaction2->getCreated() + 10 * 86400, $group2);
        $this->_assertStopStamp($stopStamp3, $group3);

        SKTest_TH::timeDaysForward(2); // day 34
        SK_PaymentTransactionGroup::stopInactive();
        $this->_assertStopStamp($paymentTransaction3->getCreated() + 20 * 86400, $group1);
        $this->_assertStopStamp($paymentTransaction2->getCreated() + 10 * 86400, $group2);
        $this->_assertStopStamp($stopStamp3, $group3);
    }

    public function testGetTransactions() {
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $serviceBundle = SKTest_TH::createServiceBundle(35, 10, 35, 20);
        $group = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'a', 1);
        $paymentTransaction1 = SKTest_TH::createPaymentTransaction(1, $paymentProvider, 'a1', 1, $serviceBundle, $group);
        $paymentTransaction2 = SKTest_TH::createPaymentTransaction(1, $paymentProvider, 'a2', 1, $serviceBundle, $group);
        $paymentTransaction3 = SKTest_TH::createPaymentTransaction(1, $paymentProvider, 'a3', 1, $serviceBundle, $group);
        $newType = $paymentTransaction3->getType() + 1;
        CM_Db_Db::update('sk_paymentTransaction', array('type' => $newType), array('id' => $paymentTransaction2->getId()));
        CM_Db_Db::update('sk_paymentTransaction', array('type' => $newType), array('id' => $paymentTransaction3->getId()));
        $this->assertSame(3, $group->getTransactions()->getCount());
        $this->assertSame(1, $group->getTransactions($paymentTransaction1->getType())->getCount());
        $this->assertSame(2, $group->getTransactions($newType)->getCount());
        $this->assertEquals($paymentTransaction1, $group->getTransactions($paymentTransaction1->getType())->getItem(0));
    }

    /**
     * @param int|null                   $expected
     * @param SK_PaymentTransactionGroup $group
     */
    private function _assertStopStamp($expected, SK_PaymentTransactionGroup $group) {
        SKTest_TH::reinstantiateModel($group);
        if (null === $expected) {
            $this->assertNull($group->getStopStamp());
        } else {
            $this->assertSameTime($expected, $group->getStopStamp());
        }
    }
}
