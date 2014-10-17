<?php

class SK_Model_Video_SceneTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $video = SK_Entity_Video::create($user, 'Test 1', SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        $this->assertSame(0, $video->getSceneList()->getCount());
        SK_Model_Video_Scene::createStatic(array('video' => $video, 'sequence' => $video->getSceneList()->getCount() + 1, 'duration' => 666));
        $this->assertSame(1, $video->getSceneList()->getCount());

        /** @var SK_Model_Video_Scene $scene */
        $scene = SK_Model_Video_Scene::createStatic(array('video'        => $video, 'description' => 'test', 'duration' => 1234,
                                                          'thumbnailUrl' => 'test.jpg'));
        $this->assertSame(2, $scene->getSequence());
        $this->assertSame('test', $scene->getDescription());
        $this->assertSame(1234, $scene->getDuration());
        $this->assertSame($video->getId(), $scene->getVideo()->getId());
        $this->assertSame('test.jpg', $scene->getThumbnailUrl());

        /** @var SK_Model_Video_Scene $scene */
        $scene = SK_Model_Video_Scene::createStatic(array('video' => $video, 'sequence' => 4, 'duration' => 1234));
        $this->assertSame(4, $scene->getSequence());
        $this->assertNull($scene->getDescription());
        $this->assertNull($scene->getThumbnailUrl());
    }

    public function testGetSourceFirst() {
        $scene = SKTest_TH::createVideoScene();
        $video = $scene->getVideo();

        try {
            $scene->getSourceFirst();
            $this->fail('Could get first video source even if there is none');
        } catch (CM_Exception_Invalid $exception) {
            $this->assertTrue(true);
        }

        try {
            SKTest_TH::reinstantiateModel($video);
            $this->fail('Video without any source should have been deleted');
        } catch (CM_Exception_Nonexistent $exception) {
            $this->assertTrue(true);
        }

        $source = SKTest_TH::createVideoSource();
        $scene = $source->getVideoScene();
        $this->assertEquals($source, $scene->getSourceFirst());
    }

    public function testGetSource() {
        $scene = SKTest_TH::createVideoScene();
        $video = $scene->getVideo();

        try {
            $scene->getSource(100);
            $this->fail('Could get video scene source even if there is none');
        } catch (CM_Exception_Invalid $exception) {
            $this->assertTrue(true);
        }

        try {
            SKTest_TH::reinstantiateModel($video);
            $this->fail('Video without any scene source should have been deleted');
        } catch (CM_Exception_Nonexistent $exception) {
            $this->assertTrue(true);
        }

        $scene1 = SKTest_TH::createVideoScene();
        $video = $scene1->getVideo();
        SK_Model_Video_Source::createStatic(array(
            'videoScene' => $scene1,
            'isPreview'  => false,
            'src'        => 'low',
            'ratio'      => 1,
            'type'       => SK_Model_Video_Source::TYPE_ADULTCENTRO,
        ));
        SK_Model_Video_Source::createStatic(array(
            'videoScene' => $scene1,
            'isPreview'  => false,
            'src'        => 'mid',
            'ratio'      => 1,
            'type'       => SK_Model_Video_Source::TYPE_ADULTCENTRO,
            'height'     => 10,
        ));
        SK_Model_Video_Source::createStatic(array(
            'videoScene' => $scene1,
            'isPreview'  => false,
            'src'        => 'high',
            'ratio'      => 1,
            'type'       => SK_Model_Video_Source::TYPE_ADULTCENTRO,
            'height'     => 100,
        ));
        SK_Model_Video_Source::createStatic(array(
            'videoScene' => $scene1,
            'isPreview'  => true,
            'src'        => 'lowPreview',
            'ratio'      => 1,
            'type'       => SK_Model_Video_Source::TYPE_ADULTCENTRO,
            'height'     => 1,
        ));
        SK_Model_Video_Source::createStatic(array(
            'videoScene' => $scene1,
            'isPreview'  => true,
            'src'        => 'highPreview',
            'ratio'      => 1,
            'type'       => SK_Model_Video_Source::TYPE_ADULTCENTRO,
            'height'     => 100,
        ));

        $this->assertSame('mid', $scene1->getSource(40, false)->getSrc());
        $this->assertSame('mid', $scene1->getSource(2, false)->getSrc());
        $this->assertSame('high', $scene1->getSource(1000, false)->getSrc());
        $this->assertSame('highPreview', $scene1->getSource(100, true)->getSrc());

        /** @var SK_Model_Video_Scene $scene2 */
        $scene2 = SK_Model_Video_Scene::createStatic(array(
            'video'    => $video,
            'duration' => 1000,
        ));
        SK_Model_Video_Source::createStatic(array(
            'videoScene' => $scene2,
            'isPreview'  => false,
            'src'        => 'one',
            'ratio'      => 1,
            'type'       => SK_Model_Video_Source::TYPE_ADULTCENTRO,
        ));
        SK_Model_Video_Source::createStatic(array(
            'videoScene' => $scene2,
            'isPreview'  => false,
            'src'        => 'two',
            'ratio'      => 1,
            'type'       => SK_Model_Video_Source::TYPE_ADULTCENTRO,
        ));

        $this->assertSame('two', $scene2->getSource(100, false)->getSrc());

        try {
            $scene2->getSource(100, true);
            $this->fail('Could get video scene preview source even if there is none');
        } catch (CM_Exception_Invalid $exception) {
            $this->assertTrue(true);
        }

        try {
            SKTest_TH::reinstantiateModel($video);
            $this->fail('Video without any scene preview source should have been deleted');
        } catch (CM_Exception_Nonexistent $exception) {
            $this->assertTrue(true);
        }
    }

    public function testFilterHasPreview() {
        $user = SKTest_TH::createUser();
        $video = SK_Entity_Video::create($user, 'Duck song', SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        /** @var SK_Model_Video_Scene $scene */
        $scene = SK_Model_Video_Scene::createStatic(array('video' => $video, 'duration' => 1000));
        SK_Model_Video_Source::createStatic(array('videoScene' => $scene, 'isPreview' => true, 'src' => 'preview', 'ratio' => 1,
                                                  'type'       => SK_Model_Video_Source::TYPE_ADULTCENTRO));
        /** @var SK_Model_Video_Scene $scene2 */
        $scene2 = SK_Model_Video_Scene::createStatic(array('video' => $video, 'duration' => 1000));
        SK_Model_Video_Source::createStatic(array('videoScene' => $scene2, 'isPreview' => false, 'src' => 'noPreview', 'ratio' => 1,
                                                  'type'       => SK_Model_Video_Source::TYPE_ADULTCENTRO));

        $this->assertTrue($scene->hasPreview());
        $this->assertFalse($scene2->hasPreview());
    }

    public function testSetDescription() {
        $scene = SKTest_TH::createVideoScene();
        $scene->setDescription('superTest');
        $this->assertSame('superTest', $scene->getDescription());

        $scene->setDescription(null);
        $this->assertNull($scene->getDescription());
    }

    public function testGetSceneNext() {
        $video = SKTest_TH::createVideo();
        $scondScene = SKTest_TH::createVideoScene($video);
        $firstScene = $video->getSceneFirst();

        $this->assertEquals($scondScene, $firstScene->getSceneNext());
        $this->assertNull($scondScene->getSceneNext());
    }
}
