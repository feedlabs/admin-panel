<?php

class SK_Model_PaymentMean_AbstractTest extends SKTest_TestCase {

    public static function tearDownAfterClass() {
        CM_Db_Db::truncate('sk_paymentMean_ccbill');
        CM_Db_Db::truncate('sk_paymentMean_segpay');
        CM_Db_Db::truncate('sk_paymentMean_creditcard_rocketgate');
        parent::tearDownAfterClass();
    }

    public function testFindByUser() {
        $user = SKTest_TH::createUser();
        $paymentMeanList = SK_Model_PaymentMean_Abstract::findByUser($user);
        $this->assertEmpty($paymentMeanList);

        $paymentMeanCcbill = SK_Model_PaymentMean_Ccbill::createStatic(array(
            'user'           => $user,
            'username'       => 'foo',
            'userpass'       => 'bar',
            'usermail'       => 'foo@bar.com',
            'subscriptionId' => '111',
        ));

        $paymentMeanSegpay = SK_Model_PaymentMean_Segpay::createStatic(array(
            'user'           => $user,
            'username'       => 'foo',
            'userpass'       => 'bar',
            'usermail'       => 'foo@bar.com',
            'subscriptionId' => '111',
        ));

        $paymentMeanRocketgate = SK_Model_PaymentMean_Creditcard_Rocketgate::createStatic(array(
            'user'            => $user,
            'cardType'        => 6,
            'hash'            => '111',
            'holder'          => 'Mr. Foo Bar',
            'lastFour'        => '0123',
            'expirationMonth' => 9,
            'expirationYear'  => 2015,
        ));

        $paymentMeanRocketgate2 = SK_Model_PaymentMean_Creditcard_Rocketgate::createStatic(array(
            'user'            => $user,
            'cardType'        => 6,
            'hash'            => '222',
            'holder'          => 'Mr. Foo Bar',
            'lastFour'        => '0123',
            'expirationMonth' => 9,
            'expirationYear'  => 2015,
        ));

        $paymentMeanList = SK_Model_PaymentMean_Abstract::findByUser($user);

        $this->assertEquals(4, count($paymentMeanList));
        $this->assertEquals($paymentMeanCcbill, $paymentMeanList[0]);
        $this->assertEquals($paymentMeanSegpay, $paymentMeanList[1]);
        $this->assertEquals($paymentMeanRocketgate2, $paymentMeanList[2]);
        $this->assertEquals($paymentMeanRocketgate, $paymentMeanList[3]);
    }

    public function testFindFirstByUser() {
        $user = SKTest_TH::createUser();

        $this->assertNull(SK_Model_PaymentMean_Ccbill::findFirstByUser($user));

        $paymentMeanCcbill = SK_Model_PaymentMean_Ccbill::createStatic(array(
            'user'           => $user,
            'username'       => 'foo',
            'userpass'       => 'bar',
            'usermail'       => 'foo@bar.com',
            'subscriptionId' => '111',
        ));

        $this->assertEquals($paymentMeanCcbill, SK_Model_PaymentMean_Ccbill::findFirstByUser($user));

        $paymentMeanSegpay = SK_Model_PaymentMean_Segpay::createStatic(array(
            'user'           => $user,
            'username'       => 'foo',
            'userpass'       => 'bar',
            'usermail'       => 'foo@bar.com',
            'subscriptionId' => '111',
        ));

        $this->assertEquals($paymentMeanSegpay, SK_Model_PaymentMean_Segpay::findFirstByUser($user));

        $paymentMeanRocketgate = SK_Model_PaymentMean_Creditcard_Rocketgate::createStatic(array(
            'user'            => $user,
            'cardType'        => 6,
            'hash'            => '111',
            'holder'          => 'Mr. Foo Bar',
            'lastFour'        => '0123',
            'expirationMonth' => 9,
            'expirationYear'  => 2015,
        ));
        $paymentMeanRocketgate2 = SK_Model_PaymentMean_Creditcard_Rocketgate::createStatic(array(
            'user'            => $user,
            'cardType'        => 6,
            'hash'            => '222',
            'holder'          => 'Mr. Foo Bar',
            'lastFour'        => '0123',
            'expirationMonth' => 9,
            'expirationYear'  => 2015,
        ));

        $this->assertEquals($paymentMeanRocketgate2, SK_Model_PaymentMean_Creditcard_Rocketgate::findFirstByUser($user));
    }
}
