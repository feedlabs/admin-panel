<?php

class SK_Component_ProfileEditTest extends SKTest_TestCase {

    public function testGuest() {
        $params = array('profileId' => SKTest_TH::createUser()->getProfile()->getId());
        $cmp = new SK_Component_ProfileEdit($params);

        $this->assertComponentNotAccessible($cmp);
    }

    protected function _render(SK_User $viewer = null) {
        // AboutMe
        $profile = SKTest_TH::createUser()->getProfile();
        $cmp = new SK_Component_ProfileEdit(array('profileId' => $profile->getId(), 'tab' => 'aboutMe'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContainsAll(array(
            'Profile Info',
            'Social',
            'Interests',
            'Physical Features',
            'Sexual Info',
        ), $page->find('.profile_edit_me')->getText());

        // AboutMatch
        $cmp = new SK_Component_ProfileEdit(array('tab' => 'aboutMatch', 'profileId' => $profile->getId()));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContainsAll(array('Profile Info', 'Social', 'Physical Features'), $page->find('.profile_edit_match')->getText());
    }
}
