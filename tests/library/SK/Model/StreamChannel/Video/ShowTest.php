<?php

class SK_Model_StreamChannel_Video_ShowTest extends SKTest_TestCase {

    public static function setUpBeforeClass() {
        CM_Config::get()->CM_Stream_Video->servers = array(1 => array('publicHost' => 'wowza1.example.com', 'privateIp' => '10.0.3.108'));
    }

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $chatShow = SKTest_TH::createChatShow();
        $params = CM_Params::factory(array('chatId' => $chatShow->getId()), false);
        /** @var SK_Model_StreamChannel_Video_Show $streamChannel */
        $streamChannel = SK_Model_StreamChannel_Video_Show::createStatic(array('key'         => '1_1', 'price' => 123.23, 'width' => 720, 'height' => 1028,
                                                                         'serverId'    => 1, 'thumbnailCount' => 0,
                                                                         'adapterType' => CM_Stream_Adapter_Video_Wowza::getTypeStatic(),
                                                                         'params'      => $params));
        $this->assertInstanceOf('SK_Model_StreamChannel_Video_Show', $streamChannel);
        $this->assertRow('cm_streamChannel', array('id'   => $streamChannel->getId(), 'key' => '1_1',
                                                   'type' => SK_Model_StreamChannel_Video_Show::getTypeStatic()));
        $this->assertRow('sk_chat_streamChannel', array('channelId' => $streamChannel->getId()));
        $this->assertSame('10.0.3.108', $streamChannel->getPrivateHost());
        $this->assertSame('wowza1.example.com', $streamChannel->getPublicHost());
        $this->assertSame(720, $streamChannel->getWidth());
        $this->assertSame(1028, $streamChannel->getHeight());
    }

    public function testCreateTwice() {
        $chatShow = SKTest_TH::createChatShow();
        $params = CM_Params::factory(array('chatId' => $chatShow->getId()), true);

        $streamChannel = SK_Model_StreamChannel_Video_Show::createStatic(array('key'         => '1_1', 'price' => 123.23, 'width' => 720, 'height' => 1028,
                                                                         'serverId'    => 1, 'thumbnailCount' => 0,
                                                                         'adapterType' => CM_Stream_Adapter_Video_Wowza::getTypeStatic(),
                                                                         'params'      => $params));

        try {
            SK_Model_StreamChannel_Video_Show::createStatic(array('key'            => '1_2', 'price' => 123.23, 'width' => 720, 'height' => 1028,
                                                            'serverId'       => 1,
                                                            'thumbnailCount' => 0, 'adapterType' => CM_Stream_Adapter_Video_Wowza::getTypeStatic(),
                                                            'params'         => $params));
            $this->fail('Can create streamchannel for the same chatShow twice');
        } catch (CM_Exception_Invalid $e) {
            $this->assertContains('already has a streamChannel', $e->getMessage());
        }

        $streamChannel->delete();
    }

    public function testGetShow() {
        $streamChannel = SKTest_TH::createStreamChannelVideoShow();
        $this->assertInstanceOf('SK_Entity_Chat_Show', $streamChannel->getShow());
        $this->assertTrue($streamChannel->getShow()->getVideoStreamChannels()->contains($streamChannel));
    }

    public function testDelete() {
        $show = SKTest_TH::createChatShow();
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($show);
        $streamChannel->delete();

        $this->assertNotRow('cm_streamChannel', array('id' => $streamChannel->getId()));
        $this->assertNotRow('cm_streamChannel_video', array('id' => $streamChannel->getId()));
        $this->assertNotRow('sk_chat_streamChannel', array('channelId' => $streamChannel->getId()));

        try {
            SKTest_TH::reinstantiateModel($show);
            $this->fail('Show not deleted.');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }

    public function testOnUnpublish() {
        $show = SKTest_TH::createChatShow();
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($show);
        $publish = SKTest_TH::createStreamPublish($show->getUser(), $streamChannel);

        $subscriber1 = SKTest_TH::createUser();
        $subscriber2 = SKTest_TH::createUser();
        $subscriber3 = SKTest_TH::createUser();
        $subscriber3->delete();

        $publish->delete();
        $streamChannel->delete();
        try {
            $showArchive = new SK_Entity_ShowArchive($show->getId());
        } catch (CM_Exception_Nonexistent $ex) {
            $this->fail('ShowArchive not created.');
        }
    }

    public function testCanSubscribe() {
        $owner = SKTest_TH::createUser();
        $chatShow = SKTest_TH::createChatShow($owner);
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($chatShow);
        $user = SKTest_TH::createUser();
        $time = time();
        $this->assertSame($time + 1000, $streamChannel->canSubscribe($user, $time));
        $this->assertSame($time, $streamChannel->canSubscribe(null, $time));
    }

    public function testCanPublish() {
        $owner = SKTest_TH::createUser();
        $user = SKTest_TH::createUser();
        $chatShow = SKTest_TH::createChatShow($owner);
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($chatShow);
        $time = time();
        $this->assertSameTime($time - 10, $streamChannel->canPublish($user, $time - 10));
        $this->assertSameTime($time + 990, $streamChannel->canPublish($owner, $time - 10));
    }

    public function testOnPublish() {
        $show = SKTest_TH::createChatShow();
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($show);
        $this->assertSame(0, $show->getUsers()->getCount());
        $streamChannel->onPublish(SKTest_TH::createStreamPublish());
        $this->assertSame(1, $show->getUsers()->getCount());
    }

    public function testOnSubscribe() {
        $show = SKTest_TH::createChatShow();
        $streamChannel = SKTest_TH::createStreamChannelVideoShow($show);
        $this->assertSame(0, $show->getUsers()->getCount());
        $streamChannel->onSubscribe(SKTest_TH::createStreamSubscribe(SKTest_TH::createUser()));
        $this->assertSame(1, $show->getUsers()->getCount());
        $streamChannel->onSubscribe(SKTest_TH::createStreamSubscribe());
        $this->assertSame(1, $show->getUsers()->getCount());
    }
}
