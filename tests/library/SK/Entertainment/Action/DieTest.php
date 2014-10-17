<?php

class SK_Entertainment_Action_DieTest extends SKTest_TestCase {

    /**
     * @expectedException CM_Exception_Nonexistent
     */
    public function testExecute() {
        $user = SKTest_TH::createUser();
        $user->setLanguage(SKTest_TH::createLanguage());
        $user->setLocation(SKTest_TH::createLocation());
        $user = SKTest_TH::createUser();

        $this->assertTrue($user->getPhotos()->isEmpty());

        $action = new SK_Entertainment_Action_Die($user, new SK_Params(), time());
        $action->execute();

        SKTest_TH::reinstantiateModel($user);
    }
}
