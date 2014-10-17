<?php

class SK_CoinTransaction_AdminGiveTest extends SKTest_TestCase {

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $admin = SKTest_TH::createUser();
        $this->assertSame(0, $user->getCoins()->getTransactions()->getCount());
        /** @var SK_CoinTransaction_AdminGive $coinTransaction */
        $coinTransaction = SK_CoinTransaction_AdminGive::createStatic(array('user' => $user, 'amount' => 4, 'admin' => $admin));
        $this->assertRow('sk_coinTransaction', array('id' => $coinTransaction->getId(), 'userId' => $user->getId(), 'amount' => 4));
        $this->assertRow('sk_coinTransaction_adminGive', array('id' => $coinTransaction->getId(), 'adminId' => $admin->getId()));
        $this->assertInstanceOf('SK_CoinTransaction_AdminGive', $coinTransaction);
        $this->assertEquals($user->getId(), $coinTransaction->getUserId());
        $this->assertEquals($admin->getId(), $coinTransaction->getAdminId());
        $this->assertEquals($admin, $coinTransaction->getAdmin());
    }

    public function testGetAdmin() {
        $admin = SKTest_TH::createUser();
        /** @var SK_CoinTransaction_AdminGive $coinTransaction */
        $coinTransaction = SKTest_TH::createCoinTransactionAdminGive(null, $admin);
        $this->assertEquals($admin, $coinTransaction->getAdmin());
        $admin->delete();
        $this->assertNull($coinTransaction->getAdmin());
    }
}
