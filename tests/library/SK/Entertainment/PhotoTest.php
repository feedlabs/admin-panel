<?php

class SK_Entertainment_PhotoTest extends SKTest_TestCase {

    public function testCreateNormal() {
        $user = SKTest_TH::createUser();
        $user->setLanguage(SKTest_TH::createLanguage());
        $image = new CM_File_Image(DIR_TEST_DATA . 'img/test.jpg');
        $photo = SK_Entity_Photo::create($image, $user);
        $template = SK_Entertainment_UserTemplate::create($user);

        $entertainmentPhoto = SK_Entertainment_Photo::create($template, $photo);
        $this->assertFalse($entertainmentPhoto->getAnimated());
        $this->assertEquals($template, $entertainmentPhoto->getUserTemplate());
        $file = $entertainmentPhoto->getFile();
        $this->assertSame($entertainmentPhoto->getId() . '.' . CM_File_Image::getExtensionByFormat(CM_File_Image::FORMAT_JPEG), $file->getFileName());
        $this->assertTrue($file->getExists());
        $this->assertFalse($entertainmentPhoto->getImage()->isAnimated());
    }

    public function testCreateAnimated() {
        $user = SKTest_TH::createUser();
        $user->setLanguage(SKTest_TH::createLanguage());
        $image = new CM_File_Image(DIR_TEST_DATA . 'img/animated.gif');
        $photo = SK_Entity_Photo::create($image, $user);
        $template = SK_Entertainment_UserTemplate::create($user);

        $entertainmentPhoto = SK_Entertainment_Photo::create($template, $photo);
        $this->assertTrue($entertainmentPhoto->getAnimated());
        $this->assertSame($entertainmentPhoto->getId() . '.' .
            CM_File_Image::getExtensionByFormat(CM_File_Image::FORMAT_GIF), $entertainmentPhoto->getFile()->getFileName());
        $this->assertTrue($entertainmentPhoto->getImage()->isAnimated());
    }

    public function testDelete() {
        $user = SKTest_TH::createUser();
        $user->setLanguage(SKTest_TH::createLanguage());
        $template = SK_Entertainment_UserTemplate::create($user);
        $photo = SK_Entertainment_Photo::create($template, SKTest_TH::createPhoto());

        $file = $photo->getFile();
        $this->assertTrue($file->getExists());
        $photo->delete();
        $this->assertFalse($file->getExists());
    }
}
