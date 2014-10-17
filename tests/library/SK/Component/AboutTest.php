<?php

class SK_Component_AboutTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_About();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('.article.about', $page->find('.article')->getText());
    }
}
