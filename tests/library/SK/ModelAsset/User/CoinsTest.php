<?php

class SK_ModelAsset_User_CoinsTest extends SKTest_TestCase {

    public function testGetBalance() {
        $user = SKTest_TH::createUser();
        $this->assertEquals(0, $user->getCoins()->getBalance());
        SKTest_TH::createCoinTransactionAdminGive($user, null, 3);
        SKTest_TH::reinstantiateModel($user);
        $this->assertEquals(1, $user->getCoins()->getTransactions()->getCount());
        $this->assertEquals(3, $user->getCoins()->getBalance());
        SKTest_TH::createCoinTransactionAdminGive($user, null, -1);
        SKTest_TH::reinstantiateModel($user);
        $this->assertEquals(2, $user->getCoins()->getTransactions()->getCount());
        $this->assertEquals(2, $user->getCoins()->getBalance());
    }
}
