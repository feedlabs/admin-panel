<?php

class SK_UserTest extends SKTest_TestCase {

    public static function setUpBeforeClass() {
    }

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    /**
     * @param string|null           $username
     * @param CM_Site_Abstract|null $site
     * @return array
     */
    private function _getData($username = null, CM_Site_Abstract $site = null) {
        $data = array();
        $data['site'] = $site;
        $data['sex'] = SK_User::SEX_MALE;
        $data['location'] = SKTest_TH::createLocationCity();
        $data['birthdate'] = new DateTime('@' . (time() - rand(18, 100) * 366 * 24 * 60 * 60));
        $data['password'] = md5(rand() . uniqid());
        $data['username'] = (string) $username;
        while (empty($data['username']) || SK_User::findUsername($data['username'])) {
            $data['username'] .= SKTest_TH::randStr(2);
        }
        $data['email'] = $data['username'] . '@example.com';
        return $data;
    }

    public function testCreate() {
        $data = $this->_getData('foo');
        /** @var SK_User $user */
        $user = SK_User::createStatic($data);
        $this->assertSame('foo', $user->getUsername());
    }

    public function testGetUsernameFiltered() {
        $this->assertSame('test_username_filtered', SK_User::getUsernameFiltered('test username%filtered'));
        $this->assertSame('testUsername', SK_User::getUsernameFiltered('testUsername'));
        $this->assertSame('test_username', SK_User::getUsernameFiltered('testéusername'));
        $this->assertSame('test_username', SK_User::getUsernameFiltered('testéöusername'));
        $this->assertSame('test__username', SK_User::getUsernameFiltered('test_éöusername'));
    }

    public function testUsernameIsValid() {
        $this->assertTrue(SK_User::usernameIsValid('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_'));
        $this->assertFalse(SK_User::usernameIsValid('$ds'));
        $this->assertFalse(SK_User::usernameIsValid('asé'));
    }

    public function testCreateLongUsername() {
        $username = null;
        while (empty($username) || SK_User::findUsername($username)) {
            $username = SKTest_TH::randStr(100);
        }
        $data = $this->_getData($username);
        /** @var SK_User $user */
        $user = SK_User::createStatic($data);
        $this->assertSame(substr($username, 0, 32), $user->getUsername());

        try {
            $user = SK_User::createStatic($data);
            $this->fail('Can re-create user with long username');
        } catch (CM_Exception $e) {
            $this->assertContains('Duplicate entry', $e->getMessage());
        }
    }

    public function testCreateWithSite() {
        $siteDefault = CM_Site_Abstract::factory();
        $siteNonDefault = $this->getMockSite();

        $data = $this->_getData();
        /** @var SK_User $user */
        $user = SK_User::createStatic($data);
        $this->assertEquals($siteDefault, $user->getSite());

        $data = $this->_getData(null, $siteNonDefault);
        /** @var SK_User $user */
        $user = SK_User::createStatic($data);
        $this->assertEquals($siteNonDefault, $user->getSite());
    }

    public function testAuthenticate() {
        $password = 'ckhjsbeasdjkdfvdfkghjhsjk';
        $user = SKTest_TH::createUser();
        $user->setPassword($password);
        $this->assertFalse($user->getOnline());
        $this->assertEquals($user, SK_User::authenticate($user->getUsername(), $password));
        $user->_change();

        //unsuccessfull login
        $user = SKTest_TH::createUser();
        try {
            SK_User::authenticate($user->getUsername(), 'wrongpassword');
            $this->fail('login with wrong password');
        } catch (CM_Exception_AuthFailed $ex) {
        }

        $user = SKTest_TH::createUser();
        $user->setBlocked(true);
        try {
            SK_User::authenticate($user->getUsername(), $user->_get('password'));
            $this->fail('login with blocked account');
        } catch (CM_Exception_AuthFailed $ex) {
        }
        $user = SKTest_TH::createUser();
    }

    public function testCheckEmailVerificationCode() {
        // With correct code
        $user = SKTest_TH::createUser();
        $user->sendEmailVerificationRequest();
        $code = CM_Db_Db::select('sk_user_emailVerification', 'code', array('userId' => $user->getId()))->fetchColumn();
        $this->assertEquals($user, SK_User::checkEmailVerificationCode($code));
        SKTest_TH::reinstantiateModel($user);
        $this->assertTrue($user->getEmailVerified());

        // With incorrect code
        $user = SKTest_TH::createUser();
        $user->sendEmailVerificationRequest();
        $this->assertNull(SK_User::checkEmailVerificationCode('wrong-code'));
        SKTest_TH::reinstantiateModel($user);
        $this->assertFalse($user->getEmailVerified());
    }

    public function testSendEmailVerificationRequest() {
        $user = SKTest_TH::createUser();
        $user->sendEmailVerificationRequest();
        $this->assertRow('sk_user_emailVerification', array('userId' => $user->getId()));
    }

    public function testGetBillingCascade() {
        $user = SKTest_TH::createUser();
        $cascade = $user->getBillingCascade();

        $this->assertInstanceOf('SK_Model_BillingCascade', $cascade);
        $this->assertEquals($cascade, $user->getBillingCascade());
    }

    public function testGetTextFormatterImages() {
        $user = SKTest_TH::createUser(SK_User::SEX_FEMALE);
        $paging = $user->getTextFormatterImages();

        $this->assertInstanceOf('SK_Paging_TextFormatterImage_User', $paging);
        $this->assertEquals(0, $paging->getCount());
    }

    public function testGetPhotoCount() {
        $user = SKTest_TH::createUser();

        $this->assertEquals(0, $user->getPhotos()->getCount());
    }

    public function testGetShowArchiveList() {
        $user = SKTest_TH::createUser();
        SKTest_TH::createShowArchive();
        $this->assertSame(0, $user->getShowArchiveList()->getCount());
        SKTest_TH::createShowArchive(null, $user);
        $this->assertSame(1, $user->getShowArchiveList()->getCount());
    }

    public function testDelete() {
        $user = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $profile = $user->getProfile();

        $photo = SKTest_TH::createPhoto($user);
        $status = SK_Entity_Status::create($user, 'bar');
        $comment = SKTest_TH::createComment('foo', null, $user);
        $blogpost = SKTest_TH::createBlogpost($user);
        $chat = SKTest_TH::createChat(array($user, $user2));
        $video = SKTest_TH::createVideo($user);
        $user->setThumbnail($photo);
        $thumbnail = $user->getThumbnailFile();
        $conversation = SKTest_TH::createConversation($user2, $user);
        $user2->getConversations()->getItem(0)->delete();
        $pinboard = SKTest_TH::createPinboard($user);

        $pinboard->add($photo);
        $pinboard->add($video);

        $linkStreamateUser = new SK_EntityProvider_Streamate_Link_User();
        $linkStreamateUser->linkModel(12345, $user);

        $linkAdultCentroUser = new SK_EntityProvider_AdultCentro_Link_User();
        $linkAdultCentroUser->linkModel(34567, $user);

        $userReviewed = SKTest_TH::createUser();
        $userReviewed->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM, $user);

        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM);
        SKTest_TH::timeForward(1);
        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_WARN);

        $user3 = SKTest_TH::createUser();
        $user3->setLanguage(SKTest_TH::createLanguage());
        $userTemplate = SK_Entertainment_UserTemplate::create($user3);
        $userTemplate->getUserList()->add($user);

        $this->assertSame(1, $userTemplate->getUserList()->getCount());

        $this->assertSame(1, $userReviewed->getReviews()->get()->getCount());

        SK_Model_ReviewCandidate::create($photo);
        $reviewCandidateList = new SK_Paging_ReviewCandidate_All();
        $this->assertSame(1, $reviewCandidateList->getCount());

        $user->delete();

        $this->assertTrue($userTemplate->getUserList()->isEmpty());

        $this->assertSame(0, $userReviewed->getReviews()->get()->getCount());

        try {
            new SK_User($user->getId());
            $this->fail("Could instantiate deleted user");
        } catch (CM_Exception $e) {
            $this->assertTrue(true);
        }

        try {
            new SK_Entity_Conversation($conversation->getId());
            $this->fail("Could instantiate deleted conversation");
        } catch (CM_Exception $e) {
            $this->assertTrue(true);
        }

        try {
            new SK_Entity_Blogpost($blogpost->getId());
            $this->fail("Could instantiate deleted blogpost");
        } catch (CM_Exception $e) {
            $this->assertTrue(true);
        }

        try {
            new SK_Entity_Photo($photo->getId());
            $this->fail("Could instantiate deleted photo");
        } catch (CM_Exception $e) {
            $this->assertTrue(true);
        }

        try {
            new SK_Entity_Chat($chat->getId());
            $this->fail("Could instantiate deleted chat");
        } catch (CM_Exception $e) {
            $this->assertTrue(true);
        }

        try {
            new SK_Entity_Video($video->getId());
            $this->fail("Could instantiate deleted video");
        } catch (CM_Exception $e) {
            $this->assertTrue(true);
        }

        $this->assertFalse($thumbnail->getExists());

        try {
            new SK_Entity_Comment($comment->getId());
            $this->fail('Comment not deleted');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }

        try {
            new SK_Entity_Status($status->getId());
            $this->fail('Could instantiate deleted user\'s status');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }

        try {
            new SK_Entity_Pinboard($pinboard->getId());
            $this->fail('Pinboard not deleted');
        } catch (CM_Exception $e) {
            $this->assertTrue(true);
        }

        $reviewCandidateList = new SK_Paging_ReviewCandidate_All();
        $this->assertSame(0, $reviewCandidateList->getCount());

        $this->assertNotRow('cm_user', array('userId' => $user->getId()));
        $this->assertNotRow('sk_user', array('userId' => $user->getId()));
        $this->assertNotRow('sk_entityProvider_streamate_user', array('modelId' => $user->getId()));
        $this->assertNotRow('sk_entityProvider_adultCentro_user', array('modelId' => $user->getId()));
    }

    public function testDeleteUserMarkedAsSpam() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $conversation1 = SKTest_TH::createConversation($user1, $user2);
        $conversation2 = SKTest_TH::createConversation($user2, $user1);
        $comment = SKTest_TH::createComment('test', null, $user1);

        $user1->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM);
        $user1->delete();

        try {
            new SK_User($user1->getId());
            $this->fail('User should be deleted.');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }

        try {
            new SK_Entity_Conversation($conversation1->getId());
            $this->fail('Conversation should be deleted.');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }

        try {
            new SK_Entity_Conversation($conversation2->getId());
            $this->assertTrue(true);
        } catch (CM_Exception_Nonexistent $e) {
            $this->fail('Conversation should NOT be deleted.');
        }

        try {
            new SK_Entity_Comment($comment->getId());
            $this->fail('Comment should be deleted.');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }
    }

    public function testDeleteUserWithSubscription() {
        $user = SKTest_TH::createUser();
        $paymentProviderCCBill = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::CCBILL);
        $paymentProviderRocketGate = SKTest_TH::createPaymentProvider(SK_PaymentProvider_Abstract::ROCKETGATE);
        $transactionGroupCCBill = SKTest_TH::createPaymentTransactionGroup($paymentProviderCCBill, 111, $user->getId());
        $transactionGroupRocketGate = SKTest_TH::createPaymentTransactionGroup($paymentProviderRocketGate, 222, $user->getId());
        $serviceBundle = SKTest_TH::createServiceBundle(19, 30, 29, 30);
        SKTest_TH::createPaymentTransaction($user->getId(), $paymentProviderCCBill, null, null, $serviceBundle, $transactionGroupCCBill);
        SKTest_TH::createPaymentTransaction($user->getId(), $paymentProviderRocketGate, null, null, $serviceBundle, $transactionGroupRocketGate);

        $transactionGroups = new SK_Paging_PaymentTransactionGroup_User($user);
        /** @var SK_PaymentTransactionGroup $transactionGroup */
        foreach ($transactionGroups as $transactionGroup) {
            $this->assertNull($transactionGroup->getCancelStamp());
        }

        $user->delete();

        $transactionGroups = new SK_Paging_PaymentTransactionGroup_User($user);
        foreach ($transactionGroups as $transactionGroup) {
            if ($transactionGroup->getPaymentProvider()->cancelOnDeleteUser()) {
                $this->assertSame(time(), $transactionGroup->getCancelStamp());
            } else {
                $this->assertNull($transactionGroup->getCancelStamp());
            }
        }
    }

    public function testSex() {
        $oldSex = SK_User::SEX_MALE;
        $newSex = SK_User::SEX_FEMALE;
        $user = SKTest_TH::createUser($oldSex);

        $this->assertInternalType('int', $user->getSex());
        $this->assertEquals($oldSex, $user->getSex());

        $user->setSex($newSex);
        $this->assertEquals($newSex, $user->getSex());
    }

    public function testBlocked() {
        $user = SKTest_TH::createUser();

        $user->setBlocked(false);
        $this->assertFalse($user->getBlocked());

        $user->setBlocked(true);
        $this->assertTrue($user->getBlocked());
    }

    public function testOnline() {
        $profile = SKTest_TH::createUser()->getProfile();
        $this->assertFalse($profile->getUser()->getOnline());
        $profile->getUser()->setOnline();
        $this->assertTrue($profile->getUser()->getOnline());
        $profile->getUser()->setOnline(false);
        $this->assertFalse($profile->getUser()->getOnline());
    }

    public function testSetPassword() {
        $user = SKTest_TH::createUser();
        $password = 'sdbifuifhbwiurhewb';
        $user->setPassword($password);
        $this->assertEquals($user, SK_User::authenticate($user->getUsername(), $password));
    }

    public function testGetSetEmailVerified() {
        $user = SKTest_TH::createUser();
        $this->assertFalse($user->getEmailVerified());

        $user->setEmailVerified(true);
        $this->assertTrue($user->getEmailVerified());

        $user->setEmailVerified(false);
        $this->assertFalse($user->getEmailVerified());
    }

    public function testGetSetEmailVerifiedBlocked() {
        $user = SKTest_TH::createUser();
        $user->setEmailVerified(true);
        $this->assertTrue($user->getEmailVerified());

        $user->setBlocked(true);
        $this->assertFalse($user->getEmailVerified());

        $user->setBlocked(false);
        $this->assertTrue($user->getEmailVerified());
    }

    public function testApplyRoles() {
        $user = SKTest_TH::createUser(SK_User::SEX_FEMALE);
        $this->assertTrue($user->getRoles()->contains(SK_Role::FEMALE));
        $user->setSex(SK_User::SEX_MALE);
        $this->assertFalse($user->getRoles()->contains(SK_Role::FEMALE));
        $user = SKTest_TH::createUser();
        $this->assertFalse($user->getRoles()->contains(SK_Role::FEMALE));
        $user->setSex(SK_User::SEX_FEMALE);
        $this->assertTrue($user->getRoles()->contains(SK_Role::FEMALE));
    }

    public function testFindUsername() {
        $username1 = 'foobar1';
        $username2 = 'foobar2';

        for ($i = 0; $i < 2; $i++) {
            $this->assertNull(SK_User::findUsername($username1));
            $this->assertNull(SK_User::findUsername($username2));
        }

        $user = SKTest_TH::createUser(null, $username1);
        for ($i = 0; $i < 2; $i++) {
            $this->assertEquals($user, SK_User::findUsername($username1));
            $this->assertNull(SK_User::findUsername($username2));
        }

        $user->setUsername($username2);
        for ($i = 0; $i < 2; $i++) {
            $this->assertNull(SK_User::findUsername($username1));
            $this->assertEquals($user, SK_User::findUsername($username2));
        }

        $user->delete();
        for ($i = 0; $i < 2; $i++) {
            $this->assertNull(SK_User::findUsername($username1));
            $this->assertNull(SK_User::findUsername($username2));
        }
    }

    public function testFindEmail() {
        $user = SKTest_TH::createUser();
        $this->assertEquals($user, SK_User::findEmail($user->getEmail()));

        $this->assertNull(SK_User::findEmail('nonexistent@example.com'));
    }

    public function testLoadData() {
        $user = SKTest_TH::createUser();
        CM_Db_Db::delete('cm_user', array('userId' => $user->getId()));
        $user->_change();
        try {
            new SK_User($user->getId());
            $this->fail("Could instantiate user with no cm table entry");
        } catch (CM_Exception $e) {
            $this->assertTrue(true);
        }
        CM_Db_Db::delete('sk_user', array('userId' => $user->getId()));

        $user = SKTest_TH::createUser();
        CM_Db_Db::delete('sk_user', array('userId' => $user->getId()));
        $user->_change();
        try {
            new SK_User($user->getId());
            $this->fail("Could instantiate user with no sk table entry");
        } catch (CM_Exception $e) {
            $this->assertTrue(true);
        }
        CM_Db_Db::delete('cm_user', array('userId' => $user->getId()));
    }

    public function testIsOfficial() {
        $site = $this->getMockSite('SK_Site_Abstract', null, array('officialUser' => 'foo'));
        $user = SKTest_TH::createUser(null, 'foo', $site);

        $this->assertTrue($user->isOfficial());
    }

    public function testGetTotalCount() {
        $quantity = 7;
        $count = 0;
        while ($count < $quantity) {
            SKTest_TH::createUser();
            $count++;
        }

        $this->assertSame(7, SK_User::getTotalCount());
    }

    public function testGetTotalCountIncreaseRatePerSecond() {
        $quantity = 8;
        $count = 0;
        while ($count < $quantity) {
            SKTest_TH::timeDaysForward(1);
            SKTest_TH::createUser();
            $count++;
        }

        $this->assertSame(1 / 86400, SK_User::getTotalCountIncreaseRatePerSecond());
    }

    public function testSetThumbnail() {
        $user = SKTest_TH::createUser();
        $image = new CM_File_Image(DIR_TEST_DATA . 'img/animated.gif');
        $photo = SK_Entity_Photo::create($image, $user);
        $user->setThumbnail($photo);
        $thumbnail = $user->getThumbnailFile();

        $this->assertTrue($user->hasThumbnail());
        $this->assertSame('image/jpeg', $thumbnail->getMimeType());
    }

    public function testSetEmailUnverifiedInactive() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $user1->setEmailVerified(true);
        $user2->setEmailVerified(true);

        SKTest_TH::timeForward(100);
        $user1->updateLatestactivity();
        SKTest_TH::timeForward(100);

        SK_User::setEmailUnverifiedInactive(150);
        $this->assertSame(true, $user1->getEmailVerified());
        $this->assertSame(false, $user2->getEmailVerified());
    }

    public function testSetBirthdate() {
        $user = SKTest_TH::createUser();
        $now = new DateTime('now');
        $date = new DateTime($now->format('Y') - (SK_User::AGE_MIN + 2) . '-' . $now->format('m-d'));
        $user->setBirthdate($date);
        $this->assertEquals($date, $user->getBirthdate());
    }

    public function testUserTemplateOnBeforeDeleteNotFemale() {
        $user = SKTest_TH::createUser();
        $this->_createPhotos($user);
        $this->_setLanguage($user);
        $this->_setSpam($user);

        $this->assertTrue(SK_Entertainment_UserTemplate::getAll()->isEmpty());
        $user->delete();
        $this->assertTrue(SK_Entertainment_UserTemplate::getAll()->isEmpty());
    }

    public function testUserTemplateAlreadyExists() {
        $user = SKTest_TH::createUser();
        $this->_createPhotos($user);
        $this->_setLanguage($user);
        $this->_setFemale($user);
        $this->_setSpam($user);

        $template = SK_Entertainment_UserTemplate::create($user);
        $template->getUserList()->add($user);

        $this->assertSame(1, SK_Entertainment_UserTemplate::getAll()->getCount());
        $user->delete();
        $this->assertSame(1, SK_Entertainment_UserTemplate::getAll()->getCount());
    }

    public function testUserTemplateOnBeforeDeleteNotEnoughPhotos() {
        $user = SKTest_TH::createUser();
        $this->_setLanguage($user);
        $this->_setFemale($user);
        $this->_setSpam($user);

        $this->assertTrue(SK_Entertainment_UserTemplate::getAll()->isEmpty());
        $user->delete();
        $this->assertTrue(SK_Entertainment_UserTemplate::getAll()->isEmpty());
    }

    public function testHasCamShow() {
        $user = SKTest_TH::createUser();
        $this->assertFalse($user->hasCamShows());

        SKTest_TH::createCamShow($user);
        $this->assertTrue($user->hasCamShows());
    }

    private function _createPhotos(SK_User $user) {
        for ($i = 0; $i < 5; $i++) {
            SKTest_TH::createPhoto($user);
        }
    }

    private function _setLanguage(SK_User $user) {
        $user->setLanguage(SKTest_TH::createLanguage());
    }

    private function _setFemale(SK_User $user) {
        $user->setSex(SK_User::SEX_FEMALE);
    }

    private function _setSpam(SK_user $user) {
        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM);
    }
}
