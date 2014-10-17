<?php

class SK_Entertainment_UserTemplateTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $user->setLanguage(SKTest_TH::createLanguage());

        $profileFields = $user->getProfile()->getFields();
        $profileFields->set('height', 32);
        $profileFields->set('match_height', array(2));
        $profileFields->set('body_type', 4);
        $profileFields->set('match_body_type', array(4));
        $profileFields->set('religion', 32);
        $profileFields->set('match_religion', array(4));
        $profileFields->set('ethnicity', 8);
        $profileFields->set('match_ethnicity', array(8));
        $profileFields->set('eye_color', 4);
        $profileFields->set('match_eye_color', array(8, 2));
        $profileFields->set('hair_color', 16);
        $profileFields->set('match_hair_color', array(8, 4));
        $profileFields->set('have_children', 8);
        $profileFields->set('language', array(8, 4, 2));
        $profileFields->set('education', 16);
        $profileFields->set('match_education', array(16));
        $profileFields->set('income', 16);
        $profileFields->set('match_income', array(16, 2));
        $profileFields->set('smoke', 32);
        $profileFields->set('match_smoke', array(16, 4));
        $profileFields->set('drink', 64);
        $profileFields->set('match_drink', array(16, 4, 2));
        $profileFields->set('interests', array(16, 4, 2, 1));
        $profileFields->set('participation_', array(16, 8));
        $profileFields->set('fetishes_', array(32, 8, 1));
        $profileFields->set('on_the_site_for_', array(32, 8, 2));
        $profileFields->set('relationship_status___', array(16, 8, 2, 1));
        $profileFields->set('match_agerange', '20-30');

        $userTemplate = SK_Entertainment_UserTemplate::create($user);
        $this->assertSame(time() - $user->getBirthdate()->getTimestamp(), $userTemplate->getAge());
        $this->assertSame(0, $userTemplate->getResurrectionCount());
        $this->assertSame(time(), $userTemplate->getCreated());
        $this->assertFalse($userTemplate->getReviewed());

        $fields = '{"match_sex":' . SK_User::SEX_MALE . ',"height":32,"match_height":2,"body_type":4,"match_body_type":4,"religion":32,"match_religion":4,"ethnicity":8,"match_ethnicity":8,"eye_color":4,"match_eye_color":10,"hair_color":16,"match_hair_color":12,"have_children":8,"language":14,"education":16,"match_education":16,"income":16,"match_income":18,"smoke":32,"match_smoke":20,"drink":64,"match_drink":22,"interests":23,"participation_":24,"fetishes_":41,"on_the_site_for_":42,"relationship_status___":27,"match_agerange":"20-30"}';
        $this->assertSame($fields, $userTemplate->_get('profileFields'));
    }

    public function testDelete() {
        $user1 = SKTest_TH::createUser();
        $user1->setLanguage(SKTest_TH::createLanguage());
        $userTemplate = SK_Entertainment_UserTemplate::create($user1);
        $user2 = SKTest_TH::createUser();
        $userTemplate->getUserList()->add($user2);

        $this->assertEquals($userTemplate, SK_Entertainment_UserTemplate::findUser($user2));
        $userTemplate->delete();
        $this->assertNull(SK_Entertainment_UserTemplate::findUser($user2));
        try {
            new SK_User($user2->getId());
            $this->fail('Templated user should have been deleted with the template');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }
    }

    public function testFindUser() {
        $user1 = SKTest_TH::createUser();
        $user1->setLanguage(SKTest_TH::createLanguage());
        $userTemplate = SK_Entertainment_UserTemplate::create($user1);
        $user2 = SKTest_TH::createUser();
        $userTemplate->getUserList()->add($user2);

        $this->assertEquals($userTemplate, SK_Entertainment_UserTemplate::findUser($user2));
        $userTemplate->getUserList()->remove($user2);
        $this->assertNull(SK_Entertainment_UserTemplate::findUser($user2));
    }

    public function testGetProfileField() {
        $userTemplate = new SK_Entertainment_UserTemplate();
        $userTemplate->_set('profileFields', '{"height":32,"match_height":3}');
        $this->assertSame(32, $userTemplate->getProfileField('height'));
        $this->assertSame(3, $userTemplate->getProfileField('match_height'));
        $this->assertNull($userTemplate->getProfileField('religion'));
        try {
            $userTemplate->getProfileField('nonsense field');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('`nonsense field` is not a valid profile field', $ex->getMessage());
        }
    }

    public function testGetProfileFieldList() {
        $user = SKTest_TH::createUser();
        $user->setLanguage(SKTest_TH::createLanguage());

        $profileFields = $user->getProfile()->getFields();
        $profileFields->set('height', 32);
        $profileFields->set('match_height', array(2));

        $userTemplate = SK_Entertainment_UserTemplate::create($user);

        $expected = array('match_sex' => SK_User::SEX_MALE, 'height' => 32, 'match_height' => 2);
        $this->assertSame($expected, $userTemplate->getProfileFieldList());
    }

    public function testGetSetResurrectionCount() {
        $user = SKTest_TH::createUser();
        $user->setLanguage(SKTest_TH::createLanguage());
        $userTemplate = SK_Entertainment_UserTemplate::create($user);
        $this->assertSame(0, $userTemplate->getResurrectionCount());

        $userTemplate->setResurrectionCount(1);
        $this->assertSame(1, $userTemplate->getResurrectionCount());
    }

    public function testGetSetReviewed() {
        $user = SKTest_TH::createUser();
        $user->setLanguage(SKTest_TH::createLanguage());
        $userTemplate = SK_Entertainment_UserTemplate::create($user);
        $this->assertFalse($userTemplate->getReviewed());

        $userTemplate->setReviewed();
        $this->assertTrue($userTemplate->getReviewed());

        $userTemplate->setReviewed(false);
        $this->assertFalse($userTemplate->getReviewed());
    }

    public function testCreateUser() {
        $locationCity = SKTest_TH::createLocationCity();
        $locationCountry = $locationCity->get(CM_Model_Location::LEVEL_COUNTRY);
        $schedule = $this->getMockBuilder('SK_Entertainment_Schedule_EntertainerCreate')->setMethods(array('execute'))->getMock();
        $schedule->expects($this->once())->method('execute');
        $mockClass = $this->_getMockUsertemplate($schedule, 1);
        $mockClass->mockStaticMethod('generateUsername')->set(function () {
            return 'fooUser';
        });
        $template = $mockClass->newInstance();
        /** @var SK_Entertainment_UserTemplate $template */
        $now = new DateTime('now');
        $birthdate = new DateTime(($now->format('Y') - 24) . '-' . $now->format('m-d'));
        $template->_set('age', time() - $birthdate->getTimestamp());
        $template->_set('countryId', $locationCountry->getId());
        $template->_set('profileFields', '{"height":32,"match_height":2,"body_type":4,"match_religion":4,"match_eye_color":10,"fetishes_":41,"match_agerange":"20-30"}');
        $this->assertTrue($template->getUserList()->isEmpty());

        $user = $template->createUser($locationCity);
        $this->assertContains($user, $template->getUserList());
        $this->assertTrue($user->getRoles()->contains(SK_Role::ENTERTAINER));
        $this->assertSame('fooUser', $user->getUsername());
        $this->assertSame(24, $user->getAge());
        $this->assertEquals($locationCity, $user->getLocation());
        $this->assertSame(32, $user->getProfile()->getFields()->getScalar('height'));
        $this->assertSame(2, $user->getProfile()->getFields()->getScalar('match_height'));
        $this->assertSame(4, $user->getProfile()->getFields()->getScalar('body_type'));
        $this->assertSame(10, $user->getProfile()->getFields()->getScalar('match_eye_color'));
        $this->assertSame(41, $user->getProfile()->getFields()->getScalar('fetishes_'));
        $this->assertSame('20-30', $user->getProfile()->getFields()->getScalar('match_agerange'));
    }

    public function testUserAgeOutOfBounds() {
        $now = new DateTime('@' . time());
        $schedule = $this->getMockBuilder('SK_Entertainment_Schedule_EntertainerCreate')->setMethods(array('execute'))->getMock();
        $schedule->expects($this->any())->method('execute');
        /** @var SK_Entertainment_UserTemplate $template */
        $template = $this->_getMockUsertemplate($schedule, 1)->newInstance();
        $template->_set('profileFields', '{"height":1,"match_height":2,"body_type":3,"match_religion":4,"match_eye_color":10,"fetishes_":25,"match_agerange":"20-30"}');

        $ageTooYoung = $now->sub(new DateInterval('P' . (SK_Entertainment_UserTemplate::AGE_MIN - 1) . 'Y'));
        $template->_set(array('age' => time() - $ageTooYoung->getTimestamp()));

        $user = $template->createUser(SKTest_TH::createLocationCity());
        $this->assertSame(SK_Entertainment_UserTemplate::AGE_MIN, $user->getAge());

        $ageTooOld = $now->sub(new DateInterval('P' . (SK_Entertainment_UserTemplate::AGE_MAX + 1) . 'Y'));
        $template->_set(array('age' => time() - $ageTooOld->getTimestamp()));

        $user = $template->createUser(SKTest_TH::createLocationCity());
        $this->assertSame(SK_Entertainment_UserTemplate::AGE_MAX, $user->getAge());
    }

    public function testCreateNewEntertainers() {
        $this->_setupUsernameGenerator();
        CM_Config::get()->CM_Elasticsearch_Client->enabled = true;
        $type = new SK_Elasticsearch_Type_User();
        $searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $searchIndexCli->create($type->getIndex()->getName());
        CM_Config::get()->SK_Entertainment_UserTemplate->entertainerCountMin = 5;

        $template1 = SKTest_TH::createEntertainmentUserTemplate(true);
        $template2 = SKTest_TH::createEntertainmentUserTemplate(true);
        $template3 = SKTest_TH::createEntertainmentUserTemplate(true);
        $template1->createUser(SKTest_TH::createLocationCity());
        $this->_updateSearchIndex();

        $entertainerList = new SK_Paging_User_Entertainer(SK_User::SEX_FEMALE);
        $this->assertSame(1, $entertainerList->getCount());

        $user1 = SKTest_TH::createUser(SK_User::SEX_MALE);
        SKTest_TH::timeForward(1);
        $user2 = SKTest_TH::createUser(SK_User::SEX_MALE);
        SKTest_TH::timeForward(1);
        $user3 = SKTest_TH::createUser(SK_User::SEX_MALE);
        SKTest_TH::timeForward(1);
        $user4 = SKTest_TH::createUser(SK_User::SEX_MALE);
        SKTest_TH::timeForward(1);
        $user5 = SKTest_TH::createUser(SK_User::SEX_MALE);
        SKTest_TH::timeForward(1);
        $user6 = SKTest_TH::createUser(SK_User::SEX_MALE);
        $this->_updateSearchIndex();
        SK_Entertainment_UserTemplate::createNewEntertainers(10);

        $this->_updateSearchIndex();
        $entertainerList = new SK_Paging_User_Entertainer(SK_User::SEX_FEMALE);
        $this->assertSame(5, $entertainerList->getCount());
        $this->assertSame(2, $template1->getUserList()->getCount());
        $this->assertSame(2, $template2->getUserList()->getCount());
        $this->assertSame(1, $template3->getUserList()->getCount());
        $this->assertSame($user6->getId(), CM_Option::getInstance()->get('entertainer_userIdUsed'));
        $this->assertEquals($user6->getLocation(), $template2->getUserList()->getItem(0)->getLocation());
        $this->assertEquals($user5->getLocation(), $template3->getUserList()->getItem(0)->getLocation());
        $this->assertEquals($user4->getLocation(), $template1->getUserList()->getItem(1)->getLocation());
        $this->assertEquals($user3->getLocation(), $template2->getUserList()->getItem(1)->getLocation());
    }

    public function testCreateNewEntertainersHighestId() {
        $this->_setupUsernameGenerator();
        CM_Config::get()->CM_Elasticsearch_Client->enabled = true;
        $type = new SK_Elasticsearch_Type_User();
        $searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $searchIndexCli->create($type->getIndex()->getName());
        CM_Config::get()->SK_Entertainment_UserTemplate->entertainerCountMin = 3;

        $template1 = SKTest_TH::createEntertainmentUserTemplate(true);

        SKTest_TH::timeForward(1);
        $user1 = SKTest_TH::createUser(SK_User::SEX_MALE);
        $user2 = SKTest_TH::createUser(SK_User::SEX_MALE);
        $this->_updateSearchIndex();
        SK_Entertainment_UserTemplate::createNewEntertainers(10);

        $this->_updateSearchIndex();
        $this->assertSame($user2->getId(), CM_Option::getInstance()->get('entertainer_userIdUsed'));
    }

    private function _setupUsernameGenerator() {
        $typesContentLists = [
            SK_Entertainment_UsernamePrefixList::getTypeStatic(),
            SK_User_UsernameGenerator_FirstnameList::getTypeStatic(),
            SK_User_UsernameGenerator_SurnameList::getTypeStatic(),
        ];
        $values = [];
        foreach ($typesContentLists as $type) {
            for ($i = 0; $i < 10; $i++) {
                $values[] = [$type, $i];
            }
        }
        CM_Db_Db::insertIgnore('cm_string', array('type', 'string'), $values);
    }

    private function _updateSearchIndex() {
        CM_Model_Location::createAggregation();
        $type = new SK_Elasticsearch_Type_User();
        $searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $searchIndexCli->update($type->getIndex()->getName());
    }

    private function _getMockUsertemplate($scheduleCreate, $id) {
        $mockClass = $this->mockClass('SK_Entertainment_UserTemplate');
        $mockClass->mockMethod('_getScheduleCreate')->set(function () use ($scheduleCreate) {
            return $scheduleCreate;
        });
        $mockClass->mockMethod('getId')->set(function () use ($id) {
            return $id;
        });
        $mockClass->mockStaticMethod('generateUsername')->set(function () {
            do {
                $username = SKTest_TH::randStr(10);
            } while (SK_User::findUsername($username));
            return $username;
        });
        return $mockClass;
    }
}
