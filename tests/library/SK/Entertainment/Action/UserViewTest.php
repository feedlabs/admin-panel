<?php

class SK_Entertainment_Action_UserViewTest extends SKTest_TestCase {

    public function tearDownAfter() {
        SKTest_TH::clearEnv();
    }

    public function testExecute() {
        $targetUser = SKTest_TH::createUser();
        $sourceUser = SKTest_TH::createUser();
        $action = new SK_Entertainment_Action_UserView($targetUser, new SK_Params(array('sourceUser' => $sourceUser)), 1);
        $this->assertTrue($targetUser->getViewHistory()->isEmpty());

        $action->execute();

        $this->assertEquals($sourceUser, $targetUser->getViewHistory()->getItem(0));
    }

    public function testExecuteSameUser() {
        $user = SKTest_TH::createUser();
        $action = new SK_Entertainment_Action_UserView($user, new SK_Params(array('sourceUser' => $user)), 1);
        $this->assertTrue($user->getViewHistory()->isEmpty());

        $action->execute();

        $this->assertTrue($user->getViewHistory()->isEmpty());
    }

    public function testExecuteBlockedUser() {
        $targetUser = SKTest_TH::createUser();
        $sourceUser = SKTest_TH::createUser();
        $targetUser->getBlockings()->add($sourceUser);
        $action = new SK_Entertainment_Action_UserView($targetUser, new SK_Params(array('sourceUser' => $sourceUser)), 1);
        $this->assertTrue($targetUser->getViewHistory()->isEmpty());

        $action->execute();

        $this->assertTrue($targetUser->getViewHistory()->isEmpty());
    }
}
