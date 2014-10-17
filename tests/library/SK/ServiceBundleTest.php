<?php

class SK_ServiceBundleTest extends SKTest_TestCase {

    public function setUp() {
        CM_Db_Db::insert('sk_serviceBundle', array(
            'id'              => 99999,
            'price'           => 213,
            'period'          => 312,
            'recurringPrice'  => 123,
            'recurringPeriod' => 321,
            'lifetimeAmount'  => 66.66,
        ));
    }

    public function tearDown() {
        CM_Db_Db::delete('sk_serviceBundle', array('id' => 99999));
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $serviceBundle = SKTest_TH::createServiceBundle(12.3, 6, 45.24, 23, 98.76);
        $this->assertEquals(12.3, $serviceBundle->getPrice());
        $this->assertEquals(6, $serviceBundle->getPeriod());
        $this->assertEquals(45.24, $serviceBundle->getRecurringPrice());
        $this->assertEquals(23, $serviceBundle->getRecurringPeriod());
        $this->assertSame(98.76, $serviceBundle->getLifetimeAmount());
        CM_Db_Db::delete('sk_serviceBundle', array('id' => $serviceBundle->getId()));
        $serviceBundle = SKTest_TH::createServiceBundle(12.3, 6);
        $this->assertEquals(6, $serviceBundle->getPeriod());
        $this->assertNull($serviceBundle->getRecurringPrice());
        $this->assertNull($serviceBundle->getRecurringPeriod());
        CM_Db_Db::delete('sk_serviceBundle', array('id' => $serviceBundle->getId()));
    }

    public function testCreateWithoutPeriod() {
        $price = 12.3;
        $data = array('price' => $price);
        $serviceBundle = SK_ServiceBundle::createStatic($data);
        $this->assertNull($serviceBundle->getPeriod());
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage Initial period missing
     */
    public function testCreateWithoutPeriodRecurring() {
        $price = 12.3;
        $recurringPrice = 45.24;
        $recurringPeriod = 23;
        $data = array('price' => $price, 'recurringPrice' => $recurringPrice, 'recurringPeriod' => $recurringPeriod);
        SK_ServiceBundle::createStatic($data);
    }

    public function testSetData() {
        $serviceBundle = new SK_ServiceBundle(99999);
        $this->assertEquals(213, $serviceBundle->getPrice());
        $this->assertEquals(312, $serviceBundle->getPeriod());
        $this->assertEquals(123, $serviceBundle->getRecurringPrice());
        $this->assertEquals(321, $serviceBundle->getRecurringPeriod());
        $this->assertSame(66.66, $serviceBundle->getLifetimeAmount());
        $serviceBundle->setData(123.54, 333, '', 32, null);
        $this->assertEquals(123.54, $serviceBundle->getPrice());
        $this->assertEquals(333, $serviceBundle->getPeriod());
        $this->assertNull($serviceBundle->getRecurringPrice());
        $this->assertEquals(32, $serviceBundle->getRecurringPeriod());
        $this->assertSame(0.0, $serviceBundle->getLifetimeAmount());
        $serviceBundle = new SK_ServiceBundle(99999);
        $this->assertEquals(123.54, $serviceBundle->getPrice());
        $this->assertEquals(333, $serviceBundle->getPeriod());
        $this->assertNull($serviceBundle->getRecurringPrice());
    }

    public function testUnsetPeriod() {
        $price = 12.3;
        $period = 6;
        $data = array('price' => $price, 'period' => $period);
        $serviceBundle = SK_ServiceBundle::createStatic($data);
        $this->assertSame($period, $serviceBundle->getPeriod());
        $serviceBundle->setData($price, null);
        $this->assertNull($serviceBundle->getPeriod());
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage Initial period missing
     */
    public function testUnsetPeriodRecurring() {
        $price = 12.3;
        $period = 6;
        $recurringPrice = 45.24;
        $recurringPeriod = 23;
        $data = array('price' => $price, 'period' => $period, 'recurringPrice' => $recurringPrice, 'recurringPeriod' => $recurringPeriod);
        $serviceBundle = SK_ServiceBundle::createStatic($data);
        $this->assertSame($period, $serviceBundle->getPeriod());
        $serviceBundle->setData($price, null, $recurringPrice, $recurringPeriod);
    }

    public function testConstructor() {
        $serviceBundle = new SK_ServiceBundle(99999);
        $this->assertEquals(213, $serviceBundle->getPrice());
        $this->assertEquals(312, $serviceBundle->getPeriod());
        $this->assertEquals(123, $serviceBundle->getRecurringPrice());
        $this->assertEquals(321, $serviceBundle->getRecurringPeriod());
        $this->assertSame(66.66, $serviceBundle->getLifetimeAmount());
    }

    public function testLifetimeAmount() {
        $serviceBundle = SKTest_TH::createServiceBundle();
        $this->assertSame(0.00, $serviceBundle->getLifetimeAmount());
        $serviceBundle->setLifetimeAmount(7);
        $this->assertSame(7.00, $serviceBundle->getLifetimeAmount());
    }

    public function testFindByData() {
        $price = 12.3;
        $period = 6;
        $priceRecurring = 45.67;
        $periodRecurring = 23;
        $serviceBundle = SKTest_TH::createServiceBundle($price, $period, $priceRecurring, $periodRecurring);
        $serviceCoinInitial = new SK_Service_Coin(10);
        $serviceCoinRecurring = new SK_Service_Coin(20);
        $servicePremiumInitial = new SK_Service_Premium(30);
        $servicePremiumRecurring = new SK_Service_Premium(40);

        $serviceBundle->getInitialServices()->add($serviceCoinInitial);
        $serviceBundle->getInitialServices()->add($servicePremiumInitial);

        $serviceBundle->getRecurringServices()->add($serviceCoinRecurring);
        $serviceBundle->getRecurringServices()->add($servicePremiumRecurring);

        $this->assertEquals($serviceBundle, SK_ServiceBundle::findByData($price, $period, array($serviceCoinInitial, $servicePremiumInitial),
            $priceRecurring, $periodRecurring, array($serviceCoinRecurring, $servicePremiumRecurring)));
        $this->assertNull(SK_ServiceBundle::findByData($price, $period, array($serviceCoinInitial, $servicePremiumInitial),
            $priceRecurring, $periodRecurring, array($serviceCoinRecurring)));
        $this->assertNull(SK_ServiceBundle::findByData($price, $period, array($serviceCoinInitial, $servicePremiumInitial),
            $priceRecurring, $periodRecurring, array($serviceCoinRecurring, new SK_Service_Premium(99))));
        $this->assertNull(SK_ServiceBundle::findByData($price, $period, array($serviceCoinInitial),
            $priceRecurring, $periodRecurring, array($serviceCoinRecurring, $servicePremiumRecurring)));
        $this->assertNull(SK_ServiceBundle::findByData(99.99, $period, array($serviceCoinInitial, $servicePremiumInitial),
            $priceRecurring, $periodRecurring, array($serviceCoinRecurring, $servicePremiumRecurring)));
        $this->assertNull(SK_ServiceBundle::findByData($price, 99, array($serviceCoinInitial, $servicePremiumInitial),
            $priceRecurring, $periodRecurring, array($serviceCoinRecurring, $servicePremiumRecurring)));
        $this->assertNull(SK_ServiceBundle::findByData($price, $period, array($serviceCoinInitial, $servicePremiumInitial),
            99.99, $periodRecurring, array($serviceCoinRecurring, $servicePremiumRecurring)));
        $this->assertNull(SK_ServiceBundle::findByData($price, $period, array($serviceCoinInitial, $servicePremiumInitial),
            $priceRecurring, 99, array($serviceCoinRecurring, $servicePremiumRecurring)));
    }

    public function testFindByDataSingleBilling() {
        $price = 12.3;
        $period = 6;
        $serviceBundle = SKTest_TH::createServiceBundle($price, $period);
        $serviceCoinInitial = new SK_Service_Coin(10);
        $servicePremiumInitial = new SK_Service_Premium(30);

        $serviceBundle->getInitialServices()->add($serviceCoinInitial);
        $serviceBundle->getInitialServices()->add($servicePremiumInitial);

        $this->assertEquals($serviceBundle, SK_ServiceBundle::findByData($price, $period, array($serviceCoinInitial, $servicePremiumInitial)));
    }

    /**
     * @expectedException CM_Exception_Invalid
     */
    public function testFindByDataWithoutInitialService() {
        $price = 12.3;
        $period = 6;
        $priceRecurring = 45.67;
        $periodRecurring = 23;
        $serviceBundle = SKTest_TH::createServiceBundle($price, $period, $priceRecurring, $periodRecurring);
        $this->assertEquals($serviceBundle, SK_ServiceBundle::findByData($price, $period, array(), $priceRecurring, $periodRecurring, array()));
    }
}
