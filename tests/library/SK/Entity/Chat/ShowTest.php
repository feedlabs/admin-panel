<?php

class SK_Entity_Chat_ShowTest extends SKTest_TestCase {

    public static function setUpBeforeClass() {
    }

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $this->assertEquals(0, $user->getChatShows()->getCount());
        /** @var SK_Entity_Chat_Show $chat */
        $chat = SK_Entity_Chat_Show::createStatic(array('user' => $user));
        $this->assertInstanceOf('SK_Entity_Chat_Show', $chat);
        $this->assertRow('sk_chat', array('chatId' => $chat->getId()));
        $this->assertRow('sk_chat_show', array('id' => $chat->getId()));
        $this->assertEquals(1, $user->getChatShows()->getCount());
        $this->assertEquals($user, $chat->getUser());
        $this->assertSameTime(time(), $chat->_get('createStamp'));
    }

    public function testGetStreamPublish() {
        $show = SKTest_TH::createChatShow();
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($show);
        try {
            $show->getStreamPublish();
            $this->fail();
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains(' has no StreamPublish.', $ex->getMessage());
        }
        $streamPublish = SKTest_TH::createStreamPublish($show->getUser(), $streamChannel);
        $this->assertEquals($streamPublish, $show->getStreamPublish());
    }

    public function testHasStreamPublish() {
        $show = SKTest_TH::createChatShow();
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($show);
        $this->assertFalse($show->hasStreamPublish());
        SKTest_TH::createStreamPublish($show->getUser(), $streamChannel);
        $this->assertTrue($show->hasStreamPublish());
    }

    public function testGetUserId() {
        $user = SKTest_TH::createUser();
        $chat = SKTest_TH::createChatShow($user);
        $this->assertEquals($user->getId(), $chat->getUserId());
    }

    public function testGetUsers() {
        $chat = SKTest_TH::createChatShow();
        $this->assertEquals(0, $chat->getUsers()->getCount());
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($chat);
        $this->assertEquals(0, $chat->getUsers()->getCount());
        $user = $chat->getUser();

        $user2 = SKTest_TH::createUser();
        $user2->getRoles()->add(SK_Role::ADMIN);

        $publish1 = CM_Model_Stream_Publish::createStatic(array('streamChannel' => $streamChannel, 'user' => $user, 'start' => 123123, 'allowedUntil' => 324234,
                                                    'key'           => '1_1'));
        $this->assertEquals(1, $chat->getUsers()->getCount());
        $subscribe1 = CM_Model_Stream_Subscribe::createStatic(array('streamChannel' => $streamChannel, 'user' => $user, 'start' => 123123, 'allowedUntil' => 324234,
                                                      'key'           => '1_4'));
        $this->assertEquals(1, $chat->getUsers()->getCount());
        $subscribe2 = CM_Model_Stream_Subscribe::createStatic(array('streamChannel' => $streamChannel, 'user' => $user2, 'start' => 123123,
                                                      'allowedUntil'  => 324234, 'key' => '1_5'));
        $this->assertEquals(2, $chat->getUsers()->getCount());

        $subscribe1->delete();
        $this->assertEquals(2, $chat->getUsers()->getCount());

        $subscribe2->delete();
        $this->assertEquals(1, $chat->getUsers()->getCount());

        $publish1->delete();
        $this->assertEquals(0, $chat->getUsers()->getCount());
    }

    public function testGetVideoStreamChannel() {
        $show = SKTest_TH::createChatShow();
        try {
            $show->getVideoStreamChannel();
            $this->fail();
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains(' has no StreamChannel.', $ex->getMessage());
        }
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($show);
        $this->assertEquals($streamChannel, $show->getVideoStreamChannel());
    }

    public function testGetVideoStreamChannels() {
        $show = SKTest_TH::createChatShow();
        $this->assertSame(0, $show->getVideoStreamChannels()->getCount());
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($show);
        SKTest_TH::createStreamChannelVideoShow();
        $this->assertSame(1, $show->getVideoStreamChannels()->getCount());
        $this->assertTrue($show->getVideoStreamChannels()->contains($streamChannel));
        $streamChannel->delete();
        $this->assertSame(0, $show->getVideoStreamChannels()->getCount());
    }

    public function testDelete() {
        $user = SKTest_TH::createUser();
        $chatShow = SKTest_TH::createChatShow($user);
        $this->assertEquals(1, $user->getChatShows()->getCount());

        $chatShow->delete();
        $this->assertNotRow('sk_chat', array('chatId' => $chatShow->getId()));
        $this->assertNotRow('sk_chat_show', array('id' => $chatShow->getId()));
        $this->assertEquals(0, $user->getChatShows()->getCount());
    }
}
