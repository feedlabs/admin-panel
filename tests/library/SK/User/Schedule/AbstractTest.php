<?php

class SK_User_Schedule_AbstractTest extends SKTest_TestCase {

    public function testExecute() {
        $user = SKTest_TH::createUser();
        $action = new SK_Entertainment_Action_FriendRequest($user, new SK_Params(), time());
        $schedule = $this->getMockBuilder('SK_User_Schedule_Abstract')->setMethods(array('_execute', 'getType'))->getMockForAbstractClass();
        $schedule->expects($this->once())->method('_execute')->with($user);
        $schedule->expects($this->any())->method('getType')->will($this->returnValue(999));
        $action->schedule($schedule);
        /** @var SK_User_Schedule_Abstract $schedule */
        $this->assertRow('sk_userAction');
        $schedule->execute($user);
        $this->assertNotRow('sk_userAction', '1');
    }
}
