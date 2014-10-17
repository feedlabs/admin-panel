<?php

class SK_Entertainment_Action_FriendRequestTest extends SKTest_TestCase {

    public function testExecute() {
        $entertainer = SKTest_TH::createUser();
        $target = SKTest_TH::createUser();
        $this->assertTrue($entertainer->getFriendRequestsSent()->isEmpty());
        $this->assertTrue($target->getFriendRequestsGot()->isEmpty());
        $action = new SK_Entertainment_Action_FriendRequest($target, new SK_Params(array('sourceUser' => $entertainer)), 100);

        $action->execute();

        $this->assertSame(1, $entertainer->getFriendRequestsSent()->getCount());
        $this->assertSame(1, $target->getFriendRequestsGot()->getCount());
    }

    public function testExecuteAlreadyFriends() {
        $entertainer = SKTest_TH::createUser();
        $target = SKTest_TH::createUser();
        $entertainer->getFriends()->add($target);
        $this->assertTrue($entertainer->getFriendRequestsSent()->isEmpty());
        $this->assertTrue($target->getFriendRequestsGot()->isEmpty());
        $action = new SK_Entertainment_Action_FriendRequest($target, new SK_Params(array('sourceUser' => $entertainer)), 100);

        $action->execute();

        $this->assertSame(0, $entertainer->getFriendRequestsSent()->getCount());
        $this->assertSame(0, $target->getFriendRequestsGot()->getCount());
    }

    public function testExecuteSameUser() {
        $target = SKTest_TH::createUser();
        $this->assertTrue($target->getFriendRequestsSent()->isEmpty());
        $this->assertTrue($target->getFriendRequestsGot()->isEmpty());
        $action = new SK_Entertainment_Action_FriendRequest($target, new SK_Params(array('sourceUser' => $target)), 100);

        $action->execute();

        $this->assertTrue($target->getFriendRequestsSent()->isEmpty());
        $this->assertTrue($target->getFriendRequestsGot()->isEmpty());
    }

    public function testExecuteBlockedUser() {
        $entertainer = SKTest_TH::createUser();
        $target = SKTest_TH::createUser();
        $target->getBlockings()->add($entertainer);
        $this->assertTrue($entertainer->getFriendRequestsSent()->isEmpty());
        $this->assertTrue($target->getFriendRequestsGot()->isEmpty());
        $action = new SK_Entertainment_Action_FriendRequest($target, new SK_Params(array('sourceUser' => $entertainer)), 100);

        $action->execute();

        $this->assertTrue($entertainer->getFriendRequestsSent()->isEmpty());
        $this->assertTrue($target->getFriendRequestsGot()->isEmpty());
    }
}
