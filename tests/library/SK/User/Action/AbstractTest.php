<?php

class SK_User_Action_AbstractTest extends SKTest_TestCase {

    public function testConstructor() {
        $user = SKTest_TH::createUser();
        $params = new SK_Params(array('foo' => 'bar'));
        /** @var SK_User_Action_Abstract $action */
        $action = $this->getMockForAbstractClass('SK_User_Action_Abstract', array($user, $params, 100));
        $this->assertEquals($user, $action->getUser());
        $this->assertSame($params, $action->getParams());
        $this->assertSame(100, $action->getExecuteStamp());
    }

    public function testFactory() {
        $user = SKTest_TH::createUser();
        $params = new SK_Params(array('foo' => 'bar'));
        foreach (SK_User_Action_Abstract::getClassChildren() as $class) {
            $actionConstructed = new $class($user, $params, 100);
            $actionManufactured = SK_User_Action_Abstract::factory($user, $params, $actionConstructed->getType(), 100);
            $this->assertEquals($actionConstructed, $actionManufactured);
        }
    }

    public function testSchedule() {
        $schedule = $this->getMockBuilder('SK_User_Schedule_Abstract')->setMethods(array('getType'))->getMockForAbstractClass();
        $schedule->expects($this->any())->method('getType')->will($this->returnValue(42));
        /** @var SK_User_Schedule_Abstract $schedule */
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto();
        $params = new SK_Params(array(
            'photo' => $photo,
            'foo'   => 'bar'
        ));
        $action = $this->getMockBuilder('SK_User_Action_Abstract')->setConstructorArgs(array($user, $params, 100))
            ->setMethods(array('getType'))->getMockForAbstractClass();
        $action->expects($this->any())->method('getType')->will($this->returnValue(87));
        /** @var SK_User_Action_Abstract $action */
        $action->schedule($schedule);
        $values = array('userId'       => $user->getId(), 'actionType' => $action->getType(), 'executeStamp' => $action->getExecuteStamp(),
                        'scheduleType' => $schedule->getType(),
                        'data'         => CM_Params::encode(array('photo' => serialize($photo), 'foo' => serialize('bar')), true));
        $this->assertRow('sk_userAction', $values);
    }
}
