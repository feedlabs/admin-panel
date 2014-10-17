<?php

class SK_Model_AffiliateTest extends SKTest_TestCase {

    /** @var SK_Model_Affiliate */
    private $_affiliate1;

    /** @var SK_Model_Affiliate */
    private $_affiliate2;

    public function setUp() {
        $this->_affiliate1 = SK_Model_Affiliate::createStatic(array(
            'label'       => 'foo',
            'provider'    => new SK_AffiliateProvider_Internal(),
            'createStamp' => time(),
        ));
        $this->_affiliate2 = SK_Model_Affiliate::createStatic(array(
            'label'       => 'foo',
            'provider'    => new SK_AffiliateProvider_Offerit(),
            'createStamp' => time(),
        ));
    }

    public function tearDown() {
        CM_Db_Db::truncate('sk_affiliateProvider');
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $this->assertSame(16, strlen($this->_affiliate1->getCode()));
        $this->assertSame('foo', $this->_affiliate1->getLabel());
        $this->assertSameTime(time(), $this->_affiliate1->getCreated());
    }

    public function testDuplicateCreate() {
        $code = 'foo';
        $label = 'bar';
        $provider = new SK_AffiliateProvider_Internal();
        $createStamp = time();
        $affiliate1 = SK_Model_Affiliate::createStatic(array(
            'code'        => $code,
            'label'       => $label,
            'provider'    => $provider,
            'createStamp' => $createStamp,
        ));
        $affiliate2 = SK_Model_Affiliate::createStatic(array(
            'code'        => $code,
            'label'       => $label,
            'provider'    => $provider,
            'createStamp' => $createStamp,
        ));
        $this->assertEquals($affiliate1->getId(), $affiliate2->getId());
    }

    public function testAddFindRequest() {
        $request = new CM_Request_Get('/');
        $this->assertCount(0, SK_Model_Affiliate::findByRequest($request));

        $this->_affiliate1->addRequest($request);
        $this->assertCount(1, SK_Model_Affiliate::findByRequest($request));
        $this->assertContains($this->_affiliate1, SK_Model_Affiliate::findByRequest($request));
        $this->_affiliate2->addRequest($request);
        $this->assertCount(2, SK_Model_Affiliate::findByRequest($request));
        $this->assertContains($this->_affiliate1, SK_Model_Affiliate::findByRequest($request));
        $this->assertContains($this->_affiliate2, SK_Model_Affiliate::findByRequest($request));
    }

    public function testAddFindUser() {
        $user = SKTest_TH::createUser();
        $this->assertCount(0, SK_Model_Affiliate::findByUserId($user->getId()));

        $this->_affiliate1->addUser($user);
        $this->assertCount(1, SK_Model_Affiliate::findByUserId($user->getId()));
        $this->assertContains($this->_affiliate1, SK_Model_Affiliate::findByUserId($user->getId()));
        $this->_affiliate2->addUser($user);
        $this->assertCount(2, SK_Model_Affiliate::findByUserId($user->getId()));
        $this->assertContains($this->_affiliate1, SK_Model_Affiliate::findByUserId($user->getId()));
        $this->assertContains($this->_affiliate2, SK_Model_Affiliate::findByUserId($user->getId()));
    }

    public function testAddUserMultipleAffiliationsAllowed() {
        $user = SKTest_TH::createUser();
        $affiliateProvider = new SK_AffiliateProvider_UserTemplate();
        /** @var SK_Model_Affiliate $affiliate1 */
        $affiliate1 = SK_Model_Affiliate::createStatic(array('label'       => 'foo1',
                                                             'provider'    => $affiliateProvider,
                                                             'createStamp' => time()));
        /** @var SK_Model_Affiliate $affiliate2 */
        $affiliate2 = SK_Model_Affiliate::createStatic(array('label'       => 'foo2',
                                                             'provider'    => $affiliateProvider,
                                                             'createStamp' => time()));

        $this->assertRow('sk_affiliate_user', array('providerId' => $affiliateProvider->getId()), 0);
        $affiliate1->addUser($user);
        $this->assertRow('sk_affiliate_user', array('providerId' => $affiliateProvider->getId()), 1);
        $affiliate1->addUser($user);
        $this->assertRow('sk_affiliate_user', array('providerId' => $affiliateProvider->getId()), 1);
        $affiliate2->addUser($user);
        $this->assertRow('sk_affiliate_user', array('providerId' => $affiliateProvider->getId()), 2);
    }

    public function testAddUserMultipleAffiliationsDisallowed() {
        $user = SKTest_TH::createUser();
        /** @var SK_Model_Affiliate $affiliate1 */
        $affiliateProvider = new SK_AffiliateProvider_Internal();
        $affiliate1 = SK_Model_Affiliate::createStatic(array('label'       => 'foo1',
                                                             'provider'    => $affiliateProvider,
                                                             'createStamp' => time()));
        /** @var SK_Model_Affiliate $affiliate2 */
        $affiliate2 = SK_Model_Affiliate::createStatic(array('label'       => 'foo2',
                                                             'provider'    => $affiliateProvider,
                                                             'createStamp' => time()));

        $this->assertRow('sk_affiliate_user', array('providerId' => $affiliateProvider->getId()), 0);
        $affiliate1->addUser($user);
        $this->assertRow('sk_affiliate_user', array('providerId' => $affiliateProvider->getId()), 1);
        $affiliate2->addUser($user);
        $this->assertRow('sk_affiliate_user', array('providerId' => $affiliateProvider->getId()), 1);
    }

    public function testAddFindTransactionGroup() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SKTest_TH::createPaymentProvider();
        $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'foo', $user->getId());
        $this->assertCount(0, SK_Model_Affiliate::findByTransactionGroup($transactionGroup));

        $this->_affiliate1->addTransactionGroup($transactionGroup);
        $this->assertRow('sk_affiliate_transactionGroup', array('providerId'         => $this->_affiliate1->getProvider()->getId(),
                                                                'transactionGroupId' => $transactionGroup->getId(), 'weight' => 1));
        $this->assertEquals(array($this->_affiliate1), SK_Model_Affiliate::findByTransactionGroup($transactionGroup));

        $this->_affiliate2->addTransactionGroup($transactionGroup, 0.5);
        $this->assertRow('sk_affiliate_transactionGroup', array('providerId'         => $this->_affiliate2->getProvider()->getId(),
                                                                'transactionGroupId' => $transactionGroup->getId(), 'weight' => 0.5));
        $this->assertEquals(array($this->_affiliate1, $this->_affiliate2), SK_Model_Affiliate::findByTransactionGroup($transactionGroup));

        try {
            $this->_affiliate1->addTransactionGroup($transactionGroup, 1.1);
            $this->fail('Added transaction using invalid weight');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('Invalid weight', $ex->getMessage());
            $this->assertEquals(array('weight' => 1.1), $ex->getMetaInfo());
        }
    }

    public function testAddTransactionGroupMultipleAffiliationsAllowed() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SKTest_TH::createPaymentProvider();
        $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'foo', $user->getId());
        $affiliateProvider = new SK_AffiliateProvider_UserTemplate();
        /** @var SK_Model_Affiliate $affiliate1 */
        $affiliate1 = SK_Model_Affiliate::createStatic(array('label'       => 'foo1',
                                                             'provider'    => $affiliateProvider,
                                                             'createStamp' => time()));
        /** @var SK_Model_Affiliate $affiliate2 */
        $affiliate2 = SK_Model_Affiliate::createStatic(array('label'       => 'foo2',
                                                             'provider'    => $affiliateProvider,
                                                             'createStamp' => time()));

        $this->assertRow('sk_affiliate_transactionGroup', array('providerId' => $affiliateProvider->getId()), 0);
        $affiliate1->addTransactionGroup($transactionGroup);
        $this->assertRow('sk_affiliate_transactionGroup', array('providerId' => $affiliateProvider->getId()), 1);
        $affiliate1->addTransactionGroup($transactionGroup);
        $this->assertRow('sk_affiliate_transactionGroup', array('providerId' => $affiliateProvider->getId()), 1);
        $affiliate2->addTransactionGroup($transactionGroup);
        $this->assertRow('sk_affiliate_transactionGroup', array('providerId' => $affiliateProvider->getId()), 2);
    }

    public function testAddTransactionGroupMultipleAffiliationsDisallowed() {
        $user = SKTest_TH::createUser();
        $paymentProvider = SKTest_TH::createPaymentProvider();
        $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'foo', $user->getId());
        $affiliateProvider = new SK_AffiliateProvider_Internal();
        /** @var SK_Model_Affiliate $affiliate1 */
        $affiliate1 = SK_Model_Affiliate::createStatic(array('label'       => 'foo1',
                                                             'provider'    => $affiliateProvider,
                                                             'createStamp' => time()));
        /** @var SK_Model_Affiliate $affiliate2 */
        $affiliate2 = SK_Model_Affiliate::createStatic(array('label'       => 'foo2',
                                                             'provider'    => $affiliateProvider,
                                                             'createStamp' => time()));

        $this->assertRow('sk_affiliate_transactionGroup', array('providerId' => $affiliateProvider->getId()), 0);
        $affiliate1->addTransactionGroup($transactionGroup);
        $this->assertRow('sk_affiliate_transactionGroup', array('providerId' => $affiliateProvider->getId()), 1);
        try {
            $affiliate2->addTransactionGroup($transactionGroup);
            $this->fail('Could add transactionGroup to multiple affiliates with payment provider that doesn\'t allow multiple affiliations');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('Multiple affiliates with the same transaction group not allowed for affiliate Provider `SK_AffiliateProvider_Internal`', $ex->getMessage());
        }
        $this->assertRow('sk_affiliate_transactionGroup', array('providerId' => $affiliateProvider->getId()), 1);
    }

    public function testGetWeight() {
        $paymentProvider = SKTest_TH::createPaymentProvider();
        $transactionGroup = SKTest_TH::createPaymentTransactionGroup($paymentProvider, 'foo1', SKTest_TH::createUser()->getId());
        $this->_affiliate1->addTransactionGroup($transactionGroup, 0.75);

        $this->assertSame(0.75, $this->_affiliate1->getWeight($transactionGroup));
        try {
            $this->_affiliate2->getWeight($transactionGroup);
            $this->fail('Fetched weight of foreign transactionGroup');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('Affiliate does not contain transactionGroup', $ex->getMessage());
        }
    }

    public function testRequestBot() {
        $request = new CM_Request_Get('/', array('User-Agent' => 'GoogleBot'));
        $this->assertCount(0, SK_Model_Affiliate::findByRequest($request));

        $this->_affiliate1->addRequest($request);
        $this->assertCount(0, SK_Model_Affiliate::findByRequest($request));
    }

    public function testDelete() {
        $request = new CM_Request_Get('/');
        $this->_affiliate1->addRequest($request);
        $this->assertCount(1, SK_Model_Affiliate::findByRequest($request));

        $user = SKTest_TH::createUser();
        $this->_affiliate1->addUser($user);
        $this->assertCount(1, SK_Model_Affiliate::findByUserId($user->getId()));

        $transactionGroup = SKTest_TH::createPaymentTransactionGroup(SKTest_TH::createPaymentProvider(), 'foo', $user->getId());
        $this->_affiliate1->addTransactionGroup($transactionGroup);
        $this->assertCount(1, SK_Model_Affiliate::findByTransactionGroup($transactionGroup));

        $this->_affiliate1->delete();

        $this->assertCount(0, SK_Model_Affiliate::findByRequest($request));
        $this->assertCount(0, SK_Model_Affiliate::findByUserId($user->getId()));
        $this->assertCount(0, SK_Model_Affiliate::findByTransactionGroup($transactionGroup));
    }

    public function testDeleteUser() {
        $user = SKTest_TH::createUser();
        $this->_affiliate1->addUser($user);
        $user->delete();
        $this->assertContains($this->_affiliate1, SK_Model_Affiliate::findByUserId($user->getId()));
    }
}
