<?php

class SK_Entertainment_Action_AbstractTest extends SKTest_TestCase {

    public function testExecuteUserPremium() {
        $sourceUser = SKTest_TH::createUser();
        $userPremium = SKTest_TH::createUserPremium();
        /** @var SK_Entertainment_Action_Abstract $action */
        $action = $this->getMockBuilder('SK_Entertainment_Action_Abstract')->setConstructorArgs(array($userPremium, new SK_Params(array('sourceUser' => $sourceUser)), 1))->setMethods(array('_execute', '_isAllowed'))->getMockForAbstractClass();
        $action->expects($this->never())->method('_execute');
        $action->expects($this->never())->method('_isAllowed');

        $action->execute();
    }

    public function testExecuteActionNotAllowed() {
        $sourceUser = SKTest_TH::createUser();
        $user = SKTest_TH::createUser();
        /** @var SK_Entertainment_Action_Abstract $action */
        $action = $this->getMockBuilder('SK_Entertainment_Action_Abstract')->setConstructorArgs(array($user, new SK_Params(array('sourceUser' => $sourceUser)), 1))->setMethods(array('_execute', '_isAllowed'))->getMockForAbstractClass();
        $action->expects($this->never())->method('_execute');
        $action->expects($this->once())->method('_isAllowed')->will($this->returnValue(false));

        $action->execute();
    }

    public function testExecuteEntertainmentAffiliation() {
        $usertemplate = SKTest_TH::createEntertainmentUserTemplate();
        $sourceUser = SKTest_TH::createUser();
        $usertemplate->getUserList()->add($sourceUser);
        $user = SKTest_TH::createUser();
        /** @var SK_Entertainment_Action_Abstract $action */
        $action = $this->getMockBuilder('SK_Entertainment_Action_Abstract')->setConstructorArgs(array($user, new SK_Params(array('sourceUser' => $sourceUser)), 1))->setMethods(array('_execute', '_isAllowed'))->getMockForAbstractClass();
        $action->expects($this->once())->method('_execute');
        $action->expects($this->once())->method('_isAllowed')->will($this->returnValue(true));

        $this->assertTrue(SK_Model_Affiliate::findByUserId($user->getId())->isEmpty());
        $action->execute();
        $affiliateList = SK_Model_Affiliate::findByUserId($user->getId());

        $affiliate = $affiliateList->getItem(0);
        $this->assertInstanceOf('SK_Model_Affiliate', $affiliate);
        $this->assertSame((string) $usertemplate->getId(), $affiliate->getCode());
    }
}
