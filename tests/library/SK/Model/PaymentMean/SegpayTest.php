<?php

class SK_Model_PaymentMean_SegpayTest extends SKTest_TestCase {

    /** @var SK_User $_user */
    private $_user;

    /** @var SK_Model_PaymentMean_Segpay $_paymentMean */
    private $_paymentMean;

    public function setUp() {
        $this->_user = SKTest_TH::createUser();
        $this->_paymentMean = SK_Model_PaymentMean_Segpay::createStatic(array(
            'user'           => $this->_user,
            'username'       => 'foo',
            'userpass'       => 'bar',
            'usermail'       => 'foo@bar.com',
            'subscriptionId' => '111',
        ));
    }

    public static function tearDownAfterClass() {
        CM_Db_Db::truncate('sk_paymentMean_segpay');
        parent::tearDownAfterClass();
    }

    public function testCreate() {
        $this->assertEquals($this->_user, $this->_paymentMean->getUser());
        $this->assertEquals('foo', $this->_paymentMean->getUserName());
        $this->assertEquals('bar', $this->_paymentMean->getUserPassword());
        $this->assertEquals('foo@bar.com', $this->_paymentMean->getUserMail());
        $this->assertEquals('111', $this->_paymentMean->getSubscriptionId());
        $this->assertEquals(time(), $this->_paymentMean->getCreateStamp());
    }

    public function testUpdate() {
        $this->assertEquals('foo', $this->_paymentMean->getUserName());
        $this->assertEquals('bar', $this->_paymentMean->getUserPassword());
        $this->assertEquals('foo@bar.com', $this->_paymentMean->getUserMail());
        $this->assertEquals('111', $this->_paymentMean->getSubscriptionId());
        $this->assertEquals(time(), $this->_paymentMean->getCreateStamp());

        $this->_paymentMean->update('foo2', 'bar2', 'foo2@bar.com', '222', time() + 1);

        $this->assertEquals('foo2', $this->_paymentMean->getUserName());
        $this->assertEquals('bar2', $this->_paymentMean->getUserPassword());
        $this->assertEquals('foo2@bar.com', $this->_paymentMean->getUserMail());
        $this->assertEquals('222', $this->_paymentMean->getSubscriptionId());
        $this->assertEquals(time() + 1, $this->_paymentMean->getCreateStamp());
    }

    public function testFindByUser() {
        $paymentMeanList = SK_Model_PaymentMean_Segpay::findByUser($this->_user);
        $this->assertEquals($this->_paymentMean, $paymentMeanList[0]);
    }

    /**
     * @expectedException CM_Exception_Nonexistent
     * @expectedExceptionMessage has no data
     */
    public function testDelete() {
        $this->_paymentMean->delete();
        new SK_Model_PaymentMean_Segpay($this->_paymentMean->getId());
    }
}
