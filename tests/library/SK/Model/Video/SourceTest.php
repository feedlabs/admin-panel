<?php

class SK_Model_Video_SourceTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $video = SK_Entity_Video::create($user, 'Test 1', SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        /** @var SK_Model_Video_Scene $scene */
        $scene = SK_Model_Video_Scene::createStatic(array('video' => $video, 'duration' => 666));
        $this->assertSame(0, $scene->getSourceList()->getCount());
        SK_Model_Video_Source::createStatic(
            array('videoScene' => $scene, 'src' => 'test.mp4', 'type' => SK_Model_Video_Source::TYPE_IFRAME, 'ratio' => 0.61, 'isPreview' => false));
        $this->assertSame(1, $scene->getSourceList()->getCount());

        /** @var SK_Model_Video_Source $source */
        $source = SK_Model_Video_Source::createStatic(
            array('videoScene' => $scene, 'src' => 'test1.mp4', 'isPreview' => true, 'type' => SK_Model_Video_Source::TYPE_OBJECT,
                  'flashvars'  => 'test', 'height' => 480, 'ratio' => 0.61));
        $this->assertSame($scene->getId(), $source->getVideoScene()->getId());
        $this->assertSame('test1.mp4', $source->getSrc());
        $this->assertSame(true, $source->getIsPreview());
        $this->assertSame(SK_Model_Video_Source::TYPE_OBJECT, $source->getSourceType());
        $this->assertSame('test', $source->getFlashvars());
        $this->assertSame(480, $source->getHeight());
        $this->assertSame(0.61, $source->getRatio());
        $source = SKTest_TH::createVideoSource($scene);
        $this->assertNull($source->getFlashvars());
    }

    public function testGetDomain() {
        $video = SKTest_TH::createVideo();
        $this->assertSame('youtube.com', $video->getSceneFirst()->getSourceFirst()->getDomain());
    }

    public function testGetFlashvarsDecoded() {
        $scene = SKTest_TH::createVideoScene();
        /** @var SK_Model_Video_Source $source */
        $source = SK_Model_Video_Source::createStatic(
            array('videoScene' => $scene, 'src' => 'test1.mp4', 'isPreview' => true, 'type' => SK_Model_Video_Source::TYPE_OBJECT,
                  'flashvars'  => 'foo=2&bar=3', 'height' => 480, 'ratio' => 0.61));

        $this->assertSame(array('foo' => '2', 'bar' => '3'), $source->getFlashvarsDecoded());
    }

    public function testGetFlashvarsDecodedInvalid() {
        $scene = SKTest_TH::createVideoScene();
        /** @var SK_Model_Video_Source $source */
        $source = SK_Model_Video_Source::createStatic(
            array('videoScene' => $scene, 'src' => 'test1.mp4', 'isPreview' => true, 'type' => SK_Model_Video_Source::TYPE_OBJECT,
                  'flashvars'  => 'sdfhsdfsdf', 'height' => 480, 'ratio' => 0.61));

        $this->assertSame(array('sdfhsdfsdf' => ''), $source->getFlashvarsDecoded());
    }

    public function testGetFlashvarsDecodedNone() {
        $scene = SKTest_TH::createVideoScene();
        /** @var SK_Model_Video_Source $source */
        $source = SK_Model_Video_Source::createStatic(
            array('videoScene' => $scene, 'src' => 'test.mp4', 'type' => SK_Model_Video_Source::TYPE_IFRAME, 'ratio' => 0.61, 'isPreview' => false));

        $this->assertSame(null, $source->getFlashvarsDecoded());
    }
}
