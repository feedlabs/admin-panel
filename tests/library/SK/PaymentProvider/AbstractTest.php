<?php

class SK_PaymentProviderMock extends SK_PaymentProvider_Abstract {

    public function __construct() {
        parent::__construct(1337);
    }

    public function getField1(CM_Site_Abstract $site = null) {
        return $this->getField('field1', $site);
    }

    public function getField2(CM_Site_Abstract $site = null) {
        return $this->getField('field2', $site);
    }

    public function getField3(CM_Site_Abstract $site = null) {
        return $this->getField('field3', $site);
    }

    public function getFieldNotExisting() {
        return $this->getField('notExisting');
    }

    public function cancelTransactionGroup(SK_PaymentTransactionGroup $group) {
    }

    public function getCheckoutUrl(SK_User $user, SK_ServiceBundle $serviceBundle, CM_Frontend_Render $render, SK_Model_PaymentOption $paymentOption, SK_ServiceBundleSet $serviceBundleSet = null) {
    }

    public function getFormUrl(SK_User $user, SK_ServiceBundle $serviceBundle, CM_Frontend_Render $render, SK_Model_PaymentOption $paymentOption) {
    }
}

class SK_PaymentProvider_AbstractTest extends SKTest_TestCase {

    private static $_site = null;

    public static function setUpBeforeClass() {
        CM_Db_Db::insert('sk_paymentProvider', array('id' => 1337, 'name' => 'MockProvider'));
        CM_Db_Db::insert('sk_paymentProvider_bundle', array('paymentProviderId' => 1337, 'bundleId' => 99999, 'providerBundleId' => 134));
        CM_Db_Db::insert('sk_paymentProvider_field', array('paymentProviderId' => 1337, 'name' => 'field1', 'value' => '123'));
        CM_Db_Db::insert('sk_paymentProvider_field', array('paymentProviderId' => 1337, 'name' => 'field1', 'value' => '666', 'site' => 111,));
        CM_Db_Db::insert('sk_paymentProvider_field', array('paymentProviderId' => 1337, 'name' => 'field2', 'value' => '321'));
        CM_Db_Db::insert('sk_paymentProvider_field', array('paymentProviderId' => 1337, 'name' => 'field3', 'value' => '888', 'site' => 111,));
        CM_Db_Db::insert('sk_serviceBundle', array('id'              => 99999, 'price' => 213, 'period' => 312, 'recurringPrice' => 123,
                                                   'recurringPeriod' => 321,));
    }

    public static function tearDownAfterClass() {
        CM_Db_Db::delete('sk_paymentProvider', array('id' => 1337));
        CM_Db_Db::delete('sk_paymentProvider_bundle', array('paymentProviderId' => 1337));
        CM_Db_Db::delete('sk_paymentProvider_field', array('paymentProviderId' => 1337));
        CM_Db_Db::delete('sk_serviceBundle', array('id' => 99999));
        parent::tearDownAfterClass();
    }

    public function testDecodeData() {
        $data = array('user' => 1, 'serviceBundle' => 2, 'site' => 999);
        $dataEncoded = SK_PaymentProvider_Abstract::encodeData($data);
        $dataDecoded = SK_PaymentProvider_Abstract::decodeData($dataEncoded);
        $this->assertSame($data['user'], $dataDecoded->getInt('user'));
        $this->assertSame($data['serviceBundle'], $dataDecoded->getInt('serviceBundle'));
        $this->assertSame($data['site'], $dataDecoded->getInt('site'));
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage Cannot base64_decode value
     */
    public function testDecodeDataInvalidBase64() {
        SK_PaymentProvider_Abstract::decodeData('{"user":1}');
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage Cannot json_decode value
     */
    public function testDecodeDataInvalidJson() {
        SK_PaymentProvider_Abstract::decodeData(base64_encode('{blabla}'));
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage Invalid data hash
     */
    public function testDecodeDataInvalidHash() {
        $data = array('user' => 1, 'serviceBundle' => 2, 'site' => 999);
        $dataEncoded = SK_PaymentProvider_Abstract::encodeData($data);
        $dataEncoded = base64_encode(str_replace('999', '1', base64_decode($dataEncoded)));
        SK_PaymentProvider_Abstract::decodeData($dataEncoded);
    }

    public function testEncodeData() {
        $data = array('user' => 1, 'serviceBundle' => 2, 'site' => 999);
        $dataEncoded = SK_PaymentProvider_Abstract::encodeData($data);
        $this->assertSame('eyJ1c2VyIjoxLCJzZXJ2aWNlQnVuZGxlIjoyLCJzaXRlIjo5OTksImhhc2giOiJkMmYzOGU1ZTU3N2UzZGRjNWZmMjFhODI2MDBmMDM4YzczZmJmMDlkZjA1YmQwNDgyZWRhZWVkNGZjYjUxNWY1In0=', $dataEncoded);
    }

    public function testGetBundleId() {
        $paymentProvider = new SK_PaymentProviderMock();
        $this->assertEquals(99999, $paymentProvider->getServiceBundle(134)->getId());
        try {
            $paymentProvider->getServiceBundle(135);
            $this->fail('Bundle 135 should not be linked to a ProviderBundleId.');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertTrue(true);
        }
    }

    public function testGetProviderBundleId() {
        $paymentProvider = new SK_PaymentProviderMock();
        $this->assertEquals(134, $paymentProvider->getProviderBundleId(new SK_ServiceBundle(99999)));
    }

    public function testSetProviderBundleId() {
        $paymentProvider = new SK_PaymentProviderMock();
        $serviceBundle = new SK_ServiceBundle(99999);
        $this->assertEquals(134, $paymentProvider->getProviderBundleId($serviceBundle));
        $paymentProvider->setProviderBundleId($serviceBundle, '456456wer');
        $this->assertEquals('456456wer', $paymentProvider->getProviderBundleId($serviceBundle));
        $paymentProvider = new SK_PaymentProviderMock();
        $this->assertEquals('456456wer', $paymentProvider->getProviderBundleId($serviceBundle));
        $paymentProvider->setProviderBundleId($serviceBundle, '');
        $this->assertNull($paymentProvider->getProviderBundleId($serviceBundle));
        $paymentProvider = new SK_PaymentProviderMock();
        $this->assertNull($paymentProvider->getProviderBundleId($serviceBundle));
    }

    public function testGetFieldNames() {
        $paymentProvider = new SK_PaymentProviderMock();
        $this->assertEquals(array('field1', 'field2', 'field3'), $paymentProvider->getFieldNames());
    }

    public function testGetField() {
        $paymentProvider = new SK_PaymentProviderMock();
        $this->assertEquals(123, $paymentProvider->getField('field1'));
        $this->assertEquals(321, $paymentProvider->getField('field2'));
        $this->assertNull($paymentProvider->getField('field3'));
        $this->assertEquals(666, $paymentProvider->getField('field1', $this->_getSite()));
        $this->assertEquals(321, $paymentProvider->getField('field2', $this->_getSite()));
        $this->assertEquals(888, $paymentProvider->getField('field3', $this->_getSite()));
    }

    public function testGetFieldValues() {
        $paymentProvider = new SK_PaymentProviderMock();
        $this->assertEquals(array(0 => '123', 111 => '666'), $paymentProvider->getFieldValues('field1'));
        $this->assertEquals(array(0 => '321'), $paymentProvider->getFieldValues('field2'));
        $this->assertEquals(array(111 => '888'), $paymentProvider->getFieldValues('field3'));
    }

    public function testSetFields() {
        $paymentProvider = new SK_PaymentProviderMock();
        $this->assertEquals('123', $paymentProvider->getField1());
        $this->assertEquals('321', $paymentProvider->getField2());
        $paymentProvider->setFields(array('field1' => 'hallo', 'field2' => 'echo', 'field3' => 'foo'));
        $this->assertEquals('hallo', $paymentProvider->getField1());
        $this->assertEquals('echo', $paymentProvider->getField2());
        $this->assertEquals('foo', $paymentProvider->getField3());

        $paymentProvider = new SK_PaymentProviderMock();
        $paymentProvider->setFields(array('field1' => 'hallo', 'field2' => 'echo', 'field3' => 'foo'), $this->_getSite());
        $this->assertEquals('hallo', $paymentProvider->getField1(), $this->_getSite());
        $this->assertEquals('echo', $paymentProvider->getField2(), $this->_getSite());
        $this->assertEquals('foo', $paymentProvider->getField3(), $this->_getSite());

        $object = (object) array();
        try {
            $paymentProvider->setFields(array('field1' => 'new', 'field2' => $object));
            $this->fail('Could set a object as a field');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertEquals('new', $paymentProvider->getField1());
        $this->assertEquals('echo', $paymentProvider->getField2());
        $this->assertEquals('foo', $paymentProvider->getField3());
    }

    public function testNonExistingField() {
        $paymentProvider = new SK_PaymentProviderMock();

        try {
            $paymentProvider->getFieldNotExisting();
            $this->fail('Could get non existing field');
        } catch (CM_Exception_Invalid $e) {
            $this->assertContains('Provider `1337` does not have a field `notExisting`', $e->getMessage());
        }

        try {
            $paymentProvider->setFields(array('notExisting' => 'foo'));
            $this->fail('Could set non existing field');
        } catch (CM_Exception_Invalid $e) {
            $this->assertContains('is not in the field list', $e->getMessage());
        }
    }

    public function testOnCreateTransactionGroupServiceBundleTransaction() {
        $provider = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ROCKETGATE);
        $serviceBundle = SKTest_TH::createServiceBundle(35, 10);
        $subscriptionKey = 'foo';
        $transactionKey = 'foo';
        $merchantAccount = 'foo';
        $user = SKTest_TH::createUser();
        $affiliateProvider1 = new SK_AffiliateProvider_UserTemplate();
        $affiliateProvider2 = new SK_AffiliateProvider_Offerit();

        $affiliateP1A1 = SK_Model_Affiliate::createStatic(array('provider' => $affiliateProvider1));
        $affiliateP1A2 = SK_Model_Affiliate::createStatic(array('provider' => $affiliateProvider1));
        $affiliateP2A1 = SK_Model_Affiliate::createStatic(array('provider' => $affiliateProvider2));
        $affiliateP1A1->addUser($user);
        $affiliateP1A2->addUser($user);
        $affiliateP2A1->addUser($user);

        $this->assertNull(SK_PaymentTransactionGroup::find($subscriptionKey, $provider));
        $this->assertNull(SK_PaymentTransaction_Abstract::find($transactionKey, $provider));
        $this->assertTrue($affiliateP1A1->getPaymentTransactionGroupList()->isEmpty());
        $this->assertTrue($affiliateP1A2->getPaymentTransactionGroupList()->isEmpty());
        $this->assertTrue($affiliateP2A1->getPaymentTransactionGroupList()->isEmpty());

        $provider->onCreateTransactionGroupServiceBundleTransaction($user->getId(), $subscriptionKey, $transactionKey, $merchantAccount, 10, $serviceBundle, $this->getMockSite('SK_Site_Abstract'), 1);

        $paymentTransactionGroup = SK_PaymentTransactionGroup::find($subscriptionKey, $provider);
        /** @var SK_PaymentTransaction_ServiceBundle $paymentTransaction */
        $paymentTransaction = SK_PaymentTransaction_ServiceBundle::find($transactionKey, $provider);
        $this->assertInstanceOf('SK_PaymentTransactionGroup', $paymentTransactionGroup);
        $this->assertInstanceOf('SK_PaymentTransaction_ServiceBundle', $paymentTransaction);
        $this->assertEquals($serviceBundle, $paymentTransaction->getServiceBundle());
        $this->assertEquals($user, $paymentTransaction->getUser());
        $this->assertEquals($user, $paymentTransactionGroup->getUser());
        $this->assertEquals($paymentTransactionGroup, $paymentTransaction->getGroup());
        $this->assertSame(1, $paymentTransaction->getGroupSequence());
        $this->assertEquals(array($paymentTransactionGroup), $affiliateP1A1->getPaymentTransactionGroupList());
        $this->assertEquals(array($paymentTransactionGroup), $affiliateP2A1->getPaymentTransactionGroupList());
        $this->assertEquals(array($paymentTransactionGroup), $affiliateP1A2->getPaymentTransactionGroupList());
        $this->assertSame(0.5, $affiliateP1A1->getWeight($paymentTransactionGroup));
        $this->assertSame(0.5, $affiliateP1A2->getWeight($paymentTransactionGroup));
        $this->assertSame(1.0, $affiliateP2A1->getWeight($paymentTransactionGroup));
    }

    public function testGetSupportEmailAddress() {
        CM_Config::get()->SK_PaymentProvider_Abstract->supportEmailAddress = 'foo@example.com';
        $paymentProvider = new SK_PaymentProviderMock();

        $this->assertSame('foo@example.com', $paymentProvider->getSupportEmailAddress());

        SKTest_TH::clearConfig();
    }

    private function _getSite() {
        if (null === self::$_site) {
            self::$_site = $this->getMockSite('SK_Site_Abstract', 111, null, array('getId'));
        }
        self::$_site->expects($this->any())->method('getId')->will($this->returnValue('111'));

        return self::$_site;
    }
}
