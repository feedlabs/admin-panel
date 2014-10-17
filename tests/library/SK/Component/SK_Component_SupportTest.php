<?php

class SK_Component_SupportTest extends SKTest_TestCase {

    public function testDefault() {
        $cmp = new SK_Component_Support();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('Need help?', $page->find('h2')->getText());
        $this->assertTrue($page->has('h2 > span.icon.icon-question'));
        $this->assertSame('Live Chat Support', $page->find('button .label')->getText());
        $this->assertContains('Other ways to contact us', $page->find('.text')->getText());
        $this->assertFalse($page->has('.phone'));
    }

    public function testCustomHeader() {
        $cmp = new SK_Component_Support(array('headerText' => 'FooBar'));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('FooBar', $page->find('h2')->getText());
        $this->assertTrue($page->has('h2 > span.icon.icon-question'));
        $this->assertSame('Live Chat Support', $page->find('button .label')->getText());
        $this->assertContains('Other ways to contact us', $page->find('.text')->getText());
        $this->assertFalse($page->has('.phone'));
    }

    public function testCustomHeaderWithoutIcon() {
        $cmp = new SK_Component_Support(array('headerText' => 'FooBar', 'showHeaderIcon' => false));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('FooBar', $page->find('h2')->getText());
        $this->assertFalse($page->has('h2 > span.icon.icon-question'));
        $this->assertSame('Live Chat Support', $page->find('button .label')->getText());
        $this->assertContains('Other ways to contact us', $page->find('.text')->getText());
        $this->assertFalse($page->has('.phone'));
    }

    public function testCustomHeaderWithoutIconAndShowPhoneNumbers() {
        $cmp = new SK_Component_Support(array('headerText' => 'FooBar', 'showHeaderIcon' => false, 'showPhoneNumbers' => true));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('FooBar', $page->find('h2')->getText());
        $this->assertFalse($page->has('h2 > span.icon.icon-question'));
        $this->assertSame('Live Chat Support', $page->find('button .label')->getText());
        $this->assertFalse($page->has('.text'));
        $this->assertContains('Or call our support by phone 844-543-8893', $page->find('.phone')->getText());
        $this->assertContains('Not in North America? +1-810-553-6150', $page->find('.phone')->getText());
    }

    public function testRedirectToSupportPage() {
        $cmp = new SK_Component_Support(array('liveChatEnabled' => false));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertContains('Need help?', $page->find('h2')->getText());
        $this->assertTrue($page->has('h2 > span.icon.icon-question'));
        $this->assertSame('Contact us', $page->find('button .label')->getText());
        $this->assertNotContains('Other ways to contact us', $page->find('.text')->getText());
        $this->assertFalse($page->has('.phone'));
    }
}
