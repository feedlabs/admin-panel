<?php

class SK_Component_RecordKeepingRequirementsTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_RecordKeepingRequirements();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('.article.recordKeepingRequirements', $page->getText());
    }
}
