<?php

class SK_Component_UserInteractionTest extends SKTest_TestCase {

    public function testGuest() {
        $params = array('user' => SKTest_TH::createUser());
        $cmp = new SK_Component_UserInteraction($params);
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertFalse($page->has('.SK_Component_Pinboard_DropdownList'));
        $this->assertContainsAll(array('Message', 'Gift'), $page->getText());
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $user = SKTest_TH::createUser();
        $cmp = new SK_Component_UserInteraction(array('user' => $user));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Add Friend', $page->find('button')->getAttribute('value'));
        $this->assertContains('Message', $page->find('button:eq(1)')->getAttribute('value'));
        $this->assertContains('Gift', $page->find('button:eq(2)')->getAttribute('value'));
        $this->assertTrue($page->has('.SK_Component_Pinboard_DropdownList'));
        $this->assertContains('Block User', $page->find('.SK_Component_MenuContext')->getText());
    }

    public function testFreeuserBlockedFriend() {
        $viewer = $this->_createViewer();
        $user = SKTest_TH::createUser();
        $viewer->getBlockings()->add($user);
        $viewer->getFriends()->add($user);
        $cmp = new SK_Component_UserInteraction(array('user' => $user));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Remove Friend', $page->find('.SK_Component_MenuContext')->getText());
        $this->assertContains('Unblock', $page->find('.SK_Component_MenuContext')->getText());
    }
}
