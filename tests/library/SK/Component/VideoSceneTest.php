<?php

class SK_Component_VideoSceneTest extends SKTest_TestCase {

    public function testGuest() {
        $video = SKTest_TH::createVideo();
        $componentVideoScene = new SK_Component_VideoScene(array('scene' => $video->getSceneFirst()));
        $html = $this->_renderComponent($componentVideoScene);

        $this->assertComponentAccessible($componentVideoScene);
        $this->assertTrue($html->has('.embeddedWrapper'));
        $this->assertFalse($html->has('.SK_Component_CoinPurchase'));
        $this->assertFalse($html->has('.reloadAutoPlay'));
    }

    public function testHttp() {
        $video = SKTest_TH::createVideo(null, false);
        $componentVideoScene = new SK_Component_VideoScene(array('scene' => $video->getSceneFirst()));
        $html = $this->_renderComponent($componentVideoScene);

        $this->assertComponentAccessible($componentVideoScene);
        $this->assertTrue($html->has('.embeddedWrapper a[target="_blank"]'));
        $this->assertFalse($html->has('.embeddedWrapper iframe'));
    }

    public function testHttps() {
        $video = SKTest_TH::createVideo(null, true);
        $componentVideoScene = new SK_Component_VideoScene(array('scene' => $video->getSceneFirst()));
        $html = $this->_renderComponent($componentVideoScene);

        $this->assertComponentAccessible($componentVideoScene);
        $this->assertTrue($html->has('.embeddedWrapper iframe'));
        $this->assertFalse($html->has('.embeddedWrapper a[target="_blank"]'));
    }

    public function testPremiumVideoGuest() {
        $video = SKTest_TH::createVideoPremium();
        $componentVideoScene = new SK_Component_VideoScene(array('scene' => $video->getSceneFirst()));
        $html = $this->_renderComponent($componentVideoScene);

        $this->assertComponentAccessible($componentVideoScene);
        $this->assertTrue($html->has('.embeddedWrapper'));
        $this->assertFalse($html->has('video'));
        $this->assertTrue($html->has('.SK_Component_CoinPurchase'));
        $this->assertFalse($html->has('.reloadAutoPlay'));
    }

    public function testPremiumVideoFreeUser() {
        $viewer = SKTest_TH::createUser();
        $video = SKTest_TH::createVideoPremium();
        $componentVideoScene = new SK_Component_VideoScene(array('scene' => $video->getSceneFirst()));
        $html = $this->_renderComponent($componentVideoScene, $viewer);

        $this->assertComponentAccessible($componentVideoScene, $viewer);
        $this->assertTrue($html->has('.embeddedWrapper'));
        $this->assertFalse($html->has('video'));
        $this->assertTrue($html->has('.SK_Component_CoinPurchase'));
        $this->assertFalse($html->has('.reloadAutoPlay'));
    }

    public function testPremiumVideoPremiumUser() {
        $viewer = SKTest_TH::createUserPremium();
        $video = SKTest_TH::createVideoPremium();
        $componentVideoScene = new SK_Component_VideoScene(array('scene' => $video->getSceneFirst()));
        $html = $this->_renderComponent($componentVideoScene, $viewer);

        $this->assertComponentAccessible($componentVideoScene, $viewer);
        $this->assertTrue($html->has('.embeddedWrapper'));
        $this->assertFalse($html->has('video'));
        $this->assertTrue($html->has('.SK_Component_CoinPurchase'));
        $this->assertFalse($html->has('.reloadAutoPlay'));
    }

    public function testPremiumVideoFeedGuest() {
        $video = SKTest_TH::createVideoPremium();
        $componentVideoScene = new SK_Component_VideoScene(array('scene' => $video->getSceneFirst(), 'displayThumbnailFirst' => true));
        $html = $this->_renderComponent($componentVideoScene);

        $this->assertComponentAccessible($componentVideoScene);
        $this->assertTrue($html->has('.embeddedWrapper'));
        $this->assertFalse($html->has('video'));
        $this->assertFalse($html->has('.SK_Component_CoinPurchase'));
        $this->assertTrue($html->has('.reloadAutoPlay'));
    }

    public function testPremiumVideoFeedFreeUser() {
        $viewer = SKTest_TH::createUser();
        $video = SKTest_TH::createVideoPremium();
        $componentVideoScene = new SK_Component_VideoScene(array('scene' => $video->getSceneFirst(), 'displayThumbnailFirst' => true));
        $html = $this->_renderComponent($componentVideoScene, $viewer);

        $this->assertComponentAccessible($componentVideoScene, $viewer);
        $this->assertTrue($html->has('.embeddedWrapper'));
        $this->assertFalse($html->has('video'));
        $this->assertFalse($html->has('.SK_Component_CoinPurchase'));
        $this->assertTrue($html->has('.reloadAutoPlay'));
    }

    public function testPremiumVideoFeedPremiumUser() {
        $viewer = SKTest_TH::createUserPremium();
        $video = SKTest_TH::createVideoPremium();
        $componentVideoScene = new SK_Component_VideoScene(array('scene' => $video->getSceneFirst(), 'displayThumbnailFirst' => true));
        $html = $this->_renderComponent($componentVideoScene, $viewer);

        $this->assertComponentAccessible($componentVideoScene, $viewer);
        $this->assertTrue($html->has('.embeddedWrapper'));
        $this->assertFalse($html->has('video'));
        $this->assertFalse($html->has('.SK_Component_CoinPurchase'));
        $this->assertTrue($html->has('.reloadAutoPlay'));
    }

    public function testPurchasedVideo() {
        $viewer = SKTest_TH::createUser();
        $video = SKTest_TH::createVideoPremium();
        SKTest_TH::createCoinTransactionAdminGive($viewer, null, $video->getPurchasePrice());
        $video->purchase($viewer);
        $componentVideoScene = new SK_Component_VideoScene(array('scene' => $video->getSceneFirst()));
        $html = $this->_renderComponent($componentVideoScene, $viewer);

        $this->assertComponentAccessible($componentVideoScene, $viewer);
        $this->assertTrue($html->has('.embeddedWrapper'));
        $this->assertTrue($html->has('video'));
        $this->assertFalse($html->has('.SK_Component_CoinPurchase'));
        $this->assertFalse($html->has('.reloadAutoPlay'));
    }

    public function testPurchasedVideoFeed() {
        $viewer = SKTest_TH::createUser();
        $video = SKTest_TH::createVideoPremium();
        SKTest_TH::createCoinTransactionAdminGive($viewer, null, $video->getPurchasePrice());
        $video->purchase($viewer);
        $componentVideoScene = new SK_Component_VideoScene(array('scene' => $video->getSceneFirst(), 'displayThumbnailFirst' => true));
        $html = $this->_renderComponent($componentVideoScene, $viewer);

        $this->assertComponentAccessible($componentVideoScene, $viewer);
        $this->assertTrue($html->has('.embeddedWrapper'));
        $this->assertFalse($html->has('video'));
        $this->assertFalse($html->has('.SK_Component_CoinPurchase'));
        $this->assertTrue($html->has('.reloadAutoPlay'));
    }
}
