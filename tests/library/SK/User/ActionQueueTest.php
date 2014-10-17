<?php

class SK_User_ActionQueueTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testFlush() {
        $schedule = $this->getMockBuilder('SK_User_Schedule_Abstract')->setMethods(array('getType'))->getMockForAbstractClass();
        $schedule->expects($this->any())->method('getType')->will($this->returnValue(98));
        /** @var SK_User_Schedule_Abstract $schedule */
        $userFlushed = SKTest_TH::createUser();
        $action = new SK_Entertainment_Action_FriendRequest($userFlushed, new SK_Params(array('sourceUser' => SKTest_TH::createUser())), time());
        $action->schedule($schedule);
        $action = new SK_Entertainment_Action_FriendRequest($userFlushed, new SK_Params(array('sourceUser' => SKTest_TH::createUser())), time());
        $action->schedule($schedule);
        $user = SKTest_TH::createUser();
        $action = new SK_Entertainment_Action_FriendRequest($user, new SK_Params(array('sourceUser' => SKTest_TH::createUser())), time());
        $action->schedule($schedule);
        $this->assertRow('sk_userAction', array('userId' => $userFlushed->getId()), 2);
        $this->assertRow('sk_userAction', array('userId' => $user->getId()));

        SK_User_ActionQueue::flush($userFlushed, $schedule);

        $this->assertNotRow('sk_userAction', array('userId' => $userFlushed->getId()));
        $this->assertRow('sk_userAction', array('userId' => $user->getId()));
    }

    public function testProcessActions() {
        $schedule = $this->getMockBuilder('SK_User_Schedule_Abstract')->setMethods(array('getType'))->getMockForAbstractClass();
        $schedule->expects($this->any())->method('getType')->will($this->returnValue(98));
        $maxExecuteStamp = time();
        $targetUser = SKTest_TH::createUser();
        $sourceUser1 = SKTest_TH::createUser();
        $sourceUser2 = SKTest_TH::createUser();
        $action = new SK_Entertainment_Action_FriendRequest($targetUser, new SK_Params(array('sourceUser' => $sourceUser1)), $maxExecuteStamp);
        $action->schedule($schedule);
        $action = new SK_Entertainment_Action_FriendRequest($targetUser, new SK_Params(array('sourceUser' => $sourceUser2)), $maxExecuteStamp);
        $action->schedule($schedule);
        SKTest_TH::timeForward(100);
        $action = new SK_Entertainment_Action_FriendRequest($targetUser, new SK_Params(array('sourceUser' => SKTest_TH::createUser())), time());
        $action->schedule($schedule);
        $this->assertTrue($sourceUser1->getFriendRequestsSent()->isEmpty());
        $this->assertTrue($sourceUser2->getFriendRequestsSent()->isEmpty());
        $this->assertTrue($targetUser->getFriendRequestsGot()->isEmpty());

        SK_User_ActionQueue::processActions($maxExecuteStamp);

        $this->assertSame(1, $sourceUser1->getFriendRequestsSent()->getCount());
        $this->assertSame(1, $sourceUser2->getFriendRequestsSent()->getCount());
        $this->assertSame(2, $targetUser->getFriendRequestsGot()->getCount());
        $this->assertRow('sk_userAction');
    }

    public function testProcessActionsNonexistentParams() {
        $schedule = $this->getMockBuilder('SK_User_Schedule_Abstract')->setMethods(array('getType'))->getMockForAbstractClass();
        $schedule->expects($this->any())->method('getType')->will($this->returnValue(98));
        /** @var SK_User_Schedule_Abstract $schedule */
        $targetUser = SKTest_TH::createUser();
        $sourceUser = SKTest_TH::createUser();
        $nonExistentUser = SKTest_TH::createUser();
        $nonExistentUser->delete();
        $action = new SK_Entertainment_Action_FriendRequest($targetUser, new SK_Params(array('sourceUser' => $nonExistentUser)), time());
        $action->schedule($schedule);
        $this->assertTrue($sourceUser->getFriendRequestsSent()->isEmpty());
        $this->assertTrue($targetUser->getFriendRequestsGot()->isEmpty());

        SKTest_TH::timeForward(100);
        SK_User_ActionQueue::processActions(time());

        $this->assertTrue($sourceUser->getFriendRequestsSent()->isEmpty());
        $this->assertTrue($targetUser->getFriendRequestsGot()->isEmpty());
        $this->assertNotRow('sk_userAction', null);
    }

    public function testProcessActionNonexistentTargetUser() {
        $schedule = $this->getMockBuilder('SK_User_Schedule_Abstract')->setMethods(array('getType'))->getMockForAbstractClass();
        $schedule->expects($this->any())->method('getType')->will($this->returnValue(98));
        /** @var SK_User_Schedule_Abstract $schedule */
        $targetUser = SKTest_TH::createUser();
        $sourceUser = SKTest_TH::createUser();
        $targetUser->delete();
        $action = new SK_Entertainment_Action_FriendRequest($targetUser, new SK_Params(array('sourceUser' => SKTest_TH::createUser())), time());
        $action->schedule($schedule);
        $this->assertTrue($sourceUser->getFriendRequestsSent()->isEmpty());
        $this->assertTrue($targetUser->getFriendRequestsGot()->isEmpty());

        SKTest_TH::timeForward(100);
        SK_User_ActionQueue::processActions(time());

        $this->assertTrue($sourceUser->getFriendRequestsSent()->isEmpty());
        $this->assertTrue($targetUser->getFriendRequestsGot()->isEmpty());
        $this->assertNotRow('sk_userAction', null);
    }
}
