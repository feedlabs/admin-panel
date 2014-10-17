<?php

class SK_User_DeleteJobTest extends SKTest_TestCase {

    public function testExecute() {
        $job = new SK_User_DeleteJob();
        $user = SKTest_TH::createUser();
        $job->run(array('user' => $user));
        try {
            SKTest_TH::reinstantiateModel($user);
            $this->fail('User not deleted');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }
}
