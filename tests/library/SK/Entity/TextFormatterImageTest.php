<?php

class SK_Entity_TextFormatterImageTest extends SKTest_TestCase {

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $image = new CM_File_Image(DIR_TEST_DATA . '/img/test.jpg');
        /** @var SK_Entity_TextFormatterImage $textFormatterImage */
        $textFormatterImage = SK_Entity_TextFormatterImage::create($image, $user);

        $this->assertGreaterThan(0, $textFormatterImage->getId());
        $this->assertEquals($user, $textFormatterImage->getUser());
        $this->assertSameTime(time(), $textFormatterImage->_get('createStamp'));
    }

    public function testGetAnimated() {
        $user = SKTest_TH::createUser();
        $imageList = array(
            DIR_TEST_DATA . 'img/test.jpg'     => false,
            DIR_TEST_DATA . 'img/animated.gif' => true,
        );
        foreach ($imageList as $path => $expected) {
            /** @var SK_Entity_TextFormatterImage $textFormatterImage */
            $textFormatterImage = SK_Entity_TextFormatterImage::create(new CM_File_Image($path), $user);
            $this->assertSame($expected, $textFormatterImage->getAnimated());
        }
    }

    public function testGetImageFormat() {
        $user = SKTest_TH::createUser();
        /** @var SK_Entity_TextFormatterImage $textFormatterImage */
        $textFormatterImage = SK_Entity_TextFormatterImage::create(new CM_File_Image(DIR_TEST_DATA . '/img/test.jpg'), $user);
        $this->assertSame(CM_File_Image::FORMAT_JPEG, $textFormatterImage->getImageFormat());

        /** @var SK_Entity_TextFormatterImage $textFormatterImageAnimated */
        $textFormatterImageAnimated = SK_Entity_TextFormatterImage::create(new CM_File_Image(DIR_TEST_DATA . '/img/animated.gif'), $user);
        $this->assertSame(CM_File_Image::FORMAT_GIF, $textFormatterImageAnimated->getImageFormat());
    }
}
