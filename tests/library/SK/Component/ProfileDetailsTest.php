<?php

class SK_Component_ProfileDetailsTest extends SKTest_TestCase {

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $profile = SKTest_TH::createUser()->getProfile();
        $cmp = new SK_Component_ProfileDetails(array('profile' => $profile, 'tab' => 'aboutMe'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContainsAll(array('About'), $page->getText());
    }
}
