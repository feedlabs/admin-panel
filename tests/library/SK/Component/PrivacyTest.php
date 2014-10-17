<?php

class SK_Component_PrivacyTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_Privacy();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('.article.privacy', $page->getText());
    }
}
