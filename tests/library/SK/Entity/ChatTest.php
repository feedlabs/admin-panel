<?php

class SK_Entity_ChatTest extends SKTest_TestCase {

    public function testGetType() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $chat = SKTest_TH::createChat(array($user1, $user2));
        $this->assertEquals($chat->getType(), SK_Entity_Chat::getTypeStatic());
    }

    public function testGetUser() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $chat = SKTest_TH::createChat(array($user1, $user2));
        $this->assertEquals($user1, $chat->getUser());
    }

    public function testGetUsers() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $chat = $chat = SKTest_TH::createChat(array($user1, $user2));
        $users = $chat->getUsers();
        $this->assertInstanceOf('SK_Paging_User_Chat', $users);
        $this->assertEquals($user1, $users->getItem(0));
        $this->assertEquals($user2, $users->getItem(1));
    }

    public function testGetUsersPresentVideo() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $user3 = SKTest_TH::createUser();
        $chat = SKTest_TH::createChat(array($user1, $user2, $user3));
        $this->assertEquals(0, $chat->getUsersPresentVideo()->getCount());
        $streamChannel = SKTest_TH::createStreamChannelVideoChat($chat);
        $this->assertEquals(0, $chat->getUsersPresentVideo()->getCount());

        CM_Model_Stream_Publish::createStatic(array('streamChannel' => $streamChannel, 'user' => $user1, 'start' => 123123, 'allowedUntil' => 324234,
                                                    'key'           => '1_1'));
        $this->assertEquals(1, $chat->getUsersPresentVideo()->getCount());
        CM_Model_Stream_Publish::createStatic(array('streamChannel' => $streamChannel, 'user' => $user1, 'start' => 123123, 'allowedUntil' => 324234,
                                                    'key'           => '1_2'));
        $this->assertEquals(1, $chat->getUsersPresentVideo()->getCount());
        CM_Model_Stream_Publish::createStatic(array('streamChannel' => $streamChannel, 'user' => $user2, 'start' => 123123,
                                                    'allowedUntil'  => 324234, 'key' => '1_3'));
        $this->assertEquals(2, $chat->getUsersPresentVideo()->getCount());
        CM_Model_Stream_Subscribe::createStatic(array('streamChannel' => $streamChannel, 'user' => $user1, 'start' => 123123,
                                                      'allowedUntil'  => 324234,
                                                      'key'           => '1_4'));
        $this->assertEquals(2, $chat->getUsersPresentVideo()->getCount());
        CM_Model_Stream_Subscribe::createStatic(array('streamChannel' => $streamChannel, 'user' => $user3, 'start' => 123123,
                                                      'allowedUntil'  => 324234, 'key' => '1_5'));
        $this->assertEquals(3, $chat->getUsersPresentVideo()->getCount());

        $streamChannel->getStreamSubscribes()->getItem(0)->delete();
        $this->assertEquals(3, $chat->getUsersPresentVideo()->getCount());

        $streamChannel->getStreamSubscribes()->getItem(0)->delete();
        $this->assertEquals(2, $chat->getUsersPresentVideo()->getCount());

        $streamChannel->getStreamPublishs()->getItem(2)->delete();
        $this->assertEquals(1, $chat->getUsersPresentVideo()->getCount());
    }

    public function testHasVideoStreamChannels() {
        $chat = SKTest_TH::createChat();
        $this->assertFalse($chat->hasVideoStreamChannels());
        SKTest_TH::createStreamChannelVideoChat($chat);
        $this->assertTrue($chat->hasVideoStreamChannels());
    }

    public function testGetMessages() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $chat = SKTest_TH::createChat(array($user1, $user2));
        $this->assertInstanceOf('SK_Paging_ChatMessage_Chat', $chat->getMessages());
        $chat->getMessages()->add($user1, 'this is a test');
        $chat->getMessages()->add($user2, 'this is a test1');
        $chat->getMessages()->add($user1, 'this is a test');
        $this->assertEquals(3, $chat->getMessages()->getCount());
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $viewer = SKTest_TH::createUser();
        /** @var $chat SK_Entity_Chat */
        $chat = SK_Entity_Chat::createStatic(array('user' => $user, 'recipients' => array($viewer)));
        $chatId = $chat->getId();
        $this->assertRow('sk_chat', array('chatId' => $chatId), 1);
        $this->assertSame(2, CM_Db_Db::count('sk_chat_user', array('chatId' => $chatId)));
        $this->assertNotRow('sk_chat_message', array('chatId' => $chatId));
        $this->assertSame($user->getId(), (int) $chat->_get('userId'));
        $this->assertSameTime(time(), $chat->_get('createStamp'));
    }

    public function testLoadData() {
        try {
            new SK_Entity_Chat(1234567);
            $this->fail('it should not be possible to create a Chat entity with a fake chat id');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertContains('has no data', $e->getMessage());
        }
    }

    public function testGetVideoStreamChannels() {
        $chat = SKTest_TH::createChat();
        $this->assertSame(0, $chat->getVideoStreamChannels()->getCount());
        $streamChannel = SKTest_TH::createStreamChannelVideoChat($chat);
        SKTest_TH::createStreamChannelVideoChat();
        $this->assertSame(1, $chat->getVideoStreamChannels()->getCount());
        $this->assertTrue($chat->getVideoStreamChannels()->contains($streamChannel));
        $streamChannel->delete();
        $this->assertSame(0, $chat->getVideoStreamChannels()->getCount());
    }

    public function testDelete() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $chat = SKTest_TH::createChat(array($user1, $user2));
        $chat->getMessages()->add($user1, 'foo');

        $chat->delete();
        $this->assertNotRow('sk_chat', array('chatId' => $chat->getId()));
        $this->assertNotRow('sk_chat_user', array('chatId' => $chat->getId()));
        $this->assertNotRow('sk_chat_message', array('chatId' => $chat->getId()));
        try {
            SKTest_TH::reinstantiateModel($chat);
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertContains('has no data', $e->getMessage());
        }
    }

    public function testDeleteWithStreamchannels() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $chat = SKTest_TH::createChat(array($user1, $user2));
        $streamChannel = SKTest_TH::createStreamChannelVideoChat($chat);

        $chat->delete();
        $this->assertEquals($streamChannel, new SK_Model_StreamChannel_Video_Chat($streamChannel->getId()));
    }

    public function testDeleteOlder() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $chatId = SKTest_TH::createChat(array($user1, $user2))->getId();

        SK_Entity_Chat::deleteOlder(10);
        $this->assertSame(1, CM_Db_Db::count('sk_chat', array('chatId' => $chatId)));

        SKTest_TH::timeForward(20);
        SK_Entity_Chat::deleteOlder(10);
        $this->assertSame(0, CM_Db_Db::count('sk_chat', array('chatId' => $chatId)));
    }
}
