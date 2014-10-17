<?php

class SK_PaymentTransaction_AbstractTest extends SKTest_TestCase {

    /** @var SK_ServiceBundle $_serviceBundle */
    private $_serviceBundle;

    public static function setUpBeforeClass() {
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::WEBBILLING);
    }

    public function setUp() {
        $this->_serviceBundle = SKTest_TH::createServiceBundle(35, 20);
    }

    public function tearDown() {
        SKTest_TH::deleteServiceBundle($this->_serviceBundle);
    }

    public function testConstructor() {
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $user = SKTest_TH::createUser();
        $transaction = SKTest_TH::createPaymentTransaction($user->getId(), $paymentProvider, 'aasd76512ads', 45.35, $this->_serviceBundle);
        try {
            new SK_PaymentTransaction_ServiceBundle(9999999);
            $this->fail('Should throw an exception');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }

        $transactionId = $transaction->getId();
        unset($transaction);
        $userId = $user->getId();
        $user->delete();
        $transaction = new SK_PaymentTransaction_ServiceBundle($transactionId);
        $this->assertSame($userId, $transaction->getUserId());
        $this->assertNull($transaction->getUser());
    }

    public function testCreate() {
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $user = SKTest_TH::createUser();
        $transaction = SKTest_TH::createPaymentTransaction($user->getId(), $paymentProvider, 'aasd7asd6512ads', 45.35, $this->_serviceBundle);
        $transaction2 = SK_PaymentTransaction_Abstract::factory($transaction->getId());
        $this->assertInstanceOf('SK_PaymentTransaction_ServiceBundle', $transaction2);
        $this->assertTrue($transaction->getUser()->equals($user));
        $this->assertEquals($transaction2, $transaction);
        $this->assertNull($transaction->getGroup());

        $this->assertNull($transaction->getGroupSequence());

        try {
            SKTest_TH::createPaymentTransaction($user->getId(), $paymentProvider, 'aasd7asd6512ads', 50.7, $this->_serviceBundle);
            $this->fail('Created duplicate transaction');
        } catch (CM_Exception_Duplicate $ex) {
            $this->assertTrue(true);
        }
    }

    public function testCreateWithGroup() {
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $user = SKTest_TH::createUser();
        $group = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'a', $user->getId());
        $this->assertSame(0, $group->getTransactions()->getCount());
        $group->setStopStamp(1);
        $this->assertSame(1, $group->getStopStamp());
        SKTest_TH::createPaymentTransaction($user->getId(), $paymentProvider, 'a1', 1, $this->_serviceBundle, $group);
        $this->assertSame(1, $group->getTransactions()->getCount());
        $this->assertNull($group->getStopStamp());
    }

    public function testFactory() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $transaction = SKTest_TH::createPaymentTransaction($user->getId(), $paymentProvider, 'aasd76qwert512ads', 45.35, $this->_serviceBundle);
        try {
            SK_PaymentTransaction_Abstract::factory($transaction->getId(), 999999);
            $this->fail('Should throw an exception');
        } catch (CM_Class_Exception_TypeNotConfiguredException $ex) {
            $this->assertTrue(true);
        }

        try {
            SK_PaymentTransaction_Abstract::factory(9999999);
            $this->fail('Should throw an exception');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }

    public function testRebill() {
        $user = SKTest_TH::createUser();
        $userId = $user->getId();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $group = SKTest_TH::createPaymentTransactionGroup($paymentProvider, '234234234', $user->getId());
        $transaction = SKTest_TH::createPaymentTransaction($user->getId(), $paymentProvider, '234234234', 45.35, $this->_serviceBundle, $group);
        $this->assertSame(1, $transaction->getGroupSequence());

        $group3 = SKTest_TH::createPaymentTransactionGroup($paymentProvider, '213123', $user->getId());

        $user->delete();
        $group2 = SK_PaymentTransactionGroup::find('234234234', $paymentProvider);
        $merchantAccount = SKTest_TH::createMerchantAccount($paymentProvider);
        $transaction2 = SK_PaymentTransaction_Abstract::createRebill('1231233132', 29.95, $group2, $merchantAccount, time());
        $this->assertSame(2, $transaction2->getGroupSequence());
        $this->assertSame(29.95, $transaction2->getAmount());
        $this->assertSame(4, $transaction2->getPaymentProvider()->getId());
        $this->assertSame('1231233132', $transaction2->getKey());
        $this->assertEquals($transaction->getGroup(), $transaction2->getGroup());
        $this->assertSame($transaction->getData(), $transaction2->getData());
        $this->assertSame($transaction2->getUserId(), $userId);
        $this->assertSame($transaction2->getMerchantAccount()->getId(), $merchantAccount->getId());
        $this->assertSame(SK_PaymentTransaction_ServiceBundle::getTypeStatic(), $transaction2->getType());

        try {
            $transaction1 = SK_PaymentTransaction_Abstract::createRebill('213123', 123, $group3, $merchantAccount, time());
            $this->fail("Rebilled to a group without initial transaction.");
        } catch (CM_Exception_Invalid $ex) {
            $this->assertTrue(true);
        }
    }

    public function testChargeback() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $group = SKTest_TH::createPaymentTransactionGroup($paymentProvider, '1234567890', $user->getId());
        $transaction = SKTest_TH::createPaymentTransaction($user->getId(), $paymentProvider, '1234567890', 45.35, $this->_serviceBundle, $group);

        $this->assertNull($transaction->getChargebackStamp());

        $transaction->setChargebackStamp(time());
        $this->assertSameTime(time(), $transaction->getChargebackStamp());

        $transaction->setChargebackStamp(null);
        $this->assertNull($transaction->getChargebackStamp());
    }

    public function testRefund() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $group = SKTest_TH::createPaymentTransactionGroup($paymentProvider, '12345678902', $user->getId());
        $transaction = SKTest_TH::createPaymentTransaction($user->getId(), $paymentProvider, '12345678902', 45.35, $this->_serviceBundle, $group);

        $this->assertNull($transaction->getRefundStamp());

        $transaction->setRefundStamp(time());
        $this->assertSameTime(time(), $transaction->getRefundStamp());

        $transaction->setRefundStamp(null);
        $this->assertNull($transaction->getRefundStamp());
    }

    public function testVoid() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $group = SKTest_TH::createPaymentTransactionGroup($paymentProvider, '12345678901', $user->getId());
        $transaction = SKTest_TH::createPaymentTransaction($user->getId(), $paymentProvider, '12345678901', 45.35, $this->_serviceBundle, $group);

        $this->assertNull($transaction->getVoidStamp());

        $transaction->setVoidStamp(time());
        $this->assertSameTime(time(), $transaction->getVoidStamp());

        $transaction->setVoidStamp(null);
        $this->assertNull($transaction->getVoidStamp());
    }

    public function testFind() {
        $user = SKTest_TH::createUser();
        SK_PaymentProvider_Abstract::getAll()->getItem(0);
        SKTest_TH::createPaymentTransaction($user->getId(), SK_PaymentProvider_Abstract::getAll()->getItem(0), '123123213123', 45.35, $this->_serviceBundle);
        SKTest_TH::createPaymentTransaction($user->getId(), SK_PaymentProvider_Abstract::getAll()->getItem(1), '123123213123', 23.4, $this->_serviceBundle);
        $transaction = SK_PaymentTransaction_Abstract::find('123123213123', SK_PaymentProvider_Abstract::getAll()->getItem(0));
        $this->assertSame(45.35, $transaction->getAmount());
        $transaction = SK_PaymentTransaction_Abstract::find('123123213123', SK_PaymentProvider_Abstract::getAll()->getItem(1));
        $this->assertSame(23.4, $transaction->getAmount());
        $transaction = SK_PaymentTransaction_Abstract::find(123, SK_PaymentProvider_Abstract::getAll()->getItem(0));
        $this->assertNull($transaction);
    }
}
