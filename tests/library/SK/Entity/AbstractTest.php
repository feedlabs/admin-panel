<?php

class SK_Entity_AbstractTest extends SKTest_TestCase {

    public function testProfileFactory() {
        $profileId = SKTest_TH::createUser()->getProfile()->getId();
        $profile = CM_Model_Entity_Abstract::factory(SK_Entity_Profile::getTypeStatic(), $profileId);

        $this->assertInstanceOf('SK_Entity_Profile', $profile);
        $this->assertEquals($profileId, $profile->getId());
        $this->assertGreaterThan(0, $profileId);
    }

    public function testSetPopular() {
        $video = SKTest_TH::createVideo();

        $this->assertNull($video->getPopularStamp());
        $video->setPopular();
        $this->assertSameTime(time(), $video->getPopularStamp());

        $video->setPopular(false);
        $this->assertNull($video->getPopularStamp());
    }

    public function testCreateAggregation() {
        $user = SKTest_TH::createUser();

        $video = SKTest_TH::createVideo($user);
        SKTest_TH::createVideoScene($video);
        $video->getRating()->setScore(SKTest_TH::createUser(), 1);
        $video->getRating()->setScore(SKTest_TH::createUser(), 1);
        SKTest_TH::createComment('fooBar', $video, SKTest_TH::createUser());

        $photo = SKTest_TH::createPhoto($user);
        $photo->getRating()->setScore(SKTest_TH::createUser(), -1);
        SKTest_TH::createComment('fooBar', $photo, SKTest_TH::createUser());

        $blogpost = SKTest_TH::createBlogpost($user);
        $blogpost->getRating()->setScore(SKTest_TH::createUser(), -1);
        SKTest_TH::createComment('fooBar', $blogpost, SKTest_TH::createUser());

        $pinboard = SKTest_TH::createPinboard($user);
        $pinboard->getRating()->setScore(SKTest_TH::createUser(), 1);
        $pinboard->getRating()->setScore(SKTest_TH::createUser(), 1);
        $pinboard->getRating()->setScore(SKTest_TH::createUser(), -1);

        SK_Entity_Abstract::createAggregation();

        $this->assertRow('sk_tmp_user', array('userId' => $user->getId(), 'ratingCount' => 1, 'rating' => -1, 'commentCount' => 1));
        $this->assertRow('sk_tmp_video', array('videoId'  => $video->getId(), 'ratingCount' => 2, 'rating' => 2, 'commentCount' => 1,
                                               'duration' => $video->getDuration()));
        $this->assertRow('sk_tmp_photo', array('photoId' => $photo->getId(), 'ratingCount' => 1, 'rating' => -1, 'commentCount' => 1));
        $this->assertRow('sk_tmp_blogpost', array('blogpostId' => $blogpost->getId(), 'ratingCount' => 1, 'rating' => -1, 'commentCount' => 1));
        $this->assertRow('sk_tmp_pinboard', array('pinboardId' => $pinboard->getId(), 'ratingCount' => 3, 'rating' => 1));
    }
}
