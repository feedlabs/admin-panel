<?php

class SK_CountryRedirectTest extends SKTest_TestCase {

    public static function setUpBeforeClass() {
    }

    public function testCreate() {
        $city = SKTest_TH::createLocationCity();
        $url = 'http://www.foo.bar';
        $country = new CM_Model_Location(CM_Model_Location::LEVEL_COUNTRY, $city->getId(CM_Model_Location::LEVEL_COUNTRY));
        /** @var SK_CountryRedirect $countryRedirect */
        $countryRedirect = SK_CountryRedirect::createStatic(array('location' => $city, 'url' => $url));
        $this->assertInstanceOf('SK_CountryRedirect', $countryRedirect);
        $this->assertRow('sk_countryRedirect', array('id' => $countryRedirect->getId(), 'countryId' => $country->getId(), 'url' => $url));
        $this->assertSame($url, $countryRedirect->getUrl());
        $this->assertEquals($country, $countryRedirect->getCountry());

        try {
            SK_CountryRedirect::createStatic(array('location' => $city, 'url' => $url));
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('CountryRedirect for country `' . $country->getName() . '` already exists.', $ex->getMessage());
        }

        $countryRedirect->delete();
    }

    public function testDelete() {
        $countryRedirect = SKTest_TH::createCountryRedirect();
        $country = $countryRedirect->getCountry();
        $this->assertEquals($countryRedirect, SK_CountryRedirect::findCountry($country));
        $countryRedirect->delete();
        try {
            SKTest_TH::reinstantiateModel($countryRedirect);
            $this->fail('CountryRedirect not deleted.');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
        $this->assertNotRow('sk_countryRedirect', array('id' => $countryRedirect->getId()));
        try {
            SK_CountryRedirect::findCountry($country);
            $this->assertTrue(true);
        } catch (CM_Exception_Nonexistent $ex) {
            $this->fail('SK_CountryRedirect::findCountry()-cache not cleared.');
        }
    }

    public function testFindCountry() {
        $country = SKTest_TH::createLocationCity()->get(CM_Model_Location::LEVEL_COUNTRY);
        $countryOther = SKTest_TH::createLocationCity()->get(CM_Model_Location::LEVEL_COUNTRY);
        $this->assertNull(SK_CountryRedirect::findCountry($country));
        $countryRedirect = SKTest_TH::createCountryRedirect($country, 'www.foo.bar');
        $this->assertEquals($countryRedirect, SK_CountryRedirect::findCountry($country));
        $this->assertNull(SK_CountryRedirect::findCountry($countryOther));
    }
}
