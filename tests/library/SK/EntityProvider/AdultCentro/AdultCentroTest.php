<?php

class SK_EntityProvider_AdultCentro_AdultCentroTest extends SKTest_TestCase {

    public function setUp() {
        CM_Config::get()->CM_Model_Abstract->types[SK_EntityProvider_AdultCentro_AdultCentro::getTypeStatic()] = 'SK_EntityProvider_AdultCentro_AdultCentro';
        SK_EntityProvider_AdultCentro_AdultCentro::createStatic(array('name' => 'AdultCentro', 'processInterval' => 500));

        $country = CM_Model_Location::createCountry('United States', 'US');
        $state = CM_Model_Location::createState($country, 'NotExisting');
        foreach (array('New York', 'Los Angeles', 'Washington', 'San Francisco') as $city) {
            CM_Model_Location::createCity($state, $city, rand(0, 100), rand(0, 100));
        }
    }

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testProcess() {
        $video = SKTest_TH::createVideo();
        $linkVideo = new SK_EntityProvider_AdultCentro_Link_Video();
        $linkUser = new SK_EntityProvider_AdultCentro_Link_User();
        $videoId = $video->getId();
        $linkVideo->linkModel(666, $video);
        $dvd = new SKService_AdultCentro_Response_Dvd(1234, 'test', 'url', 'description', 'studio');
        $scene = new SKService_AdultCentro_Response_Scene(12345, 10000, 'test.jpg', 'test', array('1test', '2test', '1test'));
        $dvd->addScene($scene);
        $scene->addSource(new SKService_AdultCentro_Response_Source('test.com', 420, 0.41, true, 'flashVars'));

        $scene->addActor(new SKService_AdultCentro_Response_Actor(999, 'John Doe', SKService_AdultCentro_Response_Actor::SEX_MALE));
        $scene->addActor(new SKService_AdultCentro_Response_Actor(997, 'Derpina', SKService_AdultCentro_Response_Actor::SEX_SHEMALE));
        $dvd->addScene(new SKService_AdultCentro_Response_Scene(12345, 10000, 'test.jpg', 'test', array('1test', '3test', '4test')));

        $marketMock = $this->getMock('SKService_AdultCentro_Client_Market', array('getDvd', 'getHistoryList'));
        $marketMock->expects($this->any())->method('getDvd')->will($this->returnValue($dvd));
        $marketMock->expects($this->any())->method('getHistoryList')->will($this->returnValue(array('published' => array(1234),
                                                                                                    'deleted'   => array(666))));
        $entityProvider = new SK_EntityProvider_AdultCentro_AdultCentro($marketMock);
        $entityProvider->process();

        $adultCentroVideo = $linkVideo->findModel(1234);
        $adultCentroVideoScene = $adultCentroVideo->getSceneFirst();
        $adultCentroUser = $linkUser->findModel(999);
        $this->assertInstanceOf('SK_Entity_Video', $adultCentroVideo);
        $this->assertSame('test', $adultCentroVideo->getTitle());
        $this->assertSame('studio', $adultCentroVideo->getStudio());
        $this->assertSame('description', $adultCentroVideo->getDescription());
        $this->assertSame('url', $adultCentroVideo->getThumbnailUrl());
        $this->assertSame(array('1test', '2test', '3test', '4test', 'derpina', 'john doe'), $adultCentroVideo->getTags()->get());

        $this->assertInstanceOf('SK_Model_Video_Scene', $adultCentroVideoScene);
        $this->assertSame(10000, $adultCentroVideoScene->getDuration());
        $this->assertSame('test.jpg', $adultCentroVideoScene->getThumbnailUrl());
        $this->assertSame('test', $adultCentroVideoScene->getDescription());

        $this->assertSame('test.com', $adultCentroVideoScene->getSourceFirst()->getSrc());
        $this->assertSame(420, $adultCentroVideoScene->getSourceFirst()->getHeight());
        $this->assertSame(0.41, $adultCentroVideoScene->getSourceFirst()->getRatio());
        $this->assertTrue($adultCentroVideoScene->getSourceFirst()->getIsPreview());
        $this->assertSame('flashVars', $adultCentroVideoScene->getSourceFirst()->getFlashvars());

        $this->assertInstanceOf('SK_User', $adultCentroUser);
        $this->assertSame(SK_User::SEX_MALE, $adultCentroUser->getSex());
        $this->assertSame('John_Doe', $adultCentroUser->getUsername());
        $this->assertEquals($adultCentroUser, $adultCentroVideo->getUser());
        $this->assertNull($adultCentroUser->getThumbnailFile());
        $this->assertTrue(in_array($adultCentroVideo->getUser()->getLocation()->getName(), array('New York', 'Los Angeles', 'Washington',
            'San Francisco')));
        $this->assertTrue($adultCentroUser->getRoles()->contains(SK_Role::ENTITYPROVIDER));

        try {
            new SK_Entity_Video($videoId);
            $this->fail('Could initialize video which should`t be there.');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }

        $this->assertNull($linkVideo->findModel(666));
    }
}
