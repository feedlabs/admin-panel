<?php

class SK_Model_StreamChannel_Video_ChatTest extends SKTest_TestCase {

    public static function setUpBeforeClass() {
        CM_Config::get()->CM_Stream_Video->servers = array(1 => array('publicHost' => 'wowza1.example.dev', 'privateIp' => '10.0.3.108'));
    }

    public function testCreate() {
        $params = CM_Params::factory(array('chatId' => SKTest_TH::createChat()->getId()), false);
        /** @var SK_Model_StreamChannel_Video_Chat $streamChannel */
        $streamChannel = SK_Model_StreamChannel_Video_Chat::createStatic(array(
            'key'            => '1_1',
            'price'          => 123.23,
            'width'          => 720,
            'height'         => 1028,
            'serverId'       => 1,
            'thumbnailCount' => 0,
            'params'         => $params,
            'adapterType'    => CM_Stream_Adapter_Video_Wowza::getTypeStatic(),
        ));
        $this->assertInstanceOf('SK_Model_StreamChannel_Video_Chat', $streamChannel);
        $this->assertRow('cm_streamChannel', array('id'   => $streamChannel->getId(), 'key' => '1_1',
                                                   'type' => SK_Model_StreamChannel_Video_Chat::getTypeStatic()));
        $this->assertRow('sk_chat_streamChannel', array('channelId' => $streamChannel->getId()));
        $this->assertSame('10.0.3.108', $streamChannel->getPrivateHost());
        $this->assertSame('wowza1.example.dev', $streamChannel->getPublicHost());
        $this->assertSame(720, $streamChannel->getWidth());
        $this->assertSame(1028, $streamChannel->getHeight());
    }

    public function testGetChat() {
        $chat = SKTest_TH::createChat();
        $streamChannel = SKTest_TH::createStreamChannelVideoChat($chat);
        $this->assertEquals($chat, $streamChannel->getChat());
    }

    public function testDelete() {
        $streamChannel = SKTest_TH::createStreamChannelVideoChat();
        $streamChannel->delete();
        $this->assertNotRow('cm_streamChannel', array('id' => $streamChannel->getId()));
        $this->assertNotRow('cm_streamChannel_video', array('id' => $streamChannel->getId()));
        $this->assertNotRow('sk_chat_streamChannel', array('channelId' => $streamChannel->getId()));
    }

    public function testOnUnpublish() {
        $user = SKTest_TH::createUser();
        $chat = SKTest_TH::createChat(array($user));
        $streamChannel = SKTest_TH::createStreamChannelVideoChat($chat);
        $streamPublish = SKTest_TH::createStreamPublish($user, $streamChannel);
        try {
            new CM_Model_StreamChannelArchive_Video($streamChannel->getId());
            $this->fail('Archive exists before StreamChannel deleted.');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
        $streamChannel->onUnpublish($streamPublish);
        try {
            new CM_Model_StreamChannelArchive_Video($streamChannel->getId());
            $this->assertTrue(true);
        } catch (CM_Exception_Nonexistent $ex) {
            $this->fail('Archive was not created.');
        }
    }

    public function testCanSubscribe() {
        $user = SKTest_TH::createUser();
        $intruder = SKTest_TH::createUser();
        $chat = SKTest_TH::createChat(array($user));
        $streamChannel = SKTest_TH::createStreamChannelVideoChat($chat);
        $this->assertSameTime(time() + 990, $streamChannel->canSubscribe($user, time() - 10));
        $this->assertSameTime(time() - 10, $streamChannel->canSubscribe($intruder, time() - 10));
    }

    public function testCanPublish() {
        $user = SKTest_TH::createUser();
        $intruder = SKTest_TH::createUser();
        $chat = SKTest_TH::createChat(array($user));
        $streamChannel = SKTest_TH::createStreamChannelVideoChat($chat);
        $this->assertSameTime(time() + 990, $streamChannel->canPublish($user, time() - 10));
        $this->assertSameTime(time() - 10, $streamChannel->canPublish($intruder, time() - 10));
    }
}
