<?php

class SK_AffiliateProvider_AbstractTest extends SKTest_TestCase {

    public function setUp() {
        CM_Config::get()->SK_AffiliateProvider_Abstract = new \stdClass();
        CM_Config::get()->SK_AffiliateProvider_Abstract->enableTracking = true;
    }

    protected function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testOnSignUp() {
        $user = SKTest_TH::createUser();
        $provider = $this->getMockForAbstractClass('SK_AffiliateProvider_Abstract', array(), '', false, true, true, array('_notifySignUp', 'getType'));
        $affiliate = $this->getMock('SK_Model_Affiliate');

        $affiliate->expects($this->once())->method('getProvider')->will($this->returnValue($provider));
        $provider->expects($this->once())->method('_notifySignUp')->with($this->equalTo($affiliate), $this->equalTo($user));
        $provider->expects($this->any())->method('getType')->will($this->returnValue(99));

        $provider->onSignUp($affiliate, $user);
    }

    public function testOnSubscription() {
        $paymentProvider = SKTest_TH::createPaymentProvider();
        $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'a', 1);
        $provider = $this->getMockForAbstractClass('SK_AffiliateProvider_Abstract', array(), '', false, true, true, array('_notifySubscription', 'getType'));
        $affiliate = $this->getMock('SK_Model_Affiliate');

        $affiliate->expects($this->once())->method('getProvider')->will($this->returnValue($provider));
        $provider->expects($this->once())->method('_notifySubscription')->with($this->equalTo($affiliate), $this->equalTo($transactionGroup));
        $provider->expects($this->any())->method('getType')->will($this->returnValue(99));

        $provider->onSubscription($affiliate, $transactionGroup);
    }

    public function testOnRebill() {
        $paymentProvider = SKTest_TH::createPaymentProvider();
        $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'a', 1);
        $provider = $this->getMockForAbstractClass('SK_AffiliateProvider_Abstract', array(), '', false, true, true, array('_notifyRebill', 'getType'));
        $affiliate = $this->getMock('SK_Model_Affiliate');

        $affiliate->expects($this->once())->method('getProvider')->will($this->returnValue($provider));
        $provider->expects($this->once())->method('_notifyRebill')->with($this->equalTo($affiliate), $this->equalTo($transactionGroup));
        $provider->expects($this->any())->method('getType')->will($this->returnValue(99));

        $provider->onRebill($affiliate, $transactionGroup);
    }

    public function testOnChargeback() {
        $user = SKTest_TH::createUser();
        $transaction = SKTest_TH::createPaymentTransaction($user->getId());
        $provider = $this->getMockForAbstractClass('SK_AffiliateProvider_Abstract', array(), '', false, true, true, array('_notifyChargeback', 'getType'));
        $affiliate = $this->getMock('SK_Model_Affiliate');

        $affiliate->expects($this->once())->method('getProvider')->will($this->returnValue($provider));
        $provider->expects($this->once())->method('_notifyChargeback')->with($this->equalTo($affiliate), $this->equalTo($transaction));
        $provider->expects($this->any())->method('getType')->will($this->returnValue(99));

        $provider->onChargeback($affiliate, $transaction);
    }

    public function testOnRefund() {
        $user = SKTest_TH::createUser();
        $transaction = SKTest_TH::createPaymentTransaction($user->getId());
        $provider = $this->getMockForAbstractClass('SK_AffiliateProvider_Abstract', array(), '', false, true, true, array('_notifyRefund', 'getType'));
        $affiliate = $this->getMock('SK_Model_Affiliate');

        $affiliate->expects($this->once())->method('getProvider')->will($this->returnValue($provider));
        $provider->expects($this->once())->method('_notifyRefund')->with($this->equalTo($affiliate), $this->equalTo($transaction));
        $provider->expects($this->any())->method('getType')->will($this->returnValue(99));

        $provider->onRefund($affiliate, $transaction);
    }

    public function testOnVoid() {
        $user = SKTest_TH::createUser();
        $transaction = SKTest_TH::createPaymentTransaction($user->getId());
        $provider = $this->getMockForAbstractClass('SK_AffiliateProvider_Abstract', array(), '', false, true, true, array('_notifyVoid', 'getType'));
        $affiliate = $this->getMock('SK_Model_Affiliate');

        $affiliate->expects($this->once())->method('getProvider')->will($this->returnValue($provider));
        $provider->expects($this->once())->method('_notifyVoid')->with($this->equalTo($affiliate), $this->equalTo($transaction));
        $provider->expects($this->any())->method('getType')->will($this->returnValue(99));

        $provider->onVoid($affiliate, $transaction);
    }
}
