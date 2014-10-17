<?php

class SK_Entity_ShowArchiveTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $show = SKTest_TH::createChatShow($user);
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($show);
        $streamPublish = SKTest_TH::createStreamPublish($user, $streamChannel);
        /** @var SK_Entity_ShowArchive $archive */
        $archive = SK_Entity_ShowArchive::createStatic(array('show' => $show));
        $this->assertInstanceOf('SK_Entity_ShowArchive', $archive);
        $this->assertSame($show->getId(), $archive->getId());
        $this->assertEquals($user, $archive->getUser());
        $this->assertSame($streamChannel->getId(), $archive->getStreamChannelArchiveId());
        $this->assertSameTime($streamPublish->getStart(), $archive->getCreated());

        $show = SKTest_TH::createChatShow();
        try {
            SK_Entity_ShowArchive::createStatic(array('show' => $show));
            $this->fail('ShowArchive created without StreamChannel.');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertTrue(true);
        }
        SKTest_TH::createStreamChannelVideoShow($show);
        try {
            SK_Entity_ShowArchive::createStatic(array('show' => $show));
            $this->fail('ShowArchive created without StreamPublish.');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertTrue(true);
        }
    }

    public function testGetUserIfExists() {
        $user = SKTest_TH::createUser();
        $showArchive = SKTest_TH::createShowArchive(null, $user);
        $this->assertEquals($user, $showArchive->getUserIfExists());
        $user->delete();
        $this->assertNull($showArchive->getUserIfExists());
    }

    public function testOnDelete() {
        $show = SKTest_TH::createChatShow();
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($show);
        $showArchive = SKTest_TH::createShowArchive($show);
        $streamChannelArchive = $showArchive->getStreamChannelArchive();
        $showArchive->delete();

        try {
            new SK_Entity_ShowArchive($showArchive->getId());
            $this->fail('ShowArchive not deleted');
        } catch (CM_Exception_Nonexistent $ex) {
        }
        try {
            new CM_Model_StreamChannelArchive_Video($streamChannelArchive->getId());
            $this->fail('StreamChannel not deleted');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }
}
