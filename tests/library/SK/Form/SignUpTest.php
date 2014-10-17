<?php

class SK_Form_SignUpTest extends SKTest_TestCase {

    /** @var CM_Model_Location */
    private static $_city;

    public static function setUpBeforeClass() {
        self::$_city = SKTest_TH::createLocationCity();
    }

    public function testProcess() {
        $data = $this->_getData();
        $site = CM_Site_Abstract::factory();

        $form = new SK_Form_SignUp(['site' => $site]);
        $action = new SK_FormAction_SignUp_Create($form);
        $request = $this->createRequestFormAction($action, $data);
        $request->mockMethod('getLanguage')->set(function () {
            return null;
        });
        $response = new CM_Response_View_Form($request);
        $response->process();

        $this->assertFormResponseSuccess($response);
        $user = SK_User::findUsername($data['username']);
        $this->assertNotNull($user, 'User was not created');
        $this->assertEquals($user, $response->getRequest()->getSession()->getUser());
        $this->assertEquals(array(SK_User::SEX_FEMALE,
            SK_User::SEX_COUPLE), $user->getProfile()->getFields()->get('match_sex'), 'Match Sex not filled in correctly');
        $this->assertEquals($data['email'], $user->getEmail());
        $this->assertEquals($data['sex'], $user->getSex());
        $this->assertEquals(self::$_city, $user->getLocation());
        $this->assertNull($user->getProfile()->getFields()->get('general_description'));
        $birthdateExpected = $data['birthdate']['year'] . '-0' . $data['birthdate']['month'] . '-' . $data['birthdate']['day'];
        $this->assertEquals($birthdateExpected, $user->getBirthdate()->format('Y-m-d'));
        $this->assertSame(null, $user->getLanguage());
    }

    public function testRequired() {
        $this->_testWrongFieldValue('location', null);
        $this->_testWrongFieldValue('location', CM_Model_Location::LEVEL_COUNTRY . '.13');
        $this->_testWrongFieldValue('sex', 0);
        $this->_testWrongFieldValue('birthdate', array('year' => date('Y') - 10, 'month' => 2, 'day' => 22));
        $this->_testWrongFieldValue('birthdate', array('year' => date('Y') - 200, 'month' => 2, 'day' => 22));
        $this->_testWrongFieldValue('username', SKTest_TH::createUser()->getUsername());
        $this->_testWrongFieldValue('email', SKTest_TH::createUser()->getEmail());
        $this->_testWrongFieldValue('email', 'test');
        $this->_testWrongFieldValue('password', '  ');
        $this->_testWrongFieldValue('match_sex', array());
    }

    public function testProcessSetLanguage() {
        $language = SKTest_TH::createLanguage();
        $data = $this->_getData();
        $form = new SK_Form_SignUp(['site' => CM_Site_Abstract::factory()]);
        $action = new SK_FormAction_SignUp_Create($form);
        $request = $this->createRequestFormAction($action, $data);
        $request->mockMethod('getLanguage')->set(function () use ($language) {
            return $language;
        });
        $response = new CM_Response_View_Form($request);
        $response->process();

        $this->assertFormResponseSuccess($response);
        $user = SK_User::findUsername($data['username']);
        $this->assertEquals($language, $user->getLanguage());
        $language->delete();
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    private function _testWrongFieldValue($name, $value) {
        $data = $this->_getData();
        $data[$name] = $value;
        $site = CM_Site_Abstract::factory();

        $form = new SK_Form_SignUp(['site' => $site]);
        $action = new SK_FormAction_SignUp_Create($form);
        $request = $this->createRequestFormAction($action, $data);
        $response = new CM_Response_View_Form($request);
        $response->process();

        $this->assertFormResponseError($response);
    }

    private function _getData() {
        $unique = substr(md5(rand() . uniqid()), 0, 15);
        $data = array();
        $data['email'] = $unique . '@example.com';
        $data['username'] = $unique;
        $data['password'] = 'asfdasdfas';
        $data['sex'] = SK_User::SEX_MALE;
        $data['match_sex'] = array(SK_User::SEX_FEMALE, SK_User::SEX_COUPLE);
        $data['location'] = CM_Model_Location::LEVEL_CITY . '.' . self::$_city->getId();
        $data['birthdate'] = array("year" => date('Y') - 30, "month" => "4", "day" => "24");
        return $data;
    }
}
