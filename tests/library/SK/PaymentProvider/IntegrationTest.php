<?php

class SK_PaymentProvider_IntegrationTest extends SKTest_TestCase {

    /** @var SK_ServiceBundle[] $_serviceBundles */
    private $_serviceBundles;

    public static function setUpBeforeClass() {
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::WEBBILLING);
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ZOMBAIO);
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ROCKETGATE);
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::WTS);
        SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::SEGPAY);
    }

    protected function setUp() {
        $this->_serviceBundles = array(SKTest_TH::createServiceBundle(35, 10), SKTest_TH::createServiceBundle(35, 10, 35, 20));
        /** @var SK_ServiceBundle $serviceBundle */
        foreach ($this->_serviceBundles as $serviceBundle) {
            $serviceBundle->getInitialServices()->add(new SK_Service_Premium(10));
            $serviceBundle->getRecurringServices()->add(new SK_Service_Premium(20));
        }
    }

    protected function tearDown() {
        SKTest_TH::clearEnv();
        foreach ($this->_serviceBundles as $serviceBundle) {
            SKTest_TH::deleteServiceBundle($serviceBundle);
        }
    }

    public function testCheckout() {
        /** @var SK_PaymentProvider_Abstract $paymentProvider */
        foreach (SK_PaymentProvider_Abstract::getAll() as $paymentProvider) {
            foreach ($this->_serviceBundles as $i => $serviceBundle) {
                $this->_setRandomProviderBundleId($paymentProvider, $serviceBundle);
                $this->_testBillingCycle($paymentProvider, $serviceBundle);
            }
        }
    }

    public function testDeletedProfile() {
        /** @var SK_PaymentProvider_Abstract $paymentProvider */
        foreach (SK_PaymentProvider_Abstract::getAll() as $paymentProvider) {
            foreach ($this->_serviceBundles as $serviceBundle) {
                $this->_setRandomProviderBundleId($paymentProvider, $serviceBundle);
                $user = SKTest_TH::createUser();
                $user->delete();

                // Initial billing
                $subscriptionKey = $this->_payInitial($user, $paymentProvider, $serviceBundle);
                $this->assertNotNull(SK_PaymentTransaction_Abstract::find($subscriptionKey, $paymentProvider), $paymentProvider->getId());

                if ($serviceBundle->getRecurringPeriod()) {
                    // Rebill
                    $key = $this->_payRebill($paymentProvider, $subscriptionKey, $serviceBundle->getRecurringPrice());
                    $this->assertNotNull(SK_PaymentTransaction_Abstract::find($key, $paymentProvider));
                }
            }
        }
    }

    /**
     * @param SK_PaymentProvider_Abstract $paymentProvider
     * @param SK_ServiceBundle            $serviceBundle
     */
    private function _setRandomProviderBundleId(SK_PaymentProvider_Abstract $paymentProvider, SK_ServiceBundle $serviceBundle) {
        switch ($paymentProvider->getName()) {
            case 'Webbilling':
                $providerBundleId = CM_Params::encode(array('group' => rand(1, 10000), 'package' => rand(1, 10000)), true);
                break;

            default:
                $providerBundleId = rand(1, 10000);
                break;
        }
        $paymentProvider->setProviderBundleId($serviceBundle, $providerBundleId);
    }

    /**
     * @param SK_PaymentProvider_Abstract $paymentProvider
     * @param SK_ServiceBundle            $serviceBundle
     */
    private function _testBillingCycle(SK_PaymentProvider_Abstract $paymentProvider, SK_ServiceBundle $serviceBundle) {
        $user = SKTest_TH::createUser();
        $this->assertFalse($user->getRoles()->contains(SK_Role::PREMIUMUSER), "User paying");

        // Initial billing
        $transactionKey = $this->_payInitial($user, $paymentProvider, $serviceBundle);
        $user->_change();

        $this->assertTrue((boolean) $user->getRoles()->getExpirationStamp(SK_Role::PREMIUMUSER),
            "Wrong limit providerId: " . $paymentProvider->getId() . " serviceBundleId: " . $serviceBundle->getId());
        /** @var SK_PaymentTransaction_ServiceBundle $transaction */
        $transaction = SK_PaymentTransaction_Abstract::find($transactionKey, $paymentProvider);
        $this->assertNotNull($transaction, "Transaction hasn't been created.");
        $this->assertEquals($serviceBundle->getId(), $transaction->getServiceBundle()->getId());
        $this->assertEquals($serviceBundle->getPrice(), $transaction->getAmount(),
            'providerId `' . $paymentProvider->getId() . '` serviceBundleId `' . $serviceBundle->getId() . '`');
        $this->assertEquals(0, SKTest_TH::timeDiffInDays($user->getRoles()->getStartStamp(SK_Role::PREMIUMUSER), time()), "Wrong start stamp");
        $this->assertEquals($serviceBundle->getPeriod(), SKTest_TH::timeDiffInDays($user->getRoles()->getStartStamp(SK_Role::PREMIUMUSER), $user->getRoles()->getExpirationStamp(SK_Role::PREMIUMUSER)), "Wrong membership duration");
        $this->assertTrue($user->getRoles()->contains(SK_Role::PREMIUMUSER), "User not paying");
        SKTest_TH::timeDaysForward($serviceBundle->getPeriod() / 2);
        $user->_change();
        $this->assertTrue($user->getRoles()->contains(SK_Role::PREMIUMUSER), "User not paying");
        SKTest_TH::timeDaysForward($serviceBundle->getPeriod() / 2 + 1);
        $user->_change();
        $this->assertFalse($user->getRoles()->contains(SK_Role::PREMIUMUSER), "User paying");

        $transactionGroup = $transaction->getGroup();
        $subscriptionKey = $transactionGroup->getKey();
        $this->assertNotNull($transactionGroup, "Transaction group hasn't been created.");
        $this->assertSame(1, $transactionGroup->getTransactions()->getCount());
        $this->assertNull($transactionGroup->getStopStamp());
        $this->assertEquals(CM_Site_Abstract::factory(), $transactionGroup->getSite());
        if ($serviceBundle->getRecurring()) {
            // Rebill
            $key = $this->_payRebill($paymentProvider, $subscriptionKey, $serviceBundle->getRecurringPrice());
            $user->_change();
            $this->assertTrue($user->getRoles()->contains(SK_Role::PREMIUMUSER), "User not paying");
            SKTest_TH::timeDaysForward($serviceBundle->getRecurringPeriod() / 2);
            $user->_change();
            $this->assertTrue($user->getRoles()->contains(SK_Role::PREMIUMUSER), "User not paying");
            SKTest_TH::timeDaysForward($serviceBundle->getRecurringPeriod() / 2 + 1);
            $user->_change();
            $this->assertFalse($user->getRoles()->contains(SK_Role::PREMIUMUSER), "User paying");

            // Rebill again
            $key = $this->_payRebill($paymentProvider, $subscriptionKey, $serviceBundle->getRecurringPrice());
            $user->_change();
            $this->assertTrue($user->getRoles()->contains(SK_Role::PREMIUMUSER), "User not paying");
            SKTest_TH::timeDaysForward($serviceBundle->getRecurringPeriod() / 2);
            $user->_change();
            $this->assertTrue($user->getRoles()->contains(SK_Role::PREMIUMUSER), "User not paying");

            // Rebill before expiration
            $key = $this->_payRebill($paymentProvider, $subscriptionKey, $serviceBundle->getRecurringPrice());
            $this->assertTrue($user->getRoles()->contains(SK_Role::PREMIUMUSER), "User not paying");
            SKTest_TH::timeDaysForward($serviceBundle->getRecurringPeriod());
            $user->_change();
            $this->assertTrue($user->getRoles()->contains(SK_Role::PREMIUMUSER), "User not paying");
            SKTest_TH::timeDaysForward($serviceBundle->getRecurringPeriod() / 2 + 1);
            $user->_change();
            $this->assertFalse($user->getRoles()->contains(SK_Role::PREMIUMUSER), "User paying");

            // Cancel
            // Zombaio don't support cancel
            if (!$paymentProvider instanceof SK_PaymentProvider_Zombaio) {
                $this->_payCancel($paymentProvider, $subscriptionKey);
                SKTest_TH::reinstantiateModel($transactionGroup);
                $this->assertEquals(time(), $transactionGroup->getCancelStamp(), null, 1);
            }

            // Segpay doesn't distinguish cancel and expire in their callback-notifications
            if (!$paymentProvider instanceof SK_PaymentProvider_Segpay) {
                $this->_payExpire($paymentProvider, $subscriptionKey);
                SKTest_TH::reinstantiateModel($transactionGroup);
                $this->assertEquals(time(), $transactionGroup->getStopStamp(), null, 1);
            }
        }

        SKTest_TH::timeReset();
    }

    /**
     * @param SK_User                     $user
     * @param SK_PaymentProvider_Abstract $paymentProvider
     * @param SK_ServiceBundle            $serviceBundle
     *
     * @return string
     */
    private function _payInitial(SK_User $user, SK_PaymentProvider_Abstract $paymentProvider, SK_ServiceBundle $serviceBundle) {
        $subscriptionKey = md5(rand());
        $transactionKey = md5(rand());
        $randomHash = md5(rand() . uniqid());
        $username = substr($randomHash, 0, 16);
        $password = substr($randomHash, 16);
        $providerBundleId = $paymentProvider->getProviderBundleId($serviceBundle);

        switch ($paymentProvider->getName()) {
            case 'CCBill':
                /** @var SK_PaymentProvider_CCBill $paymentProvider */
                $subscriptionKey = rand(1, 10000);
                $transactionKey = $subscriptionKey;

                $data = array('user' => $user->getId(), 'serviceBundle' => $serviceBundle->getId(), 'site' => $user->getSite()->getType());
                $body = http_build_query(array(
                    'accountingCurrency'         => 'USD',
                    'accountingCurrencyCode'     => '840',
                    'accountingInitialPrice'     => $serviceBundle->getPrice(),
                    'accountingRecurringPrice'   => ($serviceBundle->getRecurringPrice()) ? $serviceBundle->getRecurringPrice() : '0',
                    'address1'                   => 'foo',
                    'billedCurrency'             => 'USD',
                    'billedCurrencyCode'         => '840',
                    'billedInitialPrice'         => $serviceBundle->getPrice(),
                    'billedRecurringPrice'       => ($serviceBundle->getRecurringPrice()) ? $serviceBundle->getRecurringPrice() : '0',
                    'cardType'                   => 'VISA',
                    'city'                       => 'foo city',
                    'clientAccnum'               => $paymentProvider->getClientAccnum(),
                    'clientSubacc'               => $paymentProvider->getClientSubacc(),
                    'country'                    => 'US',
                    'email'                      => 'test@example.com',
                    'firstName'                  => 'bar',
                    'formName'                   => '144cc',
                    'initialPeriod'              => $serviceBundle->getPeriod(),
                    'ipAddress'                  => '174.251.160.130',
                    'lastName'                   => 'barbar',
                    'nextRenewalDate'            => '2014-06-18',
                    'password'                   => $password,
                    'paymentType'                => 'CREDIT',
                    'postalCode'                 => '12345',
                    'priceDescription'           => 'price desc',
                    'rebills'                    => '99',
                    'recurringPeriod'            => ($serviceBundle->getRecurringPeriod()) ? $serviceBundle->getRecurringPrice() : '0',
                    'referringUrl'               => 'https://www.example.com/account/premium',
                    'subscriptionCurrency'       => 'USD',
                    'subscriptionCurrencyCode'   => '840',
                    'subscriptionId'             => $subscriptionKey,
                    'subscriptionInitialPrice'   => $serviceBundle->getPrice(),
                    'subscriptionRecurringPrice' => ($serviceBundle->getRecurringPrice()) ? $serviceBundle->getRecurringPrice() : '0',
                    'timestamp'                  => '2013-06-18 06:09:28',
                    'transactionId'              => $transactionKey,
                    'username'                   => $username,
                    'X-data'                     => SK_PaymentProvider_Abstract::encodeData($data),
                ));

                $query = array(
                    'clientAccnum'   => $paymentProvider->getClientAccnum(),
                    'clientSubacc'   => $paymentProvider->getClientSubacc(),
                    'eventType'      => 'NewSaleSuccess',
                    'eventGroupType' => 'Subscription',
                );

                $uri = CM_Util::link('/payment-callback/ccbill/', $query);

                $request = new CM_Request_Post($uri, null, null, $body);
                $response = new SK_Response_Checkout_CCBill($request);
                $response->process();
                break;

            case 'Zombaio':
                /** @var SK_PaymentProvider_Zombaio $paymentProvider */
                $query = http_build_query(array(
                    'PRICING_ID'      => $providerBundleId,
                    'ZombaioGWPass'   => $paymentProvider->getZombaioPass(),
                    'Action'          => 'user.add',
                    'SUBSCRIPTION_ID' => $subscriptionKey,
                    'TRANSACTION_ID'  => $transactionKey,
                    'SITE_ID'         => $paymentProvider->getSiteId(),
                    'username'        => $username,
                    'password'        => $password,
                    'Amount'          => $serviceBundle->getPrice(),
                    'extra'           => $user->getId(),
                ));
                $uri = '/payment-callback/zombaio/?' . $query;
                $request = new CM_Request_Get($uri);
                $response = new SK_Response_Checkout_Zombaio($request);
                try {
                    $response->process();
                    $this->fail('The payment provider `Zombaio` should not be supported any more!');
                } catch (CM_Exception_NotImplemented $exception) {
                    $this->assertSame('The payment provider `Zombaio` is not supported any more!', $exception->getMessage());
                    $paymentProvider->onCreateTransactionGroupServiceBundleTransaction($user->getId(), $subscriptionKey, $transactionKey, $paymentProvider->getMerchantAccount(), $serviceBundle->getPrice(), $paymentProvider->getServiceBundle($providerBundleId), CM_Site_Abstract::factory(), time());
                }
                break;

            case 'Webbilling':
                /** @var SK_PaymentProvider_Webbilling $paymentProvider */
                $providerBundleParams = CM_Params::factory(CM_Params::decode($providerBundleId, true), false);
                $query = http_build_query(array(
                    'action'                  => 'new',
                    'processor_id'            => $user->getId(),
                    'selected_package'        => $providerBundleParams->getInt('package'),
                    'package_group'           => $providerBundleParams->getInt('group'),
                    'master_transactionid'    => $subscriptionKey,
                    'status'                  => '1',
                    'merchantid'              => $paymentProvider->getMerchantId(),
                    'amount'                  => $serviceBundle->getPrice(),
                    'recurring_transactionid' => $subscriptionKey,
                    'transactionid'           => $transactionKey,
                ));
                $uri = '/payment-callback/webbilling/?' . $query;
                $request = new CM_Request_Get($uri);
                $response = new SK_Response_Checkout_Webbilling($request);
                try {
                    $response->process();
                    $this->fail('The payment provider `Webbilling` should not be supported any more!');
                } catch (CM_Exception_NotImplemented $exception) {
                    $this->assertSame('The payment provider `Webbilling` is not supported any more!', $exception->getMessage());
                    $paymentProvider->onCreateTransactionGroupServiceBundleTransaction($user->getId(), $subscriptionKey, $transactionKey, $paymentProvider->getMerchantAccount(), $serviceBundle->getPrice(), $paymentProvider->getServiceBundle($providerBundleId), CM_Site_Abstract::factory(), time());
                }
                break;

            case 'RocketGate':
                /** @var SK_PaymentProvider_Rocketgate $paymentProvider */
                $content = '<HostedPage>
								<version>1.0</version>
								<transactID>' . $transactionKey . '</transactID>
								<transactionType>PURCHASE</transactionType>
								<invoiceID>' . $subscriptionKey . '</invoiceID>
								<customerID>' . $user->getId() . '</customerID>
								<customerFirstName>foo</customerFirstName>
								<customerLastName>bar</customerLastName>
								<customerAddress>address</customerAddress>
								<customerCity>city</customerCity>
								<customerState>state</customerState>
								<customerZip>zip</customerZip>
								<customerCountry>country</customerCountry>
								<requestedAmount>' . $serviceBundle->getPrice() . '</requestedAmount>
								<requestedCurrency>USD</requestedCurrency>
								<settledAmount>' . $serviceBundle->getPrice() . '</settledAmount>
								<settledCurrency>USD</settledCurrency>
								<udf02>' .
                    SK_PaymentProvider_Abstract::encodeData(array(
                        'user'          => $user->getId(),
                        'serviceBundle' => $serviceBundle->getId(),
                        'site'          => CM_Site_Abstract::factory(),
                    )) . '</udf02>
								<merchantID>' . $paymentProvider->getMerchantId() . '</merchantID>
								<merchantAccount>555</merchantAccount>
								<merchantProductID>' . $serviceBundle->getId() . '</merchantProductID>
								<merchantSiteID>1</merchantSiteID>
								<cardType>MC</cardType>
								<cardHash>kjasdhf98sdfuibh87odsaf</cardHash>
								<cardLastFour>1234</cardLastFour>
								<cardExpiration>0984</cardExpiration>
								<authNo>123456</authNo>
								<avsResponse>Y</avsResponse>
								<cvv2Code>123</cvv2Code>
								<scrubResults>foobar123</scrubResults>
							</HostedPage>';
                $uri = '/payment-callback/rocketgate/';
                $request = new CM_Request_Post($uri, null, null, $content);
                $response = new SK_Response_Checkout_Rocketgate($request);
                $response->process();
                break;

            case 'Wts':
                $subscriptionKey = rand(1, 10000);
                $transactionKey = rand(1, 10000);

                $postVars = http_build_query(array(
                    'parent_id'         => 'WTS01',
                    'sub_id'            => 'CARGO1',
                    'pmt_type'          => 'chk',
                    'chk_acct'          => '847412584',
                    'chk_aba'           => '999999999',
                    'custname'          => 'fooName',
                    'custemail'         => 'fooEmail',
                    'custaddress1'      => 'fooAddress',
                    'custcity'          => 'fooCity',
                    'custstate'         => 'fooState',
                    'custzip'           => 'fooZip',
                    'initial_amount'    => $serviceBundle->getPrice(),
                    'recur_amount'      => null !== $serviceBundle->getRecurringPrice() ? $serviceBundle->getRecurringPrice() : 0,
                    'billing_cycle'     => SKService_Wts_Client_DynamicBilling::CYCLE_MONTHLY,
                    'response_location' => 'www.foo.com',
                    'toastcode'         => '1234567890',
                    'data'              => SK_PaymentProvider_Abstract::encodeData(array(
                            'user'          => $user->getId(),
                            'serviceBundle' => $serviceBundle->getId(),
                            'site'          => CM_Site_Abstract::factory(),
                        )),
                ));

                $body = http_build_query(array(
                    'history_id'      => $transactionKey,
                    'authcode'        => 'fooAuthcode',
                    'status'          => 'Accepted',
                    'order_id'        => $subscriptionKey,
                    'consumername'    => 'fooName',
                    'state'           => 'fooState',
                    'email'           => 'fooEmail',
                    'zip'             => 'fooZip',
                    'city'            => 'fooCity',
                    'address1'        => 'fooAddress',
                    'consumer_unique' => 'fooUnique',
                    'PostedVars'      => 'query_string=' . $postVars,
                ));

                $request = new CM_Request_Post('/payment-callback/wts', null, null, $body);

                $response = new SK_Response_Checkout_Wts($request);
                $response->process();
                break;

            case 'SegPay':
                $subscriptionKey = rand(1, 10000);
                $transactionKey = rand(1, 10000);

                $data = array('user' => $user->getId(), 'serviceBundle' => $serviceBundle->getId(), 'site' => $user->getSite()->getType());
                $uri = CM_Util::link('/payment-callback/segpay-transaction', array(
                    'eticketid'  => $paymentProvider->getProviderBundleId($serviceBundle),
                    'action'     => 'auth',
                    'stage'      => 'initial',
                    'approved'   => 'yes',
                    'trantype'   => 'sale',
                    'purchaseid' => $subscriptionKey,
                    'tranid'     => $transactionKey,
                    'price'      => $serviceBundle->getPrice(),
                    'data'       => SK_PaymentProvider_Abstract::encodeData($data),
                    'username'   => $username,
                    'password'   => $password,
                    'billemail'  => 'test@example.com',
                ));
                $request = new CM_Request_Post($uri);
                $response = new SK_Response_Checkout_SegpayTransaction($request);
                $response->process();
                break;
        }
        return (string) $transactionKey;
    }

    /**
     * @param SK_PaymentProvider_Abstract $paymentProvider
     * @param string                      $subscriptionKey
     * @param float                       $amount
     * @return string
     */
    private function _payRebill(SK_PaymentProvider_Abstract $paymentProvider, $subscriptionKey, $amount) {
        $transactionKey = md5(rand());
        switch ($paymentProvider->getName()) {
            case 'CCBill':
                /** @var SK_PaymentProvider_CCBill $paymentProvider */
                $body = http_build_query(array(
                    'accountingAmount'       => $amount,
                    'accountingCurrency'     => 'USD',
                    'accountingCurrencyCode' => '840',
                    'billedAmount'           => $amount,
                    'billedCurrency'         => 'USD',
                    'billedCurrencyCode'     => '840',
                    'clientAccnum'           => $paymentProvider->getClientAccnum(),
                    'clientSubacc'           => $paymentProvider->getClientSubacc(),
                    'nextRenewalDate'        => '2013-07-12',
                    'subscriptionId'         => $subscriptionKey,
                    'timestamp'              => '2013-06-18 07:41:12',
                    'transactionId'          => $transactionKey,
                ));

                $query = array(
                    'clientAccnum'   => $paymentProvider->getClientAccnum(),
                    'clientSubacc'   => $paymentProvider->getClientSubacc(),
                    'eventType'      => 'RenewalSuccess',
                    'eventGroupType' => 'Subscription',
                );

                $uri = CM_Util::link('/payment-callback/ccbill/', $query);

                $request = new CM_Request_Post($uri, null, null, $body);
                $response = new SK_Response_Checkout_CCBill($request);
                $response->process();
                break;

            case 'Zombaio':
                /** @var SK_PaymentProvider_Zombaio $paymentProvider */
                $query = http_build_query(array('ZombaioGWPass'   => $paymentProvider->getZombaioPass(), 'Action' => 'rebill',
                                                'SUBSCRIPTION_ID' => $subscriptionKey, 'TRANSACTION_ID' => $transactionKey,
                                                'SiteID'          => $paymentProvider->getSiteId(),
                                                'Success'         => 1, 'Amount' => $amount));
                $uri = '/payment-callback/zombaio/?' . $query;
                $request = new CM_Request_Get($uri);
                $response = new SK_Response_Checkout_Zombaio($request);
                $response->process();
                break;

            case 'Webbilling':
                /** @var SK_PaymentProvider_Webbilling $paymentProvider */
                $query = http_build_query(array('action'        => 'rebill', 'status' => '1', 'merchantid' => $paymentProvider->getMerchantId(),
                                                'amount'        => $amount, 'recurring_transactionid' => $subscriptionKey,
                                                'transactionid' => $transactionKey));
                $uri = '/payment-callback/webbilling/?' . $query;
                $request = new CM_Request_Get($uri);
                $response = new SK_Response_Checkout_Webbilling($request);
                $response->process();
                break;

            case 'RocketGate':
                /** @var SK_PaymentProvider_Rocketgate $paymentProvider */
                $content = '<RecurringBilling>
								<version>1.0</version>
								<transactID>' . $transactionKey . '</transactID>
								<transactionDate>1984-09-06</transactionDate>
								<transactionTimestamp>1984-09-06 09:12:00</transactionTimestamp>
								<customerID>222</customerID>
								<invoiceID>' . $subscriptionKey . '</invoiceID>
								<requestedAmount>' . $amount . '</requestedAmount>
								<requestedCurrency>USD</requestedCurrency>
								<approvedAmount>' . $amount . '</approvedAmount>
								<approvedCurrency>USD</approvedCurrency>
								<settledAmount>' . $amount . '</settledAmount>
								<settledCurrency>USD</settledCurrency>
								<merchantID>' . $paymentProvider->getMerchantId() . '</merchantID>
								<merchantAccount>555</merchantAccount>
								<merchantProductID>666</merchantProductID>
								<billingType>R</billingType>
								<username>foo</username>
								<cardHash>sdhkafz9a8dghad</cardHash>
								<cardType>MC</cardType>
								<cardLastFour>1234</cardLastFour>
								<authNo>777</authNo>
								<avsResponse>Y</avsResponse>
							</RecurringBilling>';
                $uri = '/payment-callback/rocketgate/';
                $request = new CM_Request_Post($uri, null, null, $content);
                $response = new SK_Response_Checkout_Rocketgate($request);
                $response->process();
                break;

            case 'Wts':
                $transactionKey = rand(1, 10000);
                $sftpClient = $this->getMock('SKService_Wts_Client_Sftp', array('_getFileContent', 'getFilenameList', 'delete'), array('foo', 'bar',
                    'foobar'));
                $filename = 'CARGO-trans-WTS-123.txt';
                $contentsTrans =
                    '"SubID","Transaction Date","Amount","Consumer Name","Account Name","Transaction Type","Transaction Result","Authorization Code","Routing Number","Account Number","Account Type Description","Credit Card Number","Credit Card Expiration Date","Recurring Description","Company Name","Billing Address","Billing Address2","Billing City","Billing State","Billing Zip","Billing Country","Shipping Address","Shipping Address2","Shipping City","Shipping State","Shipping Zip","Shipping Country","Phone Number","E-Mail Address","IP Address","Server Referrer","MerchantOrderNumber","Order Number","History KeyID","Reference KeyID","Profile KeyID","Reseller Code","Partner Code","Username","ConsumerUniqueID"' .
                    "\n" .
                    '"","","' . $amount .
                    '","","","Check Settlement","Approved","","","","","","","Recurring","","","","","","","","","","","","","","","","","","","' .
                    $subscriptionKey . '","' . $transactionKey . '","","","","","",""' .
                    "\n";

                $sftpClient->expects($this->any())->method('getFilenameList')->will($this->returnValue(array($filename)));
                $sftpClient->expects($this->any())->method('_getFileContent')->will($this->returnValue($contentsTrans));
                $sftpClient->expects($this->any())->method('delete')->with($filename);

                $paymentProvider = new SK_PaymentProvider_Wts($sftpClient);
                $paymentProvider->cronCheckout(true);
                break;

            case 'SegPay':
                $transactionKey = rand(1, 10000);
                $uri = CM_Util::link('/payment-callback/segpay-transaction', array(
                    'action'     => 'auth',
                    'stage'      => 'conversion',
                    'approved'   => 'yes',
                    'trantype'   => 'sale',
                    'purchaseid' => $subscriptionKey,
                    'tranid'     => $transactionKey,
                    'price'      => $amount,
                ));
                $request = new CM_Request_Post($uri);
                $response = new SK_Response_Checkout_SegpayTransaction($request);
                $response->process();
                break;
        }
        return $transactionKey;
    }

    /**
     * @param SK_PaymentProvider_Abstract $paymentProvider
     * @param string                      $subscriptionKey
     */
    private function _payCancel(SK_PaymentProvider_Abstract $paymentProvider, $subscriptionKey) {
        switch ($paymentProvider->getName()) {
            case 'CCBill':
                /** @var SK_PaymentProvider_CCBill $paymentProvider */
                $body = http_build_query(array(
                    'clientAccnum'   => $paymentProvider->getClientAccnum(),
                    'clientSubacc'   => $paymentProvider->getClientSubacc(),
                    'reason'         => 'Failed rebill',
                    'source'         => 'failedRB',
                    'subscriptionId' => $subscriptionKey,
                    'timestamp'      => '2013-06-18 07:41:12',
                ));

                $query = array(
                    'clientAccnum'   => $paymentProvider->getClientAccnum(),
                    'clientSubacc'   => $paymentProvider->getClientSubacc(),
                    'eventType'      => 'Cancellation',
                    'eventGroupType' => 'Subscription',
                );

                $uri = CM_Util::link('/payment/ccbill/', $query);

                $request = new CM_Request_Post($uri, null, null, $body);
                $response = new SK_Response_Checkout_CCBill($request);
                $response->process();
                break;

            case 'Zombaio':
                // Zombaio doesn't support cancel
                break;

            case 'Webbilling':
                /** @var SK_PaymentProvider_Webbilling $paymentProvider */
                $query = http_build_query(array('action' => 'cancel', 'recurring_transactionid' => $subscriptionKey));
                $uri = '/payment/webbilling/?' . $query;
                $request = new CM_Request_Get($uri);
                $response = new SK_Response_Checkout_Webbilling($request);
                $response->process();
                break;

            case 'RocketGate':
                $content = '<Cancellation>
								<version>1.0</version>
								<customerID>111</customerID>
								<invoiceID>' . $subscriptionKey . '</invoiceID>
								<cancellationDate>1984-09-06</cancellationDate>
								<reason>PENDING-CANCELLATION</reason>
								<reasonCode>333</reasonCode>
								<attempts>444</attempts>
								<merchantID>555</merchantID>
							</Cancellation>';
                $uri = '/payment-callback/rocketgate/';
                $request = new CM_Request_Post($uri, null, null, $content);
                $response = new SK_Response_Checkout_Rocketgate($request);
                $response->process();
                break;

            case 'Wts':
                $sftpClient = $this->getMock('SKService_Wts_Client_Sftp', array('_getFileContent', 'getFilenameList', 'delete'), array('foo', 'bar',
                    'foobar'));
                $filename = 'CARGO-cancel-WTS-123.txt';
                $contentsTrans =
                    '"SubID","Password Expiration Date","Order Number","Username"' .
                    "\n" .
                    '"CARGO1","","' . $subscriptionKey . '",""' .
                    "\n";

                $sftpClient->expects($this->any())->method('getFilenameList')->will($this->returnValue(array($filename)));
                $sftpClient->expects($this->any())->method('_getFileContent')->will($this->returnValue($contentsTrans));
                $sftpClient->expects($this->any())->method('delete')->will($this->returnValue(null));

                $paymentProvider = new SK_PaymentProvider_Wts($sftpClient);
                $paymentProvider->cronCheckout(true);
                break;

            case 'SegPay':
                $uri = CM_Util::link('/payment-callback/segpay-subscription/disable', array('purchaseid' => $subscriptionKey));
                $request = new CM_Request_Get($uri);
                $response = new SK_Response_Checkout_SegpaySubscription($request);
                $response->process();
                break;
        }
    }

    /**
     * @param SK_PaymentProvider_Abstract $paymentProvider
     * @param string                      $subscriptionKey
     */
    private function _payExpire(SK_PaymentProvider_Abstract $paymentProvider, $subscriptionKey) {
        switch ($paymentProvider->getName()) {
            case 'CCBill':
                /** @var SK_PaymentProvider_CCBill $paymentProvider */
                $body = http_build_query(array(
                    'clientAccnum'   => $paymentProvider->getClientAccnum(),
                    'clientSubacc'   => $paymentProvider->getClientSubacc(),
                    'subscriptionId' => $subscriptionKey,
                    'timestamp'      => '2013-06-18 07:41:12',
                ));

                $query = array(
                    'clientAccnum'   => $paymentProvider->getClientAccnum(),
                    'clientSubacc'   => $paymentProvider->getClientSubacc(),
                    'eventType'      => 'Expiration',
                    'eventGroupType' => 'Subscription',
                );

                $uri = CM_Util::link('/payment-callback/ccbill/', $query);

                $request = new CM_Request_Post($uri, null, null, $body);
                $response = new SK_Response_Checkout_CCBill($request);
                $response->process();
                break;

            case 'Zombaio':
                /** @var SK_PaymentProvider_Zombaio $paymentProvider */
                $query = http_build_query(array('Action'         => 'user.delete', 'ZombaioGWPass' => $paymentProvider->getZombaioPass(),
                                                'SubscriptionID' => $subscriptionKey, 'SiteID' => $paymentProvider->getSiteId()));
                $uri = '/payment-callback/zombaio/?' . $query;
                $request = new CM_Request_Get($uri);
                $response = new SK_Response_Checkout_Zombaio($request);
                $response->process();
                break;

            case 'Webbilling':
                /** @var SK_PaymentProvider_Webbilling $paymentProvider */
                $query = http_build_query(array('action' => 'expire', 'recurring_transactionid' => $subscriptionKey));
                $uri = '/payment-callback/webbilling/?' . $query;
                $request = new CM_Request_Get($uri);
                $response = new SK_Response_Checkout_Webbilling($request);
                $response->process();
                break;

            case 'RocketGate':
                $content = '<Cancellation>
								<version>1.0</version>
								<customerID>111</customerID>
								<invoiceID>' . $subscriptionKey . '</invoiceID>
								<cancellationDate>1984-09-06</cancellationDate>
								<reason>USER</reason>
								<reasonCode>333</reasonCode>
								<attempts>444</attempts>
								<merchantID>555</merchantID>
							</Cancellation>';
                $uri = '/payment-callback/rocketgate/';
                $request = new CM_Request_Post($uri, null, null, $content);
                $response = new SK_Response_Checkout_Rocketgate($request);
                $response->process();
                break;

            case 'Wts':
                $body = http_build_query(array(
                    'reseller_code' => '111',
                    'result'        => 'fooResult',
                    'signupdate'    => '10/18/2001',
                    'acctname'      => 'Max Mustermann',
                    'transdate'     => '10/18/2001',
                    'resellercode'  => '222',
                    'state'         => 'fooState',
                    'historyid'     => '333',
                    'ordernumber'   => $subscriptionKey,
                    'address1'      => 'fooAddress',
                    'address2'      => 'fooAddress2',
                    'consumer_code' => '444',
                    'ipaddress'     => '111.222.33.44',
                    'password'      => 'xxxx',
                    'city'          => 'fooCity',
                    'currency'      => 'fooCurrency',
                    'amount'        => '20',
                    'username'      => 'fooUsername',
                    'sys_pass'      => '12345',
                    'zip1'          => 'fooZip',
                    'action'        => 'delete',
                    'siteid'        => '555',
                    'custname'      => 'Max Mustermann',
                    'site'          => '666',
                    'retrystat'     => '666',
                    'memtype'       => '19685',
                    'prenote'       => 'fooPrenote',
                    'prog_id'       => 'fooProgId',
                    'orderinfo'     => 'fooOrderInfo',
                    'pmt_type'      => 'chk',
                    'cs'            => 'DailyBatch',
                    'country'       => 'fooCountry',
                    'referrer'      => 'http://www.foo.com',
                    'partner_code'  => 'fooPartnerCode',
                    'email'         => 'mail@foo.com',
                    'orderid'       => $subscriptionKey,
                    'authno'        => 'fooAuthno',

                ));

                $request = new CM_Request_Post('/payment-callback/wts', null, null, $body);

                $response = new SK_Response_Checkout_Wts($request);
                $response->process();
                break;
        }
    }
}
