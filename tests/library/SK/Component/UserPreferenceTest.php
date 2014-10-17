<?php

class SK_Component_UserPreferenceTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_UserPreference();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_UserPreference(null);

        // Particular value false
        $viewer->getPreferences()->set('mailbox', 'notify_messages', false);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertContains('.config.mailbox', $page->find('.user_preference')->getText());
        $this->assertFalse($page->has('input[name="mailbox___notify_messages"][checked]'));

        // Particular value true
        $viewer->getPreferences()->set('mailbox', 'notify_messages', true);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('input[name="mailbox___notify_messages"][checked]'));
    }
}
