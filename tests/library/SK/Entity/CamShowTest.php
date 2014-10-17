<?php

class SK_Entity_CamShowTest extends SKTest_TestCase {

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $performerName = 'foo';
        $privacy = SK_ModelAsset_Entity_PrivacyAbstract::NONE;
        $camShow = SK_Entity_CamShow::create($user, $performerName, $privacy);
        $this->assertInstanceOf('SK_Entity_CamShow', $camShow);
        $this->assertEquals($user, $camShow->getUser());
        $this->assertSame($performerName, $camShow->getPerformerName());
        $this->assertSameTime(new \DateTime(), $camShow->getCreated());
        $this->assertNull($camShow->getLastOnline());
        $this->assertSame(SK_ModelAsset_Entity_PrivacyAbstract::NONE, $camShow->getPrivacy()->get());
    }

    public function testConstruct() {
        $camShowId = SKTest_TH::createCamShow()->getId();
        $camShow = new SK_Entity_CamShow($camShowId);
        $this->assertInstanceOf('SK_Entity_CamShow', $camShow);
    }

    /**
     * @expectedException CM_Exception_Nonexistent
     */
    public function testConstructWrong() {
        new SK_Entity_CamShow(12345);
    }
}
