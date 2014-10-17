<?php

class SK_ModelAsset_Entity_PrivacyAbstractTest extends SKTest_TestCase {

    public static function setUpBeforeClass() {
    }

    public function testGet() {
        /** @var SK_Entity_Abstract[] $entities */
        $entities = array(SKTest_TH::createBlogpost(), SKTest_TH::createPhoto(), SKTest_TH::createVideo());
        foreach ($entities as $entity) {
            $this->assertEquals(SK_ModelAsset_Entity_PrivacyAbstract::NONE, $entity->getPrivacy()->get());
        }
    }

    public function testSet() {
        $video = SKTest_TH::createVideo();

        $video->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY);
        $this->assertEquals(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY, $video->getPrivacy()->get());
    }

    public function testIsViewable() {
        $user = SKTest_TH::createUser();
        $video = SKTest_TH::createVideo($user);
        $userFriend = SKTest_TH::createUser();
        $user->getFriends()->add($userFriend);
        $userOther = SKTest_TH::createUser();

        $video->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        $this->assertTrue($video->getPrivacy()->isViewable($user));
        $this->assertTrue($video->getPrivacy()->isViewable($userFriend));
        $this->assertTrue($video->getPrivacy()->isViewable($userOther));

        $video->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY);
        $this->assertTrue($video->getPrivacy()->isViewable($user));
        $this->assertTrue($video->getPrivacy()->isViewable($userFriend));
        $this->assertFalse($video->getPrivacy()->isViewable($userOther));

        $video->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::PERSONAL);
        $this->assertTrue($video->getPrivacy()->isViewable($user));
        $this->assertFalse($video->getPrivacy()->isViewable($userFriend));
        $this->assertFalse($video->getPrivacy()->isViewable($userOther));
    }

    public function testGetOptions() {
        $this->assertEquals(array(SK_ModelAsset_Entity_PrivacyAbstract::NONE,
            SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY,
            SK_ModelAsset_Entity_PrivacyAbstract::PERSONAL), SK_ModelAsset_Entity_PrivacyAbstract::getOptions());
    }
}
