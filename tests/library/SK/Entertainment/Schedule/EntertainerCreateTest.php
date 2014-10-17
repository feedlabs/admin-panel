<?php

class SK_Entertainment_Schedule_EntertainerCreateTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testExecute() {
        $user = SKTest_TH::createUser();
        $user->setLanguage(SKTest_TH::createLanguage());
        $user->setLocation(SKTest_TH::createLocation());
        $template = SK_Entertainment_UserTemplate::create($user);
        SK_Entertainment_Photo::create($template, SKTest_TH::createPhoto($user));
        SK_Entertainment_Photo::create($template, SKTest_TH::createPhoto($user));
        SK_Entertainment_Photo::create($template, SKTest_TH::createPhoto($user));
        $user = SKTest_TH::createUser(SK_User::SEX_FEMALE);
        $template->getUserList()->add($user);

        $this->assertSame(0, SK_User_ActionQueue::getCount());
        $this->assertTrue($user->getPhotos()->isEmpty());

        $schedule = new SK_Entertainment_Schedule_EntertainerCreate();
        $schedule->execute($user);

        $this->assertSame(1, $user->getPhotos()->getCount());
        $this->assertSame(3, SK_User_ActionQueue::getCount());
    }

    public function testExecuteInsufficientPhotos() {
        $user = SKTest_TH::createUser();
        $user->setLanguage(SKTest_TH::createLanguage());
        $user->setLocation(SKTest_TH::createLocation());
        $template = SK_Entertainment_UserTemplate::create($user);
        $user = SKTest_TH::createUser(SK_User::SEX_FEMALE);
        $template->getUserList()->add($user);

        $this->assertSame(0, SK_User_ActionQueue::getCount());
        $this->assertTrue($user->getPhotos()->isEmpty());

        $schedule = new SK_Entertainment_Schedule_EntertainerCreate();
        try {
            $schedule->execute($user);
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('Usertemplate `' . $template->getId() . '` has no photos.', $ex->getMessage());
        }
    }
}
