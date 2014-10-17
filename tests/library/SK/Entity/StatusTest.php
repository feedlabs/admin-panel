<?php

class SK_Entity_StatusTest extends SKTest_TestCase {

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $status = SKTest_TH::createStatus($user);

        $this->assertInstanceOf('SK_Entity_Status', $status);
        $this->assertGreaterThan(0, $status->getId());
        $this->assertEquals($user, $status->getUser());
        $this->assertSameTime(time(), $status->getCreated());
    }

    public function testConstruct() {
        $statusId = SKTest_TH::createStatus()->getId();
        $status = new SK_Entity_Status($statusId);
        $this->assertInstanceOf('SK_Entity_Status', $status);

        try {
            new SK_Entity_Status(12345);
            $this->fail('Can instantiate nonexistent status');
        } catch (CM_Exception_Nonexistent $e) {
        }
    }

    public function testDelete() {
        $user = SKTest_TH::createUser();
        $status = SKTest_TH::createStatus($user);

        $status->delete();
        try {
            new SK_Entity_Status($status->getId());
            $this->fail('Could instantiate deleted status');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }
    }

    public function testDeleteWithComment() {
        $user = SKTest_TH::createUser();
        $status = SKTest_TH::createStatus($user);

        $user2 = SKTest_TH::createUser();
        $comment = SK_Entity_Comment::create($user2, $status, 'foo');

        $status->delete();
        try {
            new SK_Entity_Comment($comment->getId());
            $this->fail('Could instantiate deleted status\' comment');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetPath() {
        $status = SKTest_TH::createStatus();
        $this->assertSame('/status?status=' . $status->getId(), $status->getPath());
    }
}
