<?php

class SK_Component_TermsTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_Terms();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('.article.terms', $page->getText());
    }
}
