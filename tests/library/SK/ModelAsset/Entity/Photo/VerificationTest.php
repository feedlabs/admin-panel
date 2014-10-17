<?php

class SK_ModelAsset_Entity_Photo_VerificationTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user);
        $photo->getVerification()->setPending();

        $this->assertSameTime(time(), $photo->getVerification()->getCreated());
        $this->assertNull($photo->getVerification()->getDecided());
        $this->assertTrue($photo->getVerification()->isPending());
        $this->assertFalse($photo->getVerification()->isApproved());
        $this->assertFalse($photo->getVerification()->isDeclined());
        $this->assertFalse($user->getRoles()->contains(SK_Role::VERIFIED_PHOTO));
    }

    public function testSetApproved() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user);
        $photo->getVerification()->setApproved();

        $this->assertSameTime(time(), $photo->getVerification()->getCreated());
        $this->assertSameTime(time(), $photo->getVerification()->getDecided());
        $this->assertFalse($photo->getVerification()->isPending());
        $this->assertTrue($photo->getVerification()->isApproved());
        $this->assertFalse($photo->getVerification()->isDeclined());
        $this->assertTrue($user->getRoles()->contains(SK_Role::VERIFIED_PHOTO));
    }

    public function testSetDeclined() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user);
        $photo->getVerification()->setDeclined();

        $this->assertSameTime(time(), $photo->getVerification()->getCreated());
        $this->assertSameTime(time(), $photo->getVerification()->getDecided());
        $this->assertFalse($photo->getVerification()->isPending());
        $this->assertFalse($photo->getVerification()->isApproved());
        $this->assertTrue($photo->getVerification()->isDeclined());
        $this->assertFalse($user->getRoles()->contains(SK_Role::VERIFIED_PHOTO));
    }

    public function testDelete() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user);
        $photo->getVerification()->setApproved();
        $photo->delete();

        $this->assertNotRow('sk_photo_verification', array('photoId' => $photo->getId()));
        $this->assertNotRow('cm_role', array('userId' => $user->getId(), 'role' => SK_Role::VERIFIED_PHOTO));
    }

    public function testCorrectUserRoleAfterDelete() {
        $user = SKTest_TH::createUser();
        $photo1 = SKTest_TH::createPhoto($user);
        $photo2 = SKTest_TH::createPhoto($user);

        $photo1->getVerification()->setApproved();
        $this->assertTrue($user->getRoles()->contains(SK_Role::VERIFIED_PHOTO));

        $photo2->getVerification()->setDeclined();
        $this->assertTrue($user->getRoles()->contains(SK_Role::VERIFIED_PHOTO));

        $photo2->delete();
        $this->assertTrue($user->getRoles()->contains(SK_Role::VERIFIED_PHOTO));

        $photo1->delete();
        SKTest_TH::reinstantiateModel($user);
        $this->assertFalse($user->getRoles()->contains(SK_Role::VERIFIED_PHOTO));
    }

    public function testHasVerification() {
        $photo = SKTest_TH::createPhoto();

        $this->assertFalse($photo->getVerification()->hasVerification());
        $photo->getVerification()->setPending();
        $this->assertTrue($photo->getVerification()->hasVerification());
    }
}
