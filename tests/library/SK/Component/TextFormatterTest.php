<?php

class SK_Component_TextFormatterTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_TextFormatter(array('for' => 'testName'));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContainsAll(array('emoticon', 'photo', 'markdown'), $page->getHtml());
    }

    public function testGuestCustomControls() {
        $cmp = new SK_Component_TextFormatter(array('for' => 'testName', 'controls' => array('markdown')));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('markdown', $page->getHtml());
        $this->assertNotContainsAll(array('emoticon', 'photo'), $page->getHtml());
    }
}
