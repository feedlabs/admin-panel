<?php

class SK_AffiliateProvider_UserTemplateTest extends SKTest_TestCase {

    protected function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testProvider() {
        $provider = new SK_AffiliateProvider_UserTemplate();
        $provider->setName('foo');
        $provider->setQueryParam('bar');

        $this->assertSame(SK_AffiliateProvider_UserTemplate::getTypeStatic(), $provider->getId());
        $this->assertSame('foo', $provider->getName());
        $this->assertSame('bar', $provider->getQueryParam());
    }
}
