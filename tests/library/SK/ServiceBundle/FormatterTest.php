<?php

class SK_ServiceBundle_FormatterTest extends SKTest_TestCase {

    public static function setUpBeforeClass() {
        /** @var CM_Model_Language $language */
        foreach (new CM_Paging_Language_All() as $language) {
            $language->delete();
        }
    }

    public function testGetLifetime() {
        $render = new CM_Frontend_Render();

        $serviceBundle = SKTest_TH::createServiceBundle(199.95, 3650);
        $this->assertSame('VIP Lifetime Membership', $serviceBundle->getFormatter($render)->getLifetime());

        $serviceBundle = SKTest_TH::createServiceBundle(99.95, 365, 99.95, 365);
        $this->assertNull($serviceBundle->getFormatter($render)->getLifetime());
    }

    public function testGetPriceInitial() {
        $render = new CM_Frontend_Render();

        $serviceBundle = SKTest_TH::createServiceBundle(99.95, 365, 99.95, 365);
        $this->assertSame('<span class="currency">$</span><span class="price-before">99</span><span class="price-separator">.</span><span class="price-after">95</span>', $serviceBundle->getFormatter($render)->getPriceInitial());

        $serviceBundle = SKTest_TH::createServiceBundle(4.95, 3, 29.95, 30);
        $this->assertSame('<span class="currency">$</span><span class="price-before">4</span><span class="price-separator">.</span><span class="price-after">95</span>', $serviceBundle->getFormatter($render)->getPriceInitial());

        $serviceBundle = SKTest_TH::createServiceBundle(29.95, 30, 19.95, 30);
        $this->assertSame('<span class="currency">$</span><span class="price-before">29</span><span class="price-separator">.</span><span class="price-after">95</span>', $serviceBundle->getFormatter($render)->getPriceInitial());

        $serviceBundle = SKTest_TH::createServiceBundle(30, 30, 20, 30);
        $this->assertSame('<span class="currency">$</span><span class="price-before">30</span><span class="price-separator">.</span><span class="price-after">00</span>', $serviceBundle->getFormatter($render)->getPriceInitial());
    }

    public function testGetPrice() {
        $serviceBundle = SKTest_TH::createServiceBundle(99.95, 365, 99.95, 365);

        $languageAbbreviationList = array('en', 'de', 'sv', 'es', 'fr', 'it', 'zh', 'ja', 'pt', 'nl', 'ru');
        foreach ($languageAbbreviationList as $languageAbbreviation) {
            $language = SKTest_TH::createLanguage($languageAbbreviation);
            $render = new CM_Frontend_Render(new CM_Frontend_Environment(null, null, $language));
            $price = new CM_Dom_NodeList($serviceBundle->getFormatter($render)->getPriceInitial(), true);
            $this->assertSame(1, $price->find('.currency')->count());
            $this->assertSame(1, $price->find('.price-before')->count());
            $this->assertSame(1, $price->find('.price-separator')->count());
            $this->assertSame(1, $price->find('.price-after')->count());
            $language->delete();
        }
    }

    public function testGetPriceInitialMonthly() {
        $render = new CM_Frontend_Render();

        $serviceBundle = SKTest_TH::createServiceBundle(199.95, 3650);
        $this->assertNull($serviceBundle->getFormatter($render)->getPriceInitialMonthly());

        $serviceBundle = SKTest_TH::createServiceBundle(99.95, 365, 99.95, 365);
        $this->assertSame(8.3291666666667, $serviceBundle->getFormatter($render)->getPriceInitialMonthly());

        $serviceBundle = SKTest_TH::createServiceBundle(4.95, 3, 29.95, 30);
        $this->assertNull($serviceBundle->getFormatter($render)->getPriceInitialMonthly());

        $serviceBundle = SKTest_TH::createServiceBundle(29.95, 30, 19.95, 30);
        $this->assertSame(29.95, $serviceBundle->getFormatter($render)->getPriceInitialMonthly());
    }

    public function testGetPriceInitialOld() {
        $render = new CM_Frontend_Render();

        $serviceBundle = SKTest_TH::createServiceBundle(99.95, 365, 99.95, 365);
        $this->assertSame('$139.95', $serviceBundle->getFormatter($render)->getPriceInitialOld());

        $serviceBundle = SKTest_TH::createServiceBundle(4.95, 3, 29.95, 30);
        $this->assertSame('$7.95', $serviceBundle->getFormatter($render)->getPriceInitialOld());

        $serviceBundle = SKTest_TH::createServiceBundle(29.95, 30, 19.95, 30);
        $this->assertSame('$39.95', $serviceBundle->getFormatter($render)->getPriceInitialOld());
    }

    public function testGetRecurring() {
        $render = new CM_Frontend_Render();

        $serviceBundle = SKTest_TH::createServiceBundle(99.95, 365, 99.95, 365);
        $this->assertSame('<span class="currency">$</span><span class="price-before">99</span><span class="price-separator">.</span><span class="price-after">95</span>', $serviceBundle->getFormatter($render)->getPriceInitial());

        $serviceBundle = SKTest_TH::createServiceBundle(4.95, 3, 29.95, 30);
        $this->assertSame('<span class="currency">$</span><span class="price-before">4</span><span class="price-separator">.</span><span class="price-after">95</span>', $serviceBundle->getFormatter($render)->getPriceInitial());

        $serviceBundle = SKTest_TH::createServiceBundle(29.95, 30, 19.95, 30);
        $this->assertSame('<span class="currency">$</span><span class="price-before">29</span><span class="price-separator">.</span><span class="price-after">95</span>', $serviceBundle->getFormatter($render)->getPriceInitial());

        $serviceBundle = SKTest_TH::createServiceBundle(30, 30, 20, 30);
        $this->assertSame('<span class="currency">$</span><span class="price-before">30</span><span class="price-separator">.</span><span class="price-after">00</span>', $serviceBundle->getFormatter($render)->getPriceInitial());
    }

    public function testGetRecurringExplicit() {
        $language = SKTest_TH::createLanguage('en');
        $language->setTranslation('.date.period.everyDays', 'every {$count} days', array('count'));
        $render = new CM_Frontend_Render(new CM_Frontend_Environment(null, null, $language));

        $serviceBundle = SKTest_TH::createServiceBundle(199.95, 3650);
        $this->assertNull($serviceBundle->getFormatter($render)->getRecurringExplicit());

        $serviceBundle = SKTest_TH::createServiceBundle(99.95, 365, 99.95, 365);
        $this->assertSame('Billed $99.95 every 365 days', $serviceBundle->getFormatter($render)->getRecurringExplicit());

        $serviceBundle = SKTest_TH::createServiceBundle(4.95, 3, 29.95, 30);
        $this->assertSame('Billed thereafter $29.95 every 30 days', $serviceBundle->getFormatter($render)->getRecurringExplicit());

        $serviceBundle = SKTest_TH::createServiceBundle(29.95, 30, 19.95, 30);
        $this->assertSame('Billed thereafter $19.95 every 30 days', $serviceBundle->getFormatter($render)->getRecurringExplicit());

        $serviceBundle = SKTest_TH::createServiceBundle(99.95, 365, 99.95, 365);
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Premium(365));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Premium(365));
        $this->assertSame('Billed $99.95 every 365 days', $serviceBundle->getFormatter($render)->getRecurringExplicit());

        $serviceBundle = SKTest_TH::createServiceBundle(4.95, 3, 29.95, 30);
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Premium(3));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Premium(30));
        $this->assertSame('Billed thereafter $29.95 every 30 days', $serviceBundle->getFormatter($render)->getRecurringExplicit());

        $serviceBundle = SKTest_TH::createServiceBundle(29.95, 30, 19.95, 30);
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Premium(30));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Premium(30));
        $this->assertSame('Billed thereafter $19.95 every 30 days', $serviceBundle->getFormatter($render)->getRecurringExplicit());

        $serviceBundle = SKTest_TH::createServiceBundle(49.95, 30, 49.95, 90);
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Premium(30));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Premium(90));
        $this->assertSame('Billed thereafter $49.95 every 90 days', $serviceBundle->getFormatter($render)->getRecurringExplicit());

        $serviceBundle = SKTest_TH::createServiceBundle(99.95, 365, 99.95, 365);
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Premium(365));
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Coin(100));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Premium(365));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Coin(100));
        $this->assertSame('Billed $99.95 every 365 days', $serviceBundle->getFormatter($render)->getRecurringExplicit());

        $serviceBundle = SKTest_TH::createServiceBundle(4.95, 3, 29.95, 30);
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Premium(3));
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Coin(1));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Premium(30));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Coin(10));
        $this->assertSame('Billed thereafter $29.95 every 30 days', $serviceBundle->getFormatter($render)->getRecurringExplicit());

        $serviceBundle = SKTest_TH::createServiceBundle(29.95, 30, 19.95, 30);
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Premium(30));
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Coin(10));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Premium(30));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Coin(10));
        $this->assertSame('Billed thereafter $19.95 every 30 days', $serviceBundle->getFormatter($render)->getRecurringExplicit());

        $serviceBundle = SKTest_TH::createServiceBundle(49.95, 30, 49.95, 90);
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Premium(30));
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Coin(10));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Premium(90));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Coin(30));
        $this->assertSame('Billed thereafter $49.95 every 90 days', $serviceBundle->getFormatter($render)->getRecurringExplicit());

        $language->delete();
    }

    public function testGetServicesInitial() {
        $render = new CM_Frontend_Render();

        $serviceBundle = SKTest_TH::createServiceBundle(49.95, 30, 49.95, 90);
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Premium(30));
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Coin(10));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Premium(90));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Coin(30));

        $this->assertSame(array(
            '.date.period.month',
            '10 Coins',
        ), $serviceBundle->getFormatter($render)->getServicesInitial());

        $this->assertSame(array(
            '.date.period.month Premium Membership',
            '10 Coins',
        ), $serviceBundle->getFormatter($render)->getServicesInitial(true));
    }

    public function testGetServicesRecurring() {
        $render = new CM_Frontend_Render();

        $serviceBundle = SKTest_TH::createServiceBundle(49.95, 30, 49.95, 90);
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Premium(30));
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Coin(10));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Premium(90));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Coin(30));

        $this->assertSame(array(
            '.date.period.months',
            '30 Coins',
        ), $serviceBundle->getFormatter($render)->getServicesRecurring());

        $this->assertSame(array(
            '.date.period.months Premium Membership',
            '30 Coins',
        ), $serviceBundle->getFormatter($render)->getServicesRecurring(true));
    }

    public function testGetSummary() {
        $render = new CM_Frontend_Render();

        $serviceBundle = SKTest_TH::createServiceBundle(199.95, 3650);
        $this->assertSame('[Initial 3650d/$199.95]', $serviceBundle->getFormatter($render)->getSummary());

        $serviceBundle = SKTest_TH::createServiceBundle(99.95, 365, 99.95, 365);
        $this->assertSame('[Initial 365d/$99.95] – [Recurring 365d/$99.95]', $serviceBundle->getFormatter($render)->getSummary());

        $serviceBundle = SKTest_TH::createServiceBundle(4.95, 3, 29.95, 30);
        $this->assertSame('[Initial 3d/$4.95] – [Recurring 30d/$29.95]', $serviceBundle->getFormatter($render)->getSummary());

        $serviceBundle = SKTest_TH::createServiceBundle(29.95, 30, 19.95, 30);
        $this->assertSame('[Initial 30d/$29.95] – [Recurring 30d/$19.95]', $serviceBundle->getFormatter($render)->getSummary());

        $serviceBundle = SKTest_TH::createServiceBundle(199.95, 3650);
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Premium(3650));
        $this->assertSame('[Initial 3650d/$199.95] 3650x.internals.service.1', $serviceBundle->getFormatter($render)->getSummary());

        $serviceBundle = SKTest_TH::createServiceBundle(49.95, 30, 49.95, 90);
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Premium(30));
        $serviceBundle->getInitialServicesAll()->add(new SK_Service_Coin(10));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Premium(90));
        $serviceBundle->getRecurringServicesAll()->add(new SK_Service_Coin(30));
        $this->assertSame('[Initial 30d/$49.95] 30x.internals.service.1, 10x.internals.service.2 – [Recurring 90d/$49.95] 90x.internals.service.1, 30x.internals.service.2', $serviceBundle->getFormatter($render)->getSummary());
    }
}
