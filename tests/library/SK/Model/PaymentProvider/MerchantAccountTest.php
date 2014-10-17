<?php

class SK_Model_PaymentProvider_MerchantAccountTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $paymentProvider = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        $account = 'foo';
        $merchantAccount = SK_Model_PaymentProvider_MerchantAccount::create($paymentProvider, $account);

        $this->assertEquals($paymentProvider, $merchantAccount->getPaymentProvider());
        $this->assertSame($account, $merchantAccount->getAccount());
    }

    public function testFindByAccount() {
        $paymentProvider = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        $account = 'foo';

        $this->assertNull(SK_Model_PaymentProvider_MerchantAccount::findByAccount($account));

        $merchantAccount = SK_Model_PaymentProvider_MerchantAccount::create($paymentProvider, $account);

        $this->assertEquals($merchantAccount, SK_Model_PaymentProvider_MerchantAccount::findByAccount($account));
    }

    public function testGetAll() {
        $paging = SK_Model_PaymentProvider_MerchantAccount::getAll();
        $this->assertCount(0, $paging);

        $paymentProvider = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        $merchantAccount1 = SK_Model_PaymentProvider_MerchantAccount::create($paymentProvider, 'foo');
        $merchantAccount2 = SK_Model_PaymentProvider_MerchantAccount::create($paymentProvider, 'bar');
        $paging = SK_Model_PaymentProvider_MerchantAccount::getAll();

        $this->assertCount(2, $paging);
        $this->assertContains($merchantAccount1, $paging);
        $this->assertContains($merchantAccount2, $paging);

        $merchantAccount2->delete();
        $paging = SK_Model_PaymentProvider_MerchantAccount::getAll();

        $this->assertCount(1, $paging);
        $this->assertContains($merchantAccount1, $paging);
        $this->assertNotContains($merchantAccount2, $paging);
    }

    /**
     * @expectedException CM_Exception_Nonexistent
     * @expectedExceptionMessage has no data
     */
    public function testDelete() {
        $paymentProvider = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        $account = 'foo';
        $merchantAccount = SK_Model_PaymentProvider_MerchantAccount::create($paymentProvider, $account);

        $merchantAccount->delete();
        new SK_Model_PaymentProvider_MerchantAccount($merchantAccount->getId());
    }
}
