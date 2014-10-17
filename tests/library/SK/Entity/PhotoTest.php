<?php

class SK_Entity_PhotoTest extends SKTest_TestCase {

    /** @var CM_File_Image */
    private $_testImage;

    public function setUp() {
        $this->_testImage = new CM_File_Image(DIR_TEST_DATA . 'img/test.jpg');
    }

    public function testConstruct() {
        $photoId = SKTest_TH::createPhoto()->getId();
        $photo = new SK_Entity_Photo($photoId);
        $this->assertInstanceOf('SK_Entity_Photo', $photo);

        try {
            new SK_Entity_Photo(12345);
            $this->fail('Can instantiate nonexistent photo');
        } catch (CM_Exception_Nonexistent $e) {
        }
    }

    public function testCreate() {
        $image = $this->_testImage;
        $user = SKTest_TH::createUser();
        $this->assertEquals(0, $user->getPhotos()->getCount());
        $this->assertSame(0, $user->getPhotos(SK_ModelAsset_Entity_PrivacyAbstract::NONE)->getCount());

        $photo = SK_Entity_Photo::create($image, $user);
        $this->assertEquals(1, $user->getPhotos()->getCount());
        $this->assertSame(1, $user->getPhotos(SK_ModelAsset_Entity_PrivacyAbstract::NONE)->getCount());

        $this->assertInstanceOf('SK_Entity_Photo', $photo);
        $this->assertGreaterThan(0, $photo->getId());
        $this->assertSame($image->getWidth(), $photo->getWidth());
        $this->assertSame($image->getHeight(), $photo->getHeight());
        $this->assertEquals($user, $photo->getUser());
        $this->assertSame($user->getId(), $photo->getUserId());
        $this->assertSameTime(time(), $photo->getCreated());
        $this->assertInternalType('int', $photo->getCreated());
        $this->assertSame(0, $photo->getIndex());
        $this->assertNull($photo->getDescription());
        $this->assertSame($image->getHash(), $photo->getHash());
        $this->assertFalse($photo->getAnimated());
        $this->assertSame(SK_ModelAsset_Entity_PrivacyAbstract::NONE, $photo->_get('privacy'));
        $this->assertNull($photo->getPopularStamp());

        $this->assertSame(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY, SK_Entity_Photo::create($image, $user, null, SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY)->getPrivacy()->get());
    }

    public function testIsViewable() {
        $user1 = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user1);
        $user2 = SKTest_TH::createUser();

        $photo->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        $this->assertTrue($photo->getPrivacy()->isViewable($user1));
        $this->assertTrue($photo->getPrivacy()->isViewable($user2));
        $this->assertTrue($photo->getPrivacy()->isViewable(null));

        $photo->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY);
        $this->assertTrue($photo->getPrivacy()->isViewable($user1));
        $this->assertFalse($photo->getPrivacy()->isViewable($user2));
        $this->assertFalse($photo->getPrivacy()->isViewable(null));

        $user1->getFriends()->add($user2);
        $this->assertTrue($photo->getPrivacy()->isViewable($user1));
        $this->assertTrue($photo->getPrivacy()->isViewable($user2));
        $this->assertFalse($photo->getPrivacy()->isViewable(null));
    }

    public function testSetGetPrivacy() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user);

        $photo->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        $this->assertEquals(SK_ModelAsset_Entity_PrivacyAbstract::NONE, $photo->getPrivacy()->get());

        $photo->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY);
        $this->assertEquals(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY, $photo->getPrivacy()->get());
    }

    public function testExists() {
        $hash = '12qwea34';
        CM_Db_Db::insert('sk_entity_photo', array('index', 'hash'), array(2, $hash));

        $this->assertTrue(SK_Entity_Photo::exists($hash));
        $this->assertFalse(SK_Entity_Photo::exists($hash . '2'));
    }

    public function testDelete() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user);

        $photo->getRating()->setScore($user, 1);
        $this->assertRow('sk_tmp_photo', array('photoId' => $photo->getId()));

        $photo->delete();
        try {
            $photo = new SK_Entity_Photo($photo->getId());
            $this->fail('Could instantiate deleted photo');
        } catch (CM_Exception $e) {
        }
        $this->assertNotRow('sk_tmp_photo', array('photoId' => $photo->getId()));
    }

    public function testGetImage() {
        $testImage = $this->_testImage;
        /** @var SK_Entity_Photo $photo */
        $photo = SK_Entity_Photo::create($testImage, SKTest_TH::createUser());
        $this->assertSame($photo->getImage()->getHash(), $testImage->getHash());
    }

    public function testRotate() {
        $orig = SKTest_TH::createPhoto();
        $photo = new SK_Entity_Photo($orig->getId());
        $photo->rotate(180);
        $this->assertEquals($orig->getHeight(), $photo->getHeight());
        $this->assertEquals($orig->getWidth(), $photo->getWidth());
        $photo->rotate(90);
        $this->assertEquals($orig->getHeight(), $photo->getWidth());
        $this->assertEquals($orig->getWidth(), $photo->getHeight());
    }

    public function testGetTags() {
        $photo = SKTest_TH::createPhoto();
        $this->assertSame(array(), $photo->getTags()->get());
        $photo->getTags()->set(array('foo'));
        $this->assertSame(array('foo'), $photo->getTags()->get());
    }

    public function testGetAnimated() {
        $user = SKTest_TH::createUser();
        $imageList = array(
            DIR_TEST_DATA . 'img/test.jpg'     => false,
            DIR_TEST_DATA . 'img/test.gif'     => false,
            DIR_TEST_DATA . 'img/animated.gif' => true,
        );
        foreach ($imageList as $path => $expected) {
            /** @var SK_Entity_Photo $photo */
            $photo = SK_Entity_Photo::create(new CM_File_Image($path), $user);
            $this->assertSame($expected, $photo->getAnimated());
        }
    }

    public function testGetImageFormat() {
        $user = SKTest_TH::createUser();
        /** @var SK_Entity_Photo $photo */
        $photo = SK_Entity_Photo::create(new CM_File_Image(DIR_TEST_DATA . 'img/test.jpg'), $user);
        $this->assertSame(CM_File_Image::FORMAT_JPEG, $photo->getImageFormat(SK_Entity_Photo::TYPE_FULL));
        $this->assertSame(CM_File_Image::FORMAT_JPEG, $photo->getImageFormat(SK_Entity_Photo::TYPE_VIEW));
        $this->assertSame(CM_File_Image::FORMAT_JPEG, $photo->getImageFormat(SK_Entity_Photo::TYPE_PREVIEW));
        $this->assertSame(CM_File_Image::FORMAT_JPEG, $photo->getImageFormat(SK_Entity_Photo::TYPE_THUMB));

        /** @var SK_Entity_Photo $photoAnimated */
        $photoAnimated = SK_Entity_Photo::create(new CM_File_Image(DIR_TEST_DATA . 'img/animated.gif'), $user);
        $this->assertSame(CM_File_Image::FORMAT_GIF, $photoAnimated->getImageFormat(SK_Entity_Photo::TYPE_FULL));
        $this->assertSame(CM_File_Image::FORMAT_GIF, $photoAnimated->getImageFormat(SK_Entity_Photo::TYPE_VIEW));
        $this->assertSame(CM_File_Image::FORMAT_JPEG, $photoAnimated->getImageFormat(SK_Entity_Photo::TYPE_PREVIEW));
        $this->assertSame(CM_File_Image::FORMAT_JPEG, $photoAnimated->getImageFormat(SK_Entity_Photo::TYPE_THUMB));
    }

    public function testGetSetIndex() {
        $photo = SKTest_TH::createPhoto();
        $this->assertSame(0, $photo->getIndex());
        $photo->setIndex(1);
        $this->assertSame(1, $photo->getIndex());
    }

    public function testGetSetDescription() {
        $photo = SK_Entity_Photo::create($this->_testImage, SKTest_TH::createUser(), 'foo');
        $this->assertSame('foo', $photo->getDescription());
        $photo->setDescription('bar');
        $this->assertSame('bar', $photo->getDescription());
        $photo->setDescription(null);
        $this->assertNull($photo->getDescription());
    }

    public function testGetSetPopular() {
        $photo = SKTest_TH::createPhoto();
        $this->assertNull($photo->getPopularStamp());
        $photo->setPopular(true);
        $this->assertSame(time(), $photo->getPopularStamp());
        $photo->setPopular(false);
        $this->assertNull($photo->getPopularStamp());
    }

    public function testGetUser() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user);
        $this->assertEquals($user, $photo->getUser());
    }
}
