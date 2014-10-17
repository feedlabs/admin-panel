<?php

class SK_Service_CoinTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testConstruct() {
        $coin = new SK_Service_Coin(10);
        $this->assertSame(10, $coin->getAmount());
    }

    public function testProcess() {
        $coin = new SK_Service_Coin(10);
        $user = SKTest_TH::createUser();
        $transaction = SKTest_TH::createPaymentTransaction($user->getId());
        $this->assertSame(0, $user->getCoins()->getBalance());

        $coin->process($transaction);
        SKTest_TH::reinstantiateModel($user);

        $this->assertSame(10, $user->getCoins()->getBalance());
    }

    public function testProcessSubscription() {
        $coin = new SK_Service_Coin(10);
        $user = SKTest_TH::createUser();
        $serviceBundle = SKTest_TH::createServiceBundle();
        $serviceBundle->getInitialServices()->add($coin);
        $serviceBundle->getInitialServices()->add(new SK_Service_Premium(20));
        $transaction = SKTest_TH::createPaymentTransaction($user->getId(), null, null, null, $serviceBundle);
        $coin->process($transaction);
        SKTest_TH::reinstantiateModel($user);
        $coinTransaction = $user->getCoins()->getTransactions()->getItem(0);

        $this->assertSame(SK_CoinTransaction_Payment::getTypeStatic(), $coinTransaction->getType());
    }

    public function testProcessCoinOnly() {
        $coin = new SK_Service_Coin(10);
        $user = SKTest_TH::createUser();
        $serviceBundle = SKTest_TH::createServiceBundle();
        $serviceBundle->getInitialServices()->add($coin);
        $transaction = SKTest_TH::createPaymentTransaction($user->getId(), null, null, null, $serviceBundle);
        $coin->process($transaction);
        SKTest_TH::reinstantiateModel($user);
        $coinTransaction = $user->getCoins()->getTransactions()->getItem(0);

        $this->assertSame(SK_CoinTransaction_PaymentCoinOnly::getTypeStatic(), $coinTransaction->getType());
    }
}
