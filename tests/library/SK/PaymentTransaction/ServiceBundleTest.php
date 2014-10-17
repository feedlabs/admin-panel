<?php

class SK_PaymentTransaction_ServiceBundleTest extends SKTest_TestCase {

    public function setup() {
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_CCBill::CCBILL);
        CM_Db_Db::insert('sk_serviceBundle', array('id'              => 99999, 'price' => 213, 'period' => 312, 'recurringPrice' => 123,
                                                   'recurringPeriod' => 321));
        CM_Db_Db::insert('sk_serviceBundle_service', array('serviceBundleId' => 99999, 'serviceId' => 1, 'amount' => 20, 'recurringAmount' => 30));
    }

    public function tearDown() {
        CM_Db_Db::delete('sk_serviceBundle', array('id' => 99999));
        CM_Db_Db::delete('sk_serviceBundle_service', array('serviceBundleId' => 99999));
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $transaction = SKTest_TH::createPaymentTransaction($user->getId(), $paymentProvider, '02342348761234321', 21.3123765, new SK_ServiceBundle(99999));
        $this->assertEquals(99999, $transaction->getServiceBundle()->getId());
        $this->assertEquals('02342348761234321', $transaction->getKey());
    }

    public function testProcess() {
        $user = SKTest_TH::createUser();
        $userId = $user->getId();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        SKTest_TH::createPaymentTransaction($userId, $paymentProvider, '02342348761234321', 21.3123765, new SK_ServiceBundle(99999));
        $user->_change();
        $this->assertTrue($user->getRoles()->contains(SK_Role::PREMIUMUSER));
        $this->assertNotNull($user->getRoles()->getExpirationStamp(SK_Role::PREMIUMUSER));
        $user->delete();
        SKTest_TH::createPaymentTransaction($userId, $paymentProvider, '0234234876123431221', 21.3123765, new SK_ServiceBundle(99999));
    }

    public function testRebill() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SK_PaymentProvider_Abstract::getAll()->getItem(0);
        $merchantAccount = SKTest_TH::createMerchantAccount($paymentProvider);
        $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, '16289361287123', $user->getId());
        $transaction = SKTest_TH::createPaymentTransaction($user->getId(), $paymentProvider, '02342348761234321', 2.95, new SK_ServiceBundle(99999), $transactionGroup);
        $this->assertEquals($transactionGroup->getId(), $transaction->getGroup()->getId());

        $this->assertEquals(1, $transactionGroup->getTransactions()->getCount());

        $group = SK_PaymentTransactionGroup::find('16289361287123', $paymentProvider);
        $transaction1 = SK_PaymentTransaction_Abstract::createRebill('123123', 29.95, $group, $merchantAccount, time());
        $this->assertEquals(99999, $transaction1->getServiceBundle()->getId());
        $this->assertEquals(SK_PaymentTransaction_ServiceBundle::getTypeStatic(), $transaction1->getType());
    }
}
