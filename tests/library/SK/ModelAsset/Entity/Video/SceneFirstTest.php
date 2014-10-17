<?php

class SK_ModelAsset_Entity_Video_SceneFirstTest extends SKTest_TestCase {

    public function testGet() {
        $video = SK_Entity_Video::create(SKTest_TH::createUser(), 'Duck song', SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        $videoScene = SKTest_TH::createVideoScene($video);
        $videoSceneFirstAsset = new SK_ModelAsset_Entity_Video_SceneFirst($video);
        $this->assertEquals($videoScene, $videoSceneFirstAsset->get());
    }

    public function testGetNoVideoScene() {
        $video = SK_Entity_Video::create(SKTest_TH::createUser(), 'Duck song', SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        $videoSceneFirstAsset = new SK_ModelAsset_Entity_Video_SceneFirst($video);
        $this->assertNull($videoSceneFirstAsset->get());
    }
}
