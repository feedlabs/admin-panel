<?php

class SK_Component_ChatTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_Chat();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_Chat(null);

        // Online
        $viewer->setOnline(true);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.SK_Component_Chat'));
        $this->assertContains('Chat', $page->find('.panel')->getText());
        $this->assertTrue($page->has('.disableVisible'));

        // Offline
        $viewer->setOnline(false);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.SK_Component_Chat'));
        $this->assertContains('Chat', $page->find('.panel')->getText());
        $this->assertTrue($page->has('.enableVisible'));
    }

    public function testAjax_inviteUserLimit() {
        $viewer = SKTest_TH::createUser();
        $environment = new CM_Frontend_Environment(null, $viewer);
        $chat = SKTest_TH::createChat(array($viewer));
        $component = new SK_Component_Chat();

        for ($i = 0; $i < SK_Component_Chat::USERS_MAX - 1; $i++) {
            $user = SKTest_TH::createUser();

            $response = $this->getResponseAjax($component, 'inviteUser', ['chat' => $chat, 'user' => $user], $environment);
            $this->assertViewResponseSuccess($response);
        }

        $user = SKTest_TH::createUser();
        $response = $this->getResponseAjax($component, 'inviteUser', ['chat' => $chat, 'user' => $user], $environment);
        $this->assertViewResponseError($response, null, 'Reached the maximum amount of people in one chat!');
    }
}
