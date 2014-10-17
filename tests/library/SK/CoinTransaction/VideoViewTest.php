<?php

class SK_CoinTransaction_VideoViewTest extends SKTest_TestCase {

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $video = SKTest_TH::createVideo();
        SKTest_TH::createCoinTransactionAdminGive($user, null, 40);
        /** @var SK_CoinTransaction_VideoView $coinTransaction */
        $coinTransaction = SK_CoinTransaction_VideoView::createStatic(array('user' => $user, 'amount' => -40, 'video' => $video));
        $this->assertRow('sk_coinTransaction_videoView', array('id' => $coinTransaction->getId(), 'videoId' => $video->getId()));
        $this->assertInstanceOf('SK_CoinTransaction_VideoView', $coinTransaction);
        $this->assertEquals($video->getId(), $coinTransaction->getVideoId());
        $this->assertEquals($video, $coinTransaction->getVideo());
    }

    public function testGetVideoDeleted() {
        $user = SKTest_TH::createUser();
        $video = SKTest_TH::createVideo();
        SKTest_TH::createCoinTransactionAdminGive($user, null, 40);
        /** @var SK_CoinTransaction_VideoView $coinTransaction */
        $coinTransaction = SK_CoinTransaction_VideoView::createStatic(array('user' => $user, 'amount' => -40, 'video' => $video));
        $this->assertEquals($video->getId(), $coinTransaction->getVideoId());
        $this->assertEquals($video, $coinTransaction->getVideo());
        $video->delete();
        $this->assertEquals($video->getId(), $coinTransaction->getVideoId());
        $this->assertNull($coinTransaction->getVideo());
    }
}
