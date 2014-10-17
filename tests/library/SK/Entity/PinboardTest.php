<?php

class SK_Entity_PinboardTest extends SKTest_TestCase {

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $board = SK_Entity_Pinboard::create('board 1', $user);

        $this->assertInstanceOf('SK_Entity_Pinboard', $board);
        $this->assertSame('board 1', $board->getName());
        $this->assertEquals($user, $board->getUser());
        $this->assertSameTime(time(), $board->_get('createStamp'));
        $this->assertSame(SK_ModelAsset_Entity_PrivacyAbstract::NONE, $board->_get('privacy'));

        $this->assertSame(SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY, SK_Entity_Pinboard::create('board 1', $user, SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY)->_get('privacy'));
    }

    public function testDelete() {
        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto();
        $video = SKTest_TH::createVideo();
        $board = SKTest_TH::createPinboard($user);

        $board->add($photo);
        $board->add($video);
        $board->getRating()->setScore($user, 1);

        $this->assertSame(2, $board->getPinList()->getCount());
        $this->assertRow('sk_tmp_pinboard', array('pinboardId' => $board->getId(), 'rating' => 1, 'ratingCount' => 1));

        $board->delete();

        $this->assertTrue($user->getPinboardList()->isEmpty());
        $this->assertNotRow('sk_pinboard_entity', array('entityId' => $photo->getId()));
        $this->assertNotRow('sk_pinboard_entity', array('entityId' => $video->getId()));
        $this->assertNotRow('sk_tmp_pinboard', array('pinboardId' => $board->getId()));
    }

    public function testGetUser() {
        $user = SKTest_TH::createUser();
        $pinboard = SKTest_TH::createPinboard($user);
        $this->assertEquals($user, $pinboard->getUser());
    }

    public function testGetSetPopular() {
        $pinboard = SKTest_TH::createPinboard();
        $this->assertNull($pinboard->getPopularStamp());
        $pinboard->setPopular(true);
        $this->assertSame(time(), $pinboard->getPopularStamp());
        $pinboard->setPopular(false);
        $this->assertNull($pinboard->getPopularStamp());
    }
}
