<?php

class SK_EntityProvider_Streamate_StreamateTest extends SKTest_TestCase {

    public function setUp() {
        CM_Config::get()->CM_Elasticsearch_Client->enabled = true;
        $type = new SK_Elasticsearch_Type_CamShow();
        $cli = new CM_Elasticsearch_Index_Cli();
        $cli->create($type->getIndex()->getName());

        SK_EntityProvider_Streamate_Streamate::createStatic(array('name' => 'Streamate', 'processInterval' => 0));

        $countryId = CM_Db_Db::insert('cm_model_location_country', array('name' => 'United States', 'abbreviation' => 'US'));
        $stateId = CM_Db_Db::insert('cm_model_location_state', array('name' => 'NotExisting', 'countryId' => $countryId));
        foreach (array('New York', 'Los Angeles', 'Washington', 'San Francisco') as $city) {
            CM_Db_Db::insert('cm_model_location_city', array(
                'name'      => $city,
                'lat'       => rand(0, 100),
                'lon'       => rand(0, 100),
                'countryId' => $countryId,
                'stateId'   => $stateId,
            ));
        }
    }

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testProcess() {
        $performer = $this->_getPerformer();
        $clientMock = $this->getMock('SKService_Streamate_Performers_Client', array('getPerformersList'));
        $clientMock->expects($this->any())->method('getPerformersList')->will($this->returnValue(array($performer)));

        $entityProviderMock = $this->getMock('SK_EntityProvider_Streamate_Streamate', array('_getClient'));
        $entityProviderMock->expects($this->any())->method('_getClient')->will($this->returnValue($clientMock));
        $entityProviderMock->process();

        $linkUser = new SK_EntityProvider_Streamate_Link_User();
        /** @var SK_User $user */
        $user = $linkUser->findModel($performer->getId());
        $profileFields = $user->getProfile()->getFields();
        $this->assertInstanceOf('SK_User', $user);
        $this->assertSame('TestPerformer', $user->getUsername());
        $this->assertSame(256, $profileFields->get('height'));
        $this->assertEquals(date('Y') - 23, $user->getBirthdate()->format('Y'));
        $this->assertSame(16, $profileFields->get('hair_color'));
        $this->assertSame(8, $profileFields->get('eye_color'));
        $this->assertSame(1, $profileFields->get('body_type'));
        $this->assertContains(65536, $profileFields->get('fetishes_'));
        $this->assertSame(256, $profileFields->get('ethnicity'));
        $this->assertContains(1, $profileFields->get('language'));
        $this->assertContains(131072, $profileFields->get('language'));
        $this->assertSame(SK_User::SEX_FEMALE, $user->getSex());
        $this->assertContains(SK_User::SEX_MALE, $profileFields->get('match_sex'));
        $this->assertContains('About text', $profileFields->get('general_description'));
        $this->assertContains('Expertise text', $profileFields->get('general_description'));
        $this->assertContains('Turn ons text', $profileFields->get('general_description'));
        $this->assertTrue($user->getOnline());

        $camShowPaging = $user->getCamShows();
        $camShow = $camShowPaging->getItem(0);
        $this->assertCount(1, $camShowPaging->getItems());
        $this->assertInstanceOf('SK_Entity_CamShow', $camShow);
        $this->assertEquals($user->getId(), $camShow->getUserId());
    }

    public function testProcessRemove() {
        $oldUser = SKTest_TH::createUser();
        SKTest_TH::createCamShow($oldUser);
        $linkUser = new SK_EntityProvider_Streamate_Link_User();
        $linkUser->linkModel(12345, $oldUser);

        $camShowPaging = $oldUser->getCamShows();
        $camShow = $camShowPaging->getItem(0);
        $this->assertCount(1, $camShowPaging->getItems());
        $this->assertInstanceOf('SK_Entity_CamShow', $camShow);
        $this->assertEquals($oldUser->getId(), $camShow->getUserId());

        $performer = $this->_getPerformer();
        $clientMock = $this->getMock('SKService_Streamate_Performers_Client', array('getPerformersList'));
        $clientMock->expects($this->any())->method('getPerformersList')->will($this->returnValue(array($performer)));

        $entityProviderMock = $this->getMock('SK_EntityProvider_Streamate_Streamate', array('_getClient'));
        $entityProviderMock->expects($this->any())->method('_getClient')->will($this->returnValue($clientMock));
        /** @var $entityProviderMock SK_EntityProvider_Streamate_Streamate */
        $entityProviderMock->process();

        /** @var SK_User $user */
        $user = $linkUser->findModel($performer->getId());
        $camShowPaging = $user->getCamShows();
        $camShow = $camShowPaging->getItem(0);
        $this->assertFalse($oldUser->getOnline());
        $this->assertCount(1, $camShowPaging->getItems());
        $this->assertInstanceOf('SK_Entity_CamShow', $camShow);
        $this->assertEquals($user->getId(), $camShow->getUserId());
        $this->assertTrue($user->getOnline());

        $entityProviderMock->process();

        $camShowPaging = $user->getCamShows();
        $camShow = $camShowPaging->getItem(0);
        $this->assertFalse($oldUser->getOnline());
        $this->assertCount(1, $camShowPaging->getItems());
        $this->assertInstanceOf('SK_Entity_CamShow', $camShow);
        $this->assertEquals($user->getId(), $camShow->getUserId());
        $this->assertTrue($user->getOnline());
    }

    /**
     * @return SKService_Streamate_Performers_Response_Performer
     */
    private function _getPerformer() {
        return new SKService_Streamate_Performers_Response_Performer(1234, 'TestPerformer', 'live', 67, 23, 'brown', 'green', 'slim', array('roleplay'),
            'mediterranean', array('en', 'es'), 'f', 'straight', null, array(), 'About text', 'Expertise text', 'Turn ons text', time());
    }
}
