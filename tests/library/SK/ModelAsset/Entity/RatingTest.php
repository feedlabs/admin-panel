<?php

class SK_ModelAsset_Entity_RatingTest extends SKTest_TestCase {

    public function testSetScore() {
        $photo = SKTest_TH::createPhoto();
        $user = SKTest_TH::createUser();

        $this->assertNull($photo->getRating()->getScore($user));
        $this->assertTrue($photo->getRating()->getUserLikeList()->isEmpty());
        $photo->getRating()->setScore($user, 1);
        $this->assertEquals(1, $photo->getRating()->getScore($user));
        $this->assertEquals(array($user), $photo->getRating()->getUserLikeList());
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage Invalid score
     */
    public function testSetScoreInvalid() {
        $photo = SKTest_TH::createPhoto();
        $user = SKTest_TH::createUser();
        $photo->getRating()->setScore($user, 2);
    }

    /**
     * @expectedException CM_Exception_NotAllowed
     * @expectedExceptionMessage Cannot rate private entity
     */
    public function testSetScorePrivateEntityFriendsOnly() {
        $photo = SKTest_TH::createPhoto();
        $photo->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY);
        $user = SKTest_TH::createUser();
        $photo->getRating()->setScore($user, 1);
    }

    /**
     * @expectedException CM_Exception_NotAllowed
     * @expectedExceptionMessage Cannot rate private entity
     */
    public function testSetScorePrivateEntityPersonal() {
        $photo = SKTest_TH::createPhoto();
        $photo->getPrivacy()->set(SK_ModelAsset_Entity_PrivacyAbstract::PERSONAL);
        $user = SKTest_TH::createUser();
        $photo->getRating()->setScore($user, 1);
    }

    /**
     * @expectedException CM_Exception_NotAllowed
     * @expectedExceptionMessage Blocked user
     */
    public function testSetScoreBlocked() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user1);
        $user1->getBlockings()->add($user2);
        $photo->getRating()->setScore($user2, 1);
    }

    public function testEntityHasRating() {
        $user = SKTest_TH::createUser();
        foreach (array(SKTest_TH::createPhoto(), SKTest_TH::createBlogpost(), SKTest_TH::createVideo(), SKTest_TH::createStatus(),
                     SKTest_TH::createPinboard()) as $entity) {
            $entity->getRating()->setScore($user, 1);
        }
        foreach (array(SKTest_TH::createChat(), SKTest_TH::createComment(''), SKTest_TH::createConversation(),
                     SKTest_TH::createUser()->getProfile()) as $entity) {
            try {
                $entity->getRating()->setScore($user, 1);
                $this->fail('Could rate an `' . get_class($entity) . '`');
            } catch (CM_Exception $e) {
                $this->assertContains('No such asset `SK_ModelAsset_Entity_Rating`', $e->getMessage());
            }
        }
    }

    public function testDeleteScore() {
        $photo = SKTest_TH::createPhoto();
        $user = SKTest_TH::createUser();

        $photo->getRating()->setScore($user, 1);
        $this->assertEquals(1, $photo->getRating()->getScore($user));
        $this->assertEquals(array($user), $photo->getRating()->getUserLikeList());
        $photo->getRating()->deleteScore($user);
        $this->assertNull($photo->getRating()->getScore($user));
        $this->assertTrue($photo->getRating()->getUserLikeList()->isEmpty());
    }

    public function testGetValues() {
        $profile1 = SKTest_TH::createUser()->getProfile();
        $profile2 = SKTest_TH::createUser()->getProfile();
        $profile3 = SKTest_TH::createUser()->getProfile();
        $photo = SKTest_TH::createPhoto();

        $photo->getRating()->setScore($profile1->getUser(), -1);
        $photo->getRating()->setScore($profile2->getUser(), 1);
        SKTest_TH::timeForward(10);
        $photo->getRating()->setScore($profile3->getUser(), 1);
        $this->assertEquals(1, $photo->getRating()->getSum());
        $this->assertEquals(3, $photo->getRating()->getCount());
        $this->assertEquals(1, $photo->getRating()->getDislikes());
        $this->assertEquals(2, $photo->getRating()->getLikes());
        $this->assertEquals(array($profile3->getUser(), $profile2->getUser()), $photo->getRating()->getUserLikeList());
    }

    public function testDelete() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto();

        $photo->getRating()->setScore($user, 1);
        $this->assertEquals(1, $photo->getRating()->getScore($user));
        $this->assertEquals(array($user), $photo->getRating()->getUserLikeList());
        $photo->delete();
        $this->assertNotRow('sk_rating', array('userId' => $user->getId(), 'entityType' => SK_Entity_Photo::getTypeStatic()));
        $this->assertTrue($photo->getRating()->getUserLikeList()->isEmpty());
    }

    public function testDeleteAll() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto();

        $photo->getRating()->setScore($user, 1);
        $this->assertEquals(1, $photo->getRating()->getCount());
        $this->assertEquals(array($user), $photo->getRating()->getUserLikeList());

        $user->delete();
        $photo->_change();
        $this->assertEquals(0, $photo->getRating()->getCount());
        $this->assertTrue($photo->getRating()->getUserLikeList()->isEmpty());
    }

    public function testCanRate() {
        $user1 = SKTest_TH::createUser();
        $photo1 = SKTest_TH::createPhoto($user1);
        $user2 = SKTest_TH::createUser();

        $this->assertTrue($photo1->getRating()->canRate($user2));
        $this->assertTrue($photo1->getRating()->canRate($user1));
    }
}
