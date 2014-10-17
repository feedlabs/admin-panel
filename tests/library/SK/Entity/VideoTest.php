<?php

class SK_Entity_VideoTest extends SKTest_TestCase {

    public function testConstruct() {
        $user = SKTest_TH::createUser();
        $video = SK_Entity_Video::create($user, 'test', SK_ModelAsset_Entity_PrivacyAbstract::NONE);

        $video = new SK_Entity_Video($video->getId());
        $this->assertInstanceOf('SK_Entity_Video', $video);
        $this->assertSame('test', $video->getTitle());
        $this->assertNull($video->getDescription());
        $this->assertNull($video->getStudio());
        $this->assertNull($video->getThumbnailUrl());

        try {
            new SK_Entity_Video(12345);
            $this->fail('Can instantiate nonexistent video');
        } catch (CM_Exception_Nonexistent $e) {
        }
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $this->assertEquals(0, $user->getVideos()->getCount());
        $video = SKTest_TH::createVideo($user);
        $this->assertEquals(1, $user->getVideos()->getCount());

        $this->assertInstanceOf('SK_Entity_Video', $video);
        $this->assertGreaterThan(0, $video->getId());
        $this->assertEquals($user, $video->getUser());
        $this->assertSameTime(time(), $video->getCreated());
        $this->assertSame(SK_ModelAsset_Entity_PrivacyAbstract::NONE, $video->getPrivacy()->get());
    }

    public function testSetMetadata() {
        $video = SKTest_TH::createVideo();
        $video->setMetadata('Jumping Duck', 'Must see', 'url', 'CatVideos');
        $this->assertSame('Jumping Duck', $video->getTitle());
        $this->assertSame('Must see', $video->getDescription());
        $this->assertSame('url', $video->getThumbnailUrl());
        $this->assertSame('CatVideos', $video->getStudio());
        $video->setMetadata('Jumping Duck', null, null, null);
        $this->assertNull($video->getDescription());
        $this->assertNull($video->getThumbnailUrl());
        $this->assertNull($video->getStudio());
    }

    public function testDelete() {
        $video = SKTest_TH::createVideo();
        $scene = SKTest_TH::createVideoScene($video);
        SKTest_TH::createVideoSource($scene);

        $linkAdultCentroVideo = new SK_EntityProvider_AdultCentro_Link_User();
        $linkAdultCentroVideo->linkModel(12345, $video);

        $this->assertSame(2, CM_Db_Db::count('sk_videoScene', array('videoId' => $video->getId())));
        $this->assertSame(1, CM_Db_Db::count('sk_videoSource', array('videoSceneId' => $scene->getId())));
        $this->assertRow('sk_tmp_video', array('videoId' => $video->getId()));

        $video->delete();
        $this->assertSame(0, CM_Db_Db::count('sk_videoScene', array('videoId' => $video->getId())));
        $this->assertSame(0, CM_Db_Db::count('sk_videoSource', array('videoSceneId' => $scene->getId())));
        $this->assertNotRow('sk_tmp_video', array('videoId' => $video->getId()));
        $this->assertNotRow('sk_entityProvider_adultCentro_video', array('modelId' => $video->getId()));

        try {
            new SK_Entity_Video($video->getId());
            $this->fail('Could instantiate deleted video');
        } catch (CM_Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetSceneFirst() {
        $video = SK_Entity_Video::create(SKTest_TH::createUser(), 'Duck song', SK_ModelAsset_Entity_PrivacyAbstract::NONE);

        try {
            $video->getSceneFirst();
            $this->fail('Could get first video scene even if there is none');
        } catch (CM_Exception_Invalid $exception) {
            $this->assertTrue(true);
        }

        try {
            SKTest_TH::reinstantiateModel($video);
            $this->fail('Video without any scene should have been deleted');
        } catch (CM_Exception_Nonexistent $exception) {
            $this->assertTrue(true);
        }

        $video = SK_Entity_Video::create(SKTest_TH::createUser(), 'Duck song', SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        SKTest_TH::createVideoScene($video);
        $this->assertInstanceOf('SK_Model_Video_Scene', $video->getSceneFirst());
    }

    public function testGetDuration() {
        /** @var SK_Entity_Video $video */
        $video = SK_Entity_Video::create(SKTest_TH::createUser(), 'Duck song', SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        SK_Model_Video_Scene::createStatic(array('video' => $video, 'duration' => 1000));
        SK_Model_Video_Scene::createStatic(array('video' => $video, 'duration' => 2000));
        SK_Model_Video_Scene::createStatic(array('video' => $video, 'duration' => 3000));
        SKTest_TH::reinstantiateModel($video);
        $this->assertSame(6000, $video->getDuration());

        /** @var SK_Entity_Video $video */
        $video = SK_Entity_Video::create(SKTest_TH::createUser(), 'Duck song', SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        SK_Model_Video_Scene::createStatic(array('video' => $video));
        $this->assertSame(0, $video->getDuration());
    }

    public function testPurchase() {
        $user = SKTest_TH::createUser();
        /** @var SK_Entity_Video $video */
        $video = SK_Entity_Video::create(SKTest_TH::createUser(), 'Duck song', SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        SK_Model_Video_Scene::createStatic(array('video' => $video, 'duration' => 1800));
        SK_Model_Video_Scene::createStatic(array('video' => $video, 'duration' => 1800));
        SKTest_TH::reinstantiateModel($video);
        try {
            $video->purchase($user);
            $this->fail('Could purchase a video without sufficient funds');
        } catch (SK_Exception_InsufficientFunds $e) {
            $this->assertTrue(true);
        }
        $this->assertSame(10, $video->getPurchasePrice());
        $this->assertFalse($video->isPurchased($user));
        SKTest_TH::createCoinTransactionAdminGive($user, null, $video->getPurchasePrice());
        $video->purchase($user);
        $this->assertTrue($video->isPurchased($user));
        $this->assertEquals(array($video), $user->getVideoPurchasedList());
        $this->assertSame(0, $user->getCoins()->getBalance());
    }

    public function testGetSetPopular() {
        $video = SKTest_TH::createVideo();
        $this->assertNull($video->getPopularStamp());
        $video->setPopular(true);
        $this->assertSameTime(time(), $video->getPopularStamp());
        $video->setPopular(false);
        $this->assertNull($video->getPopularStamp());
    }

    public function testGetUser() {
        $user = SKTest_TH::createUser();
        $video = SKTest_TH::createVideo($user);
        $this->assertEquals($user, $video->getUser());
    }
}
