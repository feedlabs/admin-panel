<?php

class SK_Entertainment_Action_UserThumbnailTest extends SKTest_TestCase {

    public function testExecute() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto();

        $this->assertNull($user->getThumbnailFile());

        $action = new SK_Entertainment_Action_UserThumbnail($user, new SK_Params(array('photo' => $photo)), time());
        $action->execute();

        $this->assertNotNull($user->getThumbnailFile());
    }
}
