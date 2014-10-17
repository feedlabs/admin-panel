<?php

class SK_Entity_ProfileTest extends SKTest_TestCase {

    /**
     * @expectedException CM_Exception_Nonexistent
     */
    public function testConstructNoUser() {
        new SK_Entity_Profile(1);
    }

    /**
     * @expectedException CM_Exception_NotImplemented
     */
    public function testCreate() {
        SK_Entity_Profile::createStatic();
    }

    public function testGetUserId() {
        $profile = SKTest_TH::createUser()->getProfile();
        $this->assertSame($profile->getId(), $profile->getUserId());
    }

    public function testGetUser() {
        $user = SKTest_TH::createUser();
        $profile = $user->getProfile();
        $this->assertEquals($user, $profile->getUser());
    }

    /**
     * @expectedException CM_Exception_NotImplemented
     */
    public function testGetTableName() {
        $profile = SKTest_TH::createUser()->getProfile();
        $profile->getTableName();
    }
}
