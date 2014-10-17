<?php

class SK_EntityProvider_AdultCentro_Link_VideoTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testFindModel() {
        $video = SKTest_TH::createVideo();
        CM_Db_Db::insert('sk_entityProvider_adultCentro_video', array('modelId' => $video->getId(), 'providerId' => 666));
        $linkVideo = new SK_EntityProvider_AdultCentro_Link_Video();
        $this->assertEquals($video, $linkVideo->findModel(666));

        $video->delete();
        $this->assertNull($linkVideo->findModel(666));
        $this->assertNull($linkVideo->findModel(999));
    }

    public function testLinkModel() {
        $video = SKTest_TH::createVideo();
        $linkVideo = new SK_EntityProvider_AdultCentro_Link_Video();
        $this->assertNull($linkVideo->findModel(20));
        $linkVideo->linkModel(20, $video);
        $this->assertEquals($video, $linkVideo->findModel(20));
    }

    public function testUnlinkModel() {
        $video = SKTest_TH::createVideo();
        CM_Db_Db::insert('sk_entityProvider_adultCentro_video', array('modelId' => $video->getId(), 'providerId' => 90));
        $linkVideo = new SK_EntityProvider_AdultCentro_Link_Video();
        $this->assertEquals($video, $linkVideo->findModel(90));
        $linkVideo->unlinkModel($video);
        $this->assertNull($linkVideo->findModel(90));
    }
}
