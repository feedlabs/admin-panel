<?php

class SKTest_TH extends CMTest_TH {

    /**
     * @param SK_User|null $user
     * @param string|null  $name
     * @return SK_Entity_Pinboard
     */
    public static function createPinboard(SK_User $user = null, $name = null) {
        if (!$user) {
            $user = self::createUser();
        }
        $name = is_null($name) ? self::randStr(10) : (string) $name;
        return SK_Entity_Pinboard::create($name, $user);
    }

    /**
     * @param SK_Entertainment_Schedule_Abstract|null $schedule
     * @param int|null                                $actionType
     * @param int|null                                $offset
     * @param int|null                                $sourceUserTemplate
     * @return SK_Entertainment_ScheduleItem
     */
    public static function createEntertainmentScheduleItem(SK_Entertainment_Schedule_Abstract $schedule = null, $actionType = null, $offset = null, $sourceUserTemplate = null) {
        if (is_null($schedule)) {
            $schedule = SK_Entertainment_Schedule_Offline::createStatic(array('description' => null));
        }
        $actionType = is_null($actionType) ? rand(1, 1000) : (int) $actionType;
        $offset = is_null($offset) ? rand(1, 1000) : (int) $offset;
        $sourceUserTemplate = is_null($sourceUserTemplate) ? null : (int) $sourceUserTemplate;
        return SK_Entertainment_ScheduleItem::createStatic(array(
            'schedule'           => $schedule,
            'actionType'         => $actionType,
            'offset'             => $offset,
            'sourceUserTemplate' => $sourceUserTemplate,
        ));
    }

    /**
     * @param SK_Entertainment_MessageSet $set
     * @param string|null                 $body
     * @return SK_Entertainment_Message
     */
    public static function createEntertainmentMessage(SK_Entertainment_MessageSet $set = null, $body = null) {
        if (is_null($set)) {
            $set = $messageSet = SK_Entertainment_MessageSet::createStatic(array('description' => null));
        }
        $body = is_null($body) ? self::randStr(30) : (string) $body;
        return SK_Entertainment_Message::createStatic(array('set' => $set, 'body' => $body));
    }

    /**
     * @param boolean|null $reviewed
     * @return SK_Entertainment_UserTemplate
     */
    public static function createEntertainmentUserTemplate($reviewed = null) {
        $reviewed = null !== $reviewed ? (boolean) $reviewed : false;
        $user = SKTest_TH::createUser();
        $user->setLanguage(SKTest_TH::createLanguage());
        $photo = SKTest_TH::createPhoto($user);
        $userTemplate = SK_Entertainment_UserTemplate::create($user);
        $userTemplate->setReviewed($reviewed);
        SK_Entertainment_Photo::create($userTemplate, $photo);
        return $userTemplate;
    }

    /**
     * @return CM_Model_Location
     */
    public static function createLocationCountry() {
        return CM_Model_Location::createCountry('United States', 'US');
    }

    /**
     * @return CM_Model_Location
     */
    public static function createLocationState() {
        $country = self::createLocationCountry();
        return CM_Model_Location::createState($country, 'New York');
    }

    /**
     * @return CM_Model_Location
     */
    public static function createLocationCity() {
        $state = self::createLocationState();
        $city = CM_Model_Location::createCity($state, 'New York', 40.7647, -73.979);
        CM_Model_Location::createAggregation();
        return $city;
    }

    /**
     * @param SK_User|null $user
     * @return SK_Entity_Photo
     */
    public static function createPhoto(SK_User $user = null) {
        if (!$user) {
            $user = self::createUser();
        }
        $image = new CM_File_Image(DIR_TEST_DATA . 'img/test.jpg');
        $photo = SK_Entity_Photo::create($image, $user);
        return $photo;
    }

    /**
     * @param int|null              $sex
     * @param string|null           $username
     * @param CM_Site_Abstract|null $site
     * @return SK_User
     */
    public static function createUser($sex = null, $username = null, $site = null) {
        if (is_null($sex)) {
            $sex = SK_User::SEX_MALE;
        }
        if (is_null($username)) {
            while (empty($username) || SK_User::findUsername($username)) {
                $username .= self::randStr(2);
            }
        }
        $fields = array();
        $fields['site'] = $site;
        $fields['sex'] = (int) $sex;
        $fields['location'] = self::createLocationCity();
        $dateNow = new DateTime('now');
        list($y, $m, $d) = explode('-', $dateNow->format('Y-m-d'));
        $fields['birthdate'] = new DateTime($y - rand(18, 99) . '-' . $m . '-' . $d);
        $fields['password'] = md5(rand() . uniqid());
        $fields['username'] = (string) $username;
        $fields['email'] = $fields['username'] . '@example.com';
        return SK_User::createStatic($fields);
    }

    /**
     * @return SK_User
     */
    public static function createUserPremium() {
        $user = self::createUser();
        $user->getRoles()->add(SK_Role::PREMIUMUSER, 1000 * 86400);
        return $user;
    }

    /**
     * @return SK_User
     */
    public static function createUserAdmin() {
        $user = self::createUser();
        $user->getRoles()->add(SK_Role::ADMIN);
        return $user;
    }

    /**
     * @param SK_User   $user
     * @param bool|null $https
     * @return SK_Entity_Video
     */
    public static function createVideo(SK_User $user = null, $https = null) {
        if (!$user) {
            $user = self::createUser();
        }
        /** @var SK_Entity_Video $video */
        $video = SK_Entity_Video::create($user, 'Duck song', SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        $scene = self::createVideoScene($video, 0);
        SKTest_TH::reinstantiateModel($video);
        self::createVideoSource($scene, null, $https);
        return $video;
    }

    /**
     * @return SK_Entity_Video
     */
    public static function createVideoPremium() {
        $user = self::createUser();
        /** @var SK_Entity_Video $video */
        $video = SK_Entity_Video::create($user, 'Premium Video', SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        $scene = self::createVideoScene($video);
        SKTest_TH::reinstantiateModel($video);
        self::createVideoSource($scene, SK_Model_Video_Source::TYPE_ADULTCENTRO);
        return $video;
    }

    /**
     * @param SK_Entity_Video $video
     * @param int|null        $duration
     * @return SK_Model_Video_Scene
     */
    public static function createVideoScene(SK_Entity_Video $video = null, $duration = null) {
        if (!$video) {
            $video = self::createVideo();
        }
        if (null === $duration) {
            $duration = rand(1, 1000);
        }
        $duration = (int) $duration;
        return SK_Model_Video_Scene::createStatic(array('video' => $video, 'duration' => $duration));
    }

    /**
     * @param SK_Model_Video_Scene|null $scene
     * @param int|null                  $type
     * @param bool|null                 $https
     * @return SK_Model_Video_Source
     */
    public static function createVideoSource(SK_Model_Video_Scene $scene = null, $type = null, $https = null) {
        if (!$scene) {
            $scene = self::createVideoScene();
        }
        if (null === $type) {
            $type = SK_Model_Video_Source::TYPE_IFRAME;
        }
        $https = (bool) $https;
        $protocol = $https ? 'https' : 'http';
        return SK_Model_Video_Source::createStatic(array(
            'videoScene' => $scene,
            'isPreview'  => false,
            'src'        => $protocol . '://www.youtube.com/embed/MtN1YnoL46Q',
            'ratio'      => 0.61,
            'type'       => $type,
        ));
    }

    /**
     * @param SK_Model_Video_Scene $scene
     */
    public static function deleteVideoScene(SK_Model_Video_Scene $scene) {
        CM_Db_Db::delete('sk_videoScene', array('id' => $scene->getId()));
        SKTest_TH::clearCache();
    }

    /**
     * @param SK_User $user
     * @return SK_Entity_Blogpost
     */
    public static function createBlogpost(SK_User $user = null) {
        if (!$user) {
            $user = self::createUser();
        }
        return SK_Entity_Blogpost::create('TestPost', 'TestText', $user, SK_ModelAsset_Entity_PrivacyAbstract::NONE);
    }

    /**
     * @param SK_User|null $user
     * @param string|null  $performerName
     * @param int|null     $privacy
     * @return SK_Entity_CamShow
     */
    public static function createCamShow(SK_User $user = null, $performerName = null, $privacy = null) {
        if (null === $user) {
            $user = self::createUser();
        }
        if (null === $performerName) {
            $performerName = self::randStr(5);
        }
        if (null === $privacy) {
            $privacy = SK_ModelAsset_Entity_PrivacyAbstract::NONE;
        }
        return SK_Entity_CamShow::create($user, $performerName, $privacy);
    }

    /**
     * @param SK_User $sender    OPTIONAL
     * @param SK_User $recipient OPTIONAL
     * @return SK_Entity_Conversation
     */
    public static function createConversation(SK_User $sender = null, SK_User $recipient = null) {
        if (!$sender) {
            $sender = self::createUser();
        }
        if (!$recipient) {
            $recipient = self::createUser();
        }
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $sender, 'recipients' => array($recipient)));
        SK_Entity_ConversationMessage_Text::createStatic(array(
            'conversation' => $conversation,
            'user'         => $sender,
            'text'         => 'some random text blah blah blah!',
        ));
        return $conversation;
    }

    /**
     * @param SK_User|null $user
     * @param SK_User|null $admin
     * @param int|null     $amount
     * @return SK_CoinTransaction_AdminGive
     */
    public static function createCoinTransactionAdminGive(SK_User $user = null, SK_User $admin = null, $amount = null) {
        if (is_null($user)) {
            $user = SKTest_TH::createUser();
        }
        if (is_null($admin)) {
            $admin = SKTest_TH::createUser();
        }
        if (is_null($amount)) {
            $amount = 1;
        }
        $amount = (int) $amount;
        return SK_CoinTransaction_AdminGive::createStatic(array('user' => $user, 'amount' => $amount, 'admin' => $admin));
    }

    /**
     * @param int|null $id
     * @return SK_PaymentProvider_Abstract
     */
    public static function createPaymentProvider($id = null) {
        switch ($id) {
            case SK_PaymentProvider_Abstract::WEBBILLING:
                CM_Db_Db::insert('sk_paymentProvider', array('id' => SK_PaymentProvider_Abstract::WEBBILLING, 'name' => 'Webbilling'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::WEBBILLING, 'name' => 'merchantid', 'value' => 'foo'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::WEBBILLING, 'name' => 'merchantpass', 'value' => 'bar'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::WEBBILLING, 'name' => 'salt', 'value' => 'fooBar'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::WEBBILLING, 'name' => 'statement', 'value' => 'Webbilling'));
                break;

            case SK_PaymentProvider_Abstract::ZOMBAIO:
                CM_Db_Db::insert('sk_paymentProvider', array('id' => SK_PaymentProvider_Abstract::ZOMBAIO, 'name' => 'Zombaio'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::ZOMBAIO, 'name' => 'site_id', 'value' => rand(1, 10000)));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::ZOMBAIO, 'name' => 'zombaio_pass', 'value' => 'bar'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::ZOMBAIO, 'name' => 'merchant_id', 'value' => rand(1, 10000)));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::ZOMBAIO, 'name' => 'statement', 'value' => 'Zombaio'));
                break;

            case SK_PaymentProvider_Abstract::ROCKETGATE:
                CM_Db_Db::insert('sk_paymentProvider', array('id' => SK_PaymentProvider_Abstract::ROCKETGATE, 'name' => 'RocketGate'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::ROCKETGATE, 'name' => 'merchantId', 'value' => rand(1, 10000)));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::ROCKETGATE, 'name' => 'merchantPassword', 'value' => 'foo'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::ROCKETGATE, 'name' => 'hashSecret', 'value' => 'bar'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::ROCKETGATE, 'name' => 'statement', 'value' => 'RocketGate'));
                break;

            case SK_PaymentProvider_Abstract::WTS:
                CM_Db_Db::insert('sk_paymentProvider', array(
                    'id'                 => SK_PaymentProvider_Abstract::WTS,
                    'name'               => 'Wts',
                    'request_time_frame' => 7200,
                    'last_request_time'  => time() - 7200,
                ));

                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::WTS, 'name' => 'parentId', 'value' => 'foo'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::WTS, 'name' => 'subId', 'value' => 'bar'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::WTS, 'name' => 'sftpHostname', 'value' => 'bar'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::WTS, 'name' => 'sftpUsername', 'value' => 'bar'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::WTS, 'name' => 'sftpPassword', 'value' => 'bar'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::WTS, 'name' => 'statement', 'value' => 'WTS'));

                break;

            case SK_PaymentProvider_Abstract::CCBILL:
            default:
                CM_Db_Db::insert('sk_paymentProvider', array('id' => SK_PaymentProvider_Abstract::CCBILL, 'name' => 'CCBill'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::CCBILL, 'name' => 'clientAccnum', 'value' => rand(1, 10000)));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::CCBILL, 'name' => 'clientPassword', 'value' => 'foo'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::CCBILL, 'name' => 'clientUsername', 'value' => 'bar'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::CCBILL, 'name' => 'clientSubacc', 'value' => 0));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::CCBILL, 'name' => 'clientSubaccSingleBillings', 'value' => 1));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::CCBILL, 'name' => 'salt', 'value' => 'foobar'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::CCBILL, 'name' => 'statement', 'value' => 'CCBill'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::CCBILL, 'name' => 'formNameCreditCard', 'value' => 'foo'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::CCBILL, 'name' => 'formNameCheck', 'value' => 'bar'));
                $id = SK_PaymentProvider_Abstract::CCBILL;
                break;
            case SK_PaymentProvider_Abstract::SEGPAY;
                CM_Db_Db::insert('sk_paymentProvider', array('id' => SK_PaymentProvider_Abstract::SEGPAY, 'name' => 'SegPay'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::SEGPAY, 'name' => 'statement', 'value' => 'Segpay'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::SEGPAY, 'name' => 'backendId', 'value' => 'foo'));
                CM_Db_Db::insert('sk_paymentProvider_field',
                    array('paymentProviderId' => SK_PaymentProvider_Abstract::SEGPAY, 'name' => 'backendAccessKey', 'value' => 'bar'));
                break;
        }
        return SK_PaymentProvider_Abstract::factory($id);
    }

    /**
     * @param int                                           $userId
     * @param SK_PaymentProvider_Abstract|null              $paymentProvider
     * @param string|null                                   $transactionKey
     * @param float|null                                    $amount
     * @param SK_ServiceBundle|null                         $serviceBundle
     * @param SK_PaymentTransactionGroup|null               $group
     * @param SK_Model_PaymentProvider_MerchantAccount|null $merchantAccount
     * @return SK_PaymentTransaction_ServiceBundle
     */
    public static function createPaymentTransaction($userId, SK_PaymentProvider_Abstract $paymentProvider = null, $transactionKey = null, $amount = null, SK_ServiceBundle $serviceBundle = null, SK_PaymentTransactionGroup $group = null, SK_Model_PaymentProvider_MerchantAccount $merchantAccount = null) {
        if (is_null($paymentProvider)) {
            $paymentProvider = SKTest_TH::createPaymentProvider();
        }
        if (is_null($transactionKey)) {
            $transactionKey = md5(rand());
        }
        if (is_null($amount)) {
            $amount = rand();
        }
        if (is_null($serviceBundle)) {
            $serviceBundle = SKTest_TH::createServiceBundle();
        }
        if (is_null($merchantAccount)) {
            $merchantAccount = SKTest_TH::createMerchantAccount($paymentProvider);
        }
        return SK_PaymentTransaction_ServiceBundle::createStatic(array(
            'userId'          => $userId,
            'paymentProvider' => $paymentProvider,
            'key'             => $transactionKey,
            'merchantAccount' => $merchantAccount,
            'amount'          => $amount,
            'group'           => $group,
            'data'            => array('serviceBundleId' => $serviceBundle->getId()),
        ));
    }

    /**
     * @param SK_PaymentProvider_Abstract $paymentProvider
     * @param string                      $subscriptionKey
     * @param int                         $userId
     * @param CM_Site_Abstract|null       $site
     * @return SK_PaymentTransactionGroup
     */
    public static function createPaymentTransactionGroup(SK_PaymentProvider_Abstract $paymentProvider, $subscriptionKey, $userId, CM_Site_Abstract $site = null) {
        if (null === $site) {
            $site = CM_Site_Abstract::factory();
        }
        return SK_PaymentTransactionGroup::createStatic(array(
            'userId'          => $userId,
            'paymentProvider' => $paymentProvider,
            'subscriptionKey' => $subscriptionKey,
            'site'            => $site,
        ));
    }

    /**
     * @param SK_PaymentProvider_Abstract|null $paymentProvider
     * @param string|null                      $account
     * @return SK_Model_PaymentProvider_MerchantAccount
     */
    public static function createMerchantAccount(SK_PaymentProvider_Abstract $paymentProvider = null, $account = null) {
        if (is_null($paymentProvider)) {
            $paymentProvider = SKTest_TH::createPaymentProvider();
        }
        if (is_null($account)) {
            $account = self::randStr(5);
        }
        return SK_Model_PaymentProvider_MerchantAccount::create($paymentProvider, $account);
    }

    /**
     * @param SK_User[] $users
     * @return SK_Entity_Chat
     */
    public static function createChat(array $users = null) {
        if (is_null($users)) {
            $data['user'] = SKTest_TH::createUser();
            $data['recipients'] = array(SKTest_TH::createUser());
        } else {
            $data['user'] = $users[0];
            unset($users[0]);
            $data['recipients'] = $users;
        }

        /** @var SK_Entity_Chat $chat */
        $chat = SK_Entity_Chat::createStatic($data);

        return $chat;
    }

    /**
     * @param SK_User|null $user
     * @return SK_Entity_Chat_Show
     */
    public static function createChatShow(SK_User $user = null) {
        if (is_null($user)) {
            $user = SKTest_TH::createUser();
        }
        return SK_Entity_Chat_Show::createStatic(array('user' => $user));
    }

    /**
     * @param string|null             $text
     * @param SK_Entity_Abstract|null $entity
     * @param SK_User|null            $user
     * @return SK_Entity_Comment
     */
    public static function createComment($text = null, SK_Entity_Abstract $entity = null, SK_User $user = null) {
        if (null === $text) {
            $text = SKTest_TH::randStr(10);
        }
        if (null === $user) {
            $user = SKTest_TH::createUser();
        }
        if (null === $entity) {
            $entity = SKTest_TH::createBlogpost();
        }
        return SK_Entity_Comment::create($user, $entity, $text);
    }

    /**
     * @param CM_Model_Location|null $country
     * @param string|null            $url
     * @return SK_CountryRedirect
     */
    public static function createCountryRedirect(CM_Model_Location $country = null, $url = null) {
        if (is_null($country)) {
            $country = self::createLocationCountry();
        }
        if (is_null($url)) {
            $url = 'http://www.' . self::randStr(10) . 'foo';
        }
        return SK_CountryRedirect::createStatic(array('location' => $country, 'url' => $url));
    }

    /**
     * @param float|null $price
     * @param int|null   $period
     * @param float|null $recurringPrice
     * @param int|null   $recurringPeriod
     * @param float|null $lifetimeAmount
     * @return SK_ServiceBundle
     */
    public static function createServiceBundle($price = null, $period = null, $recurringPrice = null, $recurringPeriod = null, $lifetimeAmount = null) {
        if (null === $price) {
            $price = 3.1418;
        }
        if (null === $period) {
            $period = 7;
        }
        $lifetimeAmount = (null === $lifetimeAmount) ? 0 : (float) $lifetimeAmount;
        $data = array('price' => $price, 'period' => $period, 'lifetimeAmount' => $lifetimeAmount);
        if (null !== $recurringPrice && null !== $recurringPeriod) {
            $data['recurringPrice'] = $recurringPrice;
            $data['recurringPeriod'] = $recurringPeriod;
        }
        return SK_ServiceBundle::createStatic($data);
    }

    /**
     * @param SK_PaymentProvider_Abstract $paymentProvider
     * @param int|null                    $paymentType
     * @return SK_Model_PaymentOption
     */
    public static function createPaymentOption(SK_PaymentProvider_Abstract $paymentProvider = null, $paymentType = null) {
        if (null === $paymentProvider) {
            $paymentProvider = self::createPaymentProvider();
        }
        if (null === $paymentType) {
            $paymentType = SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD;
        }
        return SK_Model_PaymentOption::create($paymentProvider, $paymentType);
    }

    /**
     * @param CM_Site_Abstract|null $site
     * @param int|null              $paymentType
     * @param string|null           $label
     * @param bool|null             $enabled
     * @return SK_Model_PaymentOptionSet
     */
    public static function createPaymentOptionSet(CM_Site_Abstract $site = null, $paymentType = null, $label = null, $enabled = null) {
        if (null === $site) {
            $site = CM_Site_Abstract::factory();
        }
        if (null === $paymentType) {
            $paymentType = SK_Model_PaymentOption::PAYMENT_TYPE_CREDITCARD;
        }
        if (null === $label) {
            $label = self::randStr(5);
        }
        if (null === $enabled) {
            $enabled = true;
        }
        return SK_Model_PaymentOptionSet::create($site, $label, $paymentType, $enabled);
    }

    /**
     * @return SK_ServiceBundleSet
     */
    public static function createServiceBundleSet() {
        return SK_ServiceBundleSet::createStatic();
    }

    /**
     * @param SK_Entity_Chat_Show|null $show
     * @param SK_User|null             $user
     * @return SK_Entity_ShowArchive
     */
    public static function createShowArchive(SK_Entity_Chat_Show $show = null, SK_User $user = null) {
        if (is_null($show)) {
            $show = SKTest_TH::createChatShow($user);
        }
        if (!$show->hasVideoStreamChannels()) {
            SKTest_TH::createStreamChannelVideoShow($show);
        }
        if (!$show->hasStreamPublish()) {
            SKTest_TH::createStreamPublish($show->getUser(), $show->getVideoStreamChannel());
        }
        SKTest_TH::createStreamChannelVideoArchive($show->getVideoStreamChannel());
        return SK_Entity_ShowArchive::createStatic(array('show' => $show));
    }

    /**
     * @param SK_User|null $user
     * @return SK_Entity_Status
     */
    public static function createStatus(SK_User $user = null) {
        if (!$user) {
            $user = self::createUser();
        }
        $text = 'Hello there!';
        return SK_Entity_Status::create($user, $text);
    }

    /**
     * @param SK_User|null $user
     * @return SK_Entity_TextFormatterImage
     */
    public static function createTextFormatterImage(SK_User $user = null) {
        if (!$user) {
            $user = self::createUser();
        }
        $image = new CM_File_Image(DIR_TEST_DATA . '/img/test.jpg');
        return SK_Entity_TextFormatterImage::create($image, $user);
    }

    /**
     * @param SK_Entity_Chat $chat
     * @return SK_Model_StreamChannel_Video_Chat
     */
    public static function createStreamChannelVideoChat(SK_Entity_Chat $chat = null) {
        if (is_null($chat)) {
            $chat = SKTest_TH::createChat();
        }
        $params = CM_Params::factory(array('chatId' => $chat), false);
        return SK_Model_StreamChannel_Video_Chat::createStatic(array(
            'key'            => rand(1, 10000) . '_' . rand(1, 100),
            'width'          => 720,
            'height'         => 1028,
            'serverId'       => 1,
            'thumbnailCount' => 0,
            'params'         => $params,
            'adapterType'    => CM_Stream_Adapter_Video_Wowza::getTypeStatic(),
        ));
    }

    /**
     * @param SK_Entity_Chat_Show|null $chat
     * @return SK_Model_StreamChannel_Video_Show
     */
    public static function createStreamChannelVideoShow(SK_Entity_Chat_Show $chat = null) {
        if (is_null($chat)) {
            $chat = SKTest_TH::createChatShow();
        }
        $params = CM_Params::factory(array('chatId' => $chat), false);
        return SK_Model_StreamChannel_Video_Show::createStatic(array(
            'key'            => rand(1, 10000) . '_' . rand(1, 100),
            'width'          => 720,
            'height'         => 1028,
            'serverId'       => 1,
            'thumbnailCount' => 0,
            'adapterType'    => CM_Stream_Adapter_Video_Wowza::getTypeStatic(),
            'params'         => $params,
        ));
    }

    public static function createStreamChannel($type = null, $adapterType = null) {
        if (is_null($type)) {
            $type = CM_Model_StreamChannel_Video::getTypeStatic();
        }
        if (null === $adapterType) {
            $adapterType = CM_Stream_Adapter_Video_Wowza::getTypeStatic();
        }
        switch ($type) {
            case CM_Model_StreamChannel_Video::getTypeStatic():
                return CM_Model_StreamChannel_Video::createStatic(array(
                    'key'            => rand(1, 10000) . '_' . rand(1, 100),
                    'width'          => 720,
                    'height'         => 1028,
                    'serverId'       => 1,
                    'thumbnailCount' => 0,
                    'adapterType'    => $adapterType,
                ));
                break;
            default:
                throw new CM_Exception_Invalid('Invalid StreamChannel type `' . $type . '`');
        }
    }

    /**
     * @param CM_Model_StreamChannel_Video|null $streamChannel
     * @param CM_Model_User|null                $user
     * @return CM_Model_StreamChannelArchive_Video
     */
    public static function createStreamChannelVideoArchive(CM_Model_StreamChannel_Video $streamChannel = null, CM_Model_User $user = null) {
        if (is_null($streamChannel)) {
            $streamChannel = SKTest_TH::createStreamChannel();
            SKTest_TH::createStreamPublish($user, $streamChannel);
        }
        if (!$streamChannel->hasStreamPublish()) {
            SKTest_TH::createStreamPublish($user, $streamChannel);
        }
        return CM_Model_StreamChannelArchive_Video::createStatic(array('streamChannel' => $streamChannel));
    }

    /**
     * @param CM_Model_User|null                   $user
     * @param CM_Model_StreamChannel_Abstract|null $streamChannel
     * @return CM_Model_Stream_Publish
     */
    public static function createStreamPublish(CM_Model_User $user = null, CM_Model_StreamChannel_Abstract $streamChannel = null) {
        if (!$user) {
            $user = SKTest_TH::createUser();
        }
        if (is_null($streamChannel)) {
            $streamChannel = SKTest_TH::createStreamChannel();
        }
        $data = array('user' => $user, 'start' => time(), 'allowedUntil' => time() + 100, 'price' => rand(10, 50) / 10,
                      'key'  => rand(1, 10000) . '_' . rand(1, 100), 'streamChannel' => $streamChannel);
        return CM_Model_Stream_Publish::createStatic($data);
    }

    /**
     * @param CM_Model_User|null                   $user
     * @param CM_Model_StreamChannel_Abstract|null $streamChannel
     * @return CM_Model_Stream_Subscribe
     */
    public static function createStreamSubscribe(CM_Model_User $user = null, CM_Model_StreamChannel_Abstract $streamChannel = null) {
        if (is_null($streamChannel)) {
            $streamChannel = SKTest_TH::createStreamChannel();
        }
        return CM_Model_Stream_Subscribe::createStatic(array(
            'streamChannel' => $streamChannel,
            'user'          => $user,
            'start'         => time(),
            'allowedUntil'  => time() + 100,
            'key'           => rand(1, 10000) . '_' . rand(1, 100),
        ));
    }

    /**
     * @param SK_ServiceBundle $serviceBundle
     */
    public static function deleteServiceBundle(SK_ServiceBundle $serviceBundle) {
        CM_Db_Db::delete('sk_serviceBundle', array('id' => $serviceBundle->getId()));
        CM_Db_Db::delete('sk_serviceBundle_service', array('serviceBundleId' => $serviceBundle->getId()));
        CM_Db_Db::delete('sk_paymentProvider_bundle', array('bundleId' => $serviceBundle->getId()));
    }

    public static function inProfileArray(SK_Entity_Profile $needle, array $haystack) {
        foreach ($haystack as $straw) {
            if ($needle->equals($straw)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param int    $length
     * @param string $charset
     * @return string
     */
    public static function randStr($length, $charset = 'abcdefghijklmnopqrstuvwxyz0123456789') {
        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count - 1)];
        }
        return $str;
    }

    /**
     * @param CM_Model_Abstract $model
     */
    public static function reinstantiateModel(CM_Model_Abstract &$model) {
        $model = CM_Model_Abstract::factoryGeneric($model->getType(), $model->getIdRaw());
    }
}
