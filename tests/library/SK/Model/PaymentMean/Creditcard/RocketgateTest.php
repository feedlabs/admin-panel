<?php

class SK_Model_PaymentMean_Creditcard_RocketgateTest extends SKTest_TestCase {

    /** @var SK_User $_affiliate */
    private $_user;

    /** @var string $_hash */
    private $_hash;

    /** @var SK_Model_PaymentMean_Creditcard_Rocketgate $_affiliate */
    private $_rocketgateCreditcard;

    public function setUp() {
        $this->_user = SKTest_TH::createUser();
        $this->_hash = '123foo';
        $this->_rocketgateCreditcard = SK_Model_PaymentMean_Creditcard_Rocketgate::createStatic(array(
            'user'            => $this->_user,
            'cardType'        => 6,
            'hash'            => $this->_hash,
            'holder'          => 'Mr. Foo Bar',
            'lastFour'        => '0123',
            'expirationMonth' => 9,
            'expirationYear'  => 2015,
        ));
    }

    public static function tearDownAfterClass() {
        CM_Db_Db::truncate('sk_paymentMean_creditcard_rocketgate');
        parent::tearDownAfterClass();
    }

    public function testCreate() {
        $this->assertEquals($this->_user, $this->_rocketgateCreditcard->getUser());
        $this->assertSame(6, $this->_rocketgateCreditcard->getCardType());
        $this->assertSame('123foo', $this->_rocketgateCreditcard->getHash());
        $this->assertSame('Mr. Foo Bar', $this->_rocketgateCreditcard->getHolder());
        $this->assertSame('0123', $this->_rocketgateCreditcard->getLastFour());
        $this->assertSame(9, $this->_rocketgateCreditcard->getExpirationMonth());
        $this->assertSame(2015, $this->_rocketgateCreditcard->getExpirationYear());
    }

    public function testFindByHashAndUser() {
        $this->assertEquals($this->_rocketgateCreditcard, SK_Model_PaymentMean_Creditcard_Rocketgate::findByHashAndUser($this->_hash, $this->_user));
    }

    public function testFindByUser() {
        $paymentMeanList = SK_Model_PaymentMean_Creditcard_Rocketgate::findByUser($this->_user);
        $this->assertEquals(1, count($paymentMeanList));
        $this->assertEquals($this->_rocketgateCreditcard, $paymentMeanList[0]);

        $rocketgateCreditcard2 = SK_Model_PaymentMean_Creditcard_Rocketgate::createStatic(array(
            'user'            => $this->_user,
            'cardType'        => 6,
            'hash'            => 'test123',
            'holder'          => 'Mr. Foo Bar',
            'lastFour'        => '0123',
            'expirationMonth' => 9,
            'expirationYear'  => 2015,
        ));

        $paymentMeanList = SK_Model_PaymentMean_Creditcard_Rocketgate::findByUser($this->_user);
        $this->assertEquals(2, count($paymentMeanList));
        $this->assertEquals($rocketgateCreditcard2, $paymentMeanList[0]);
        $this->assertEquals($this->_rocketgateCreditcard, $paymentMeanList[1]);
    }

    /**
     * @expectedException CM_Exception_Nonexistent
     * @expectedExceptionMessage has no data
     */
    public function testDelete() {
        $this->_rocketgateCreditcard->delete();
        new SK_Model_PaymentMean_Creditcard_Rocketgate($this->_rocketgateCreditcard->getId());
    }
}
