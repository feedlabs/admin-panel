<?php

class SK_ParamsTest extends SKTest_TestCase {

    public function testGetPhoto() {
        $params = new SK_Params(
            array('entity1' => CM_Params::encode(SKTest_TH::createPhoto()), 'entity2' => SKTest_TH::createPhoto()->getId(),
                  'entity3' => SKTest_TH::createPhoto(),
                  'entity4' => 'foo',
                  'entity5' => SKTest_TH::createPinboard()));

        $this->assertInstanceOf('SK_Entity_Photo', $params->getPhoto('entity1'));
        $this->assertInstanceOf('SK_Entity_Photo', $params->getPhoto('entity2'));
        $this->assertInstanceOf('SK_Entity_Photo', $params->getPhoto('entity3'));
        try {
            $params->getPhoto('entity4');
            $this->fail('nonexistent param. should not exist');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }

        try {
            $params->getPhoto('entity5');
            $this->fail('wrong parameter type');
        } catch (CM_Exception_InvalidParam $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetVideo() {
        $params = new SK_Params(
            array('entity1' => CM_Params::encode(SKTest_TH::createVideo()), 'entity2' => SKTest_TH::createVideo()->getId(),
                  'entity3' => SKTest_TH::createVideo(),
                  'entity4' => 'foo',
                  'entity5' => SKTest_TH::createPinboard()));

        $this->assertInstanceOf('SK_Entity_Video', $params->getVideo('entity1'));
        $this->assertInstanceOf('SK_Entity_Video', $params->getVideo('entity2'));
        $this->assertInstanceOf('SK_Entity_Video', $params->getVideo('entity3'));
        try {
            $params->getVideo('entity4');
            $this->fail('nonexistent param. should not exist');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }

        try {
            $params->getVideo('entity5');
            $this->fail('wrong parameter type');
        } catch (CM_Exception_InvalidParam $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetVideoScene() {
        $params = new SK_Params(
            array('scene1' => CM_Params::encode(SKTest_TH::createVideoScene()), 'scene2' => SKTest_TH::createVideoScene()->getId(),
                  'scene3' => SKTest_TH::createVideoScene(),
                  'scene4' => 'foo',
                  'scene5' => SKTest_TH::createPinboard()));

        $this->assertInstanceOf('SK_Model_Video_Scene', $params->getVideoScene('scene1'));
        $this->assertInstanceOf('SK_Model_Video_Scene', $params->getVideoScene('scene2'));
        $this->assertInstanceOf('SK_Model_Video_Scene', $params->getVideoScene('scene3'));
        try {
            $params->getVideoScene('scene4');
            $this->fail('nonexistent param. should not exist');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }

        try {
            $params->getVideoScene('scene5');
            $this->fail('wrong parameter type');
        } catch (CM_Exception_InvalidParam $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetBlogpost() {
        $params = new SK_Params(
            array('entity1' => CM_Params::encode(SKTest_TH::createBlogpost()), 'entity2' => SKTest_TH::createBlogpost()->getId(),
                  'entity3' => SKTest_TH::createBlogpost(),
                  'entity4' => 'foo',
                  'entity5' => SKTest_TH::createPinboard()));

        $this->assertInstanceOf('SK_Entity_Blogpost', $params->getBlogpost('entity1'));
        $this->assertInstanceOf('SK_Entity_Blogpost', $params->getBlogpost('entity2'));
        $this->assertInstanceOf('SK_Entity_Blogpost', $params->getBlogpost('entity3'));
        try {
            $params->getBlogpost('entity4');
            $this->fail('nonexistent param. should not exist');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }

        try {
            $params->getBlogpost('entity5');
            $this->fail('wrong parameter type');
        } catch (CM_Exception_InvalidParam $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetConversation() {
        $params = new SK_Params(
            array('entity1' => CM_Params::encode(SKTest_TH::createConversation()), 'entity2' => SKTest_TH::createConversation()->getId(),
                  'entity3' => SKTest_TH::createConversation(),
                  'entity4' => 'foo',
                  'entity5' => SKTest_TH::createPinboard()));

        $this->assertInstanceOf('SK_Entity_Conversation', $params->getConversation('entity1'));
        $this->assertInstanceOf('SK_Entity_Conversation', $params->getConversation('entity2'));
        $this->assertInstanceOf('SK_Entity_Conversation', $params->getConversation('entity3'));
        try {
            $params->getConversation('entity4');
            $this->fail('nonexistent param. should not exist');
        } catch (CM_Exception_Nonexistent $e) {
            $this->assertTrue(true);
        }

        try {
            $params->getConversation('entity5');
            $this->fail('wrong parameter type');
        } catch (CM_Exception_InvalidParam $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetProfile() {
        $profile1 = SKTest_TH::createUser()->getProfile();
        $profile2 = SKTest_TH::createUser()->getProfile();
        $profile3 = SKTest_TH::createUser()->getProfile();
        $params = new SK_Params(
            array('entity1' => CM_Params::encode($profile1), 'entity2' => $profile2->getId(),
                  'entity3' => $profile3,
                  'entity5' => SKTest_TH::createPinboard()));

        $this->assertEquals($profile1, $params->getProfile('entity1'));
        $this->assertEquals($profile2, $params->getProfile('entity2'));
        $this->assertEquals($profile3, $params->getProfile('entity3'));

        try {
            $params->getProfile('entity5');
            $this->fail('wrong parameter type');
        } catch (CM_Exception_InvalidParam $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetEntityQuery() {
        $entityQuery1 = new SK_EntityQuery_EntityQuery(new CM_Params(), new CM_Frontend_Environment());
        $entityQuery2 = new SK_EntityQuery_Photo(new CM_Params(), new CM_Frontend_Environment());
        $params = new SK_Params(array(
            'entityQuery1' => CM_Params::encode($entityQuery1),
            'entityQuery2' => $entityQuery2,
            'entityQuery3' => SKTest_TH::createUser(),
        ));

        $this->assertEquals($entityQuery1, $params->getEntityQuery('entityQuery1'));
        $this->assertEquals($entityQuery2, $params->getEntityQuery('entityQuery2'));

        try {
            $params->getEntityQuery('entityQuery3');
            $this->fail('wrong parameter type');
        } catch (CM_Exception_InvalidParam $e) {
            $this->assertTrue(true);
        }
    }
}
