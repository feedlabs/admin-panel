<?php

class SK_Entertainment_Action_PhotoUploadTest extends SKTest_TestCase {

    public function testExecute() {
        $user = SKTest_TH::createUser();
        $user->setLanguage(SKTest_TH::createLanguage());
        $user->setLocation(SKTest_TH::createLocation());
        $template = SK_Entertainment_UserTemplate::create($user);
        $photo = SK_Entertainment_Photo::create($template, SKTest_TH::createPhoto($user));
        $user = SKTest_TH::createUser();

        $this->assertTrue($user->getPhotos()->isEmpty());
        $this->assertFalse($user->getOnline());

        $action = new SK_Entertainment_Action_PhotoUpload($user, new SK_Params(array('photo' => $photo)), time());
        $action->execute();

        $this->assertTrue($user->getOnline());
        $this->assertSame(1, $user->getPhotos()->getCount());
    }
}
