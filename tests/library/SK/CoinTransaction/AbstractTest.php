<?php

class SK_CoinTransaction_AbstractTest extends SKTest_TestCase {

    public static function setUpBeforeClass() {
        CM_Db_Db::exec("CREATE TABLE IF NOT EXISTS `sk_coinTransaction_abstractMock` (
			`id` INT UNSIGNED NOT NULL,
			`foo` INT UNSIGNED NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $this->assertSame(0, $user->getCoins()->getTransactions()->getCount());
        /** @var SK_CoinTransaction_AbstractMock $coinTransaction */
        $coinTransaction = SK_CoinTransaction_AbstractMock::createStatic(array('user' => $user, 'amount' => 4, 'foo' => 'foo'));
        $this->assertRow('sk_coinTransaction', array('id' => $coinTransaction->getId(), 'userId' => $user->getId(), 'amount' => 4));
        $this->assertRow('sk_coinTransaction_abstractMock', array('id' => $coinTransaction->getId(), 'foo' => 'foobar'));
        $this->assertInstanceOf('SK_CoinTransaction_Abstract', $coinTransaction);
        $this->assertEquals($user->getId(), $coinTransaction->getUserId());
        $this->assertEquals(4, $coinTransaction->getAmount());
        $this->assertGreaterThan(0, $coinTransaction->getCreated());
        $this->assertSame(1, $user->getCoins()->getTransactions()->getCount());

        try {
            SK_CoinTransaction_AbstractMock::createStatic(array('user' => $user, 'amount' => -5, 'foo' => 'foo'));
            $this->fail('Can create transactions that cost more than the current balance.');
        } catch (SK_Exception_InsufficientFunds $ex) {
            $this->assertTrue(true);
        }

        $this->assertSame(4, $user->getCoins()->getBalance());
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage Cannot create coinTransaction with amount 0
     */
    public function testCreateZero() {
        $user = SKTest_TH::createUser();
        SK_CoinTransaction_AbstractMock::createStatic(array('user' => $user, 'amount' => 0, 'foo' => 'foo'));
    }

    public function testDelete() {
        $user = SKTest_TH::createUser();
        /** @var SK_CoinTransaction_AbstractMock $coinTransaction */
        $coinTransaction = SK_CoinTransaction_AbstractMock::createStatic(array('user' => $user, 'amount' => 4, 'foo' => 'foo'));

        $this->assertSame(4, $user->getCoins()->getBalance());
        $this->assertRow('sk_coinTransaction', array('id' => $coinTransaction->getId(), 'userId' => $user->getId(), 'amount' => 4));
        $this->assertRow('sk_coinTransaction_abstractMock', array('id' => $coinTransaction->getId(), 'foo' => 'foobar'));

        $coinTransaction->delete();

        SKTest_TH::reinstantiateModel($user);
        $this->assertSame(0, $user->getCoins()->getBalance());
        $this->assertNotRow('sk_coinTransaction', array('id' => $coinTransaction->getId(), 'userId' => $user->getId(), 'amount' => 4));
        $this->assertNotRow('sk_coinTransaction_abstractMock', array('id' => $coinTransaction->getId(), 'foo' => 'foobar'));
    }

    public function testGetUser() {
        $user = SKTest_TH::createUser();
        /** @var SK_CoinTransaction_AbstractMock $coinTransaction */
        $coinTransaction = SK_CoinTransaction_AbstractMock::createStatic(array('user' => $user, 'amount' => 3, 'foo' => 'foobar'));
        $this->assertEquals($user, $coinTransaction->getUser());
        $user->delete();
        $this->assertNull($coinTransaction->getUser());
    }
}

class SK_CoinTransaction_AbstractMock extends SK_CoinTransaction_Abstract {

    public static function getTypeStatic() {
        return 255;
    }

    protected static function _createData(array $data) {
        $foo = $data['foo'] . 'bar';
        return array('foo' => $foo);
    }

    protected static function _getTableName() {
        return 'sk_coinTransaction_abstractMock';
    }
}
