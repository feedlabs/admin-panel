<?php

class SK_ModelAsset_Entity_Photo_PrivacyTest extends SKTest_TestCase {

    public function testSet() {
        $user = SKTest_TH::createUser();
        $photo1 = SKTest_TH::createPhoto($user);
        $photo2 = SKTest_TH::createPhoto($user);
        $photo2->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY);
        $this->assertSame(1, $user->getPhotos(SK_ModelAsset_Entity_PrivacyAbstract::NONE)->getCount());
        $this->assertSame(1, $user->getPhotos(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY)->getCount());
        $photo2->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        $this->assertSame(2, $user->getPhotos(SK_ModelAsset_Entity_PrivacyAbstract::NONE)->getCount());
        $this->assertSame(0, $user->getPhotos(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY)->getCount());
    }
}
