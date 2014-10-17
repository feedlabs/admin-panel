<?php

class SK_Component_ReviewTest extends SKTest_TestCase {

    public function testGuest() {
        $params = array('user' => SKTest_TH::createUser());
        $component = new SK_Component_Review($params);

        $this->assertComponentNotAccessible($component);
    }

    /**
     * @expectedException CM_Exception_NotAllowed
     */
    public function testFreeuser() {
        $environment = new CM_Frontend_Environment(null, $this->_createViewer());
        $params = array('user' => SKTest_TH::createUser());
        $component = new SK_Component_Review($params);
        $component->checkAccessible($environment);
    }

    public function testReviewerCanReviewModerator() {
        $viewer = $this->_createViewer();
        $viewer->getRoles()->add(SK_Role::REVIEWER);

        $user = SKTest_TH::createUser();
        $user->getRoles()->add(SK_Role::MODERATOR);
        $params = array('user' => $user);

        $component = new SK_Component_Review($params);
        $this->assertComponentAccessible($component, $viewer);

        $environment = new CM_Frontend_Environment(null, $viewer);

        $response = $this->getResponseAjax($component, 'setReview', ['user' => $user, 'type' => SK_ModelAsset_User_Reviews::TYPE_SPAM], $environment);
        $this->assertViewResponseSuccess($response);
    }

    public function testModeratorCannotReviewModerator() {
        $viewer = $this->_createViewer();
        $viewer->getRoles()->add(SK_Role::MODERATOR);

        $user = SKTest_TH::createUser();
        $user->getRoles()->add(SK_Role::MODERATOR);
        $params = array('user' => $user);

        $component = new SK_Component_Review($params);
        $this->assertComponentAccessible($component, $viewer);

        $environment = new CM_Frontend_Environment(null, $viewer);
        $response = $this->getResponseAjax($component, 'setReview', array('user' => $user, 'type' => SK_ModelAsset_User_Reviews::TYPE_SPAM), $environment);

        $errorMsg = 'You are not allowed to review a moderator';
        $responseContent = json_decode($response->getContent(), true);
        $this->assertTrue(isset($responseContent['error']));
        $this->assertTrue(isset($responseContent['error']['msg']));
        $this->assertContains($errorMsg, $responseContent['error']['msg']);
    }
}
