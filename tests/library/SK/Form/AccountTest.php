<?php

class SK_Form_AccountTest extends SKTest_TestCase {

    /** @var SK_User */
    private $_user1;

    /** @var SK_User */
    private $_user2;

    /** @var CM_Model_Location */
    private $_city;

    /** @var array */
    private $_data;

    public function setUp() {
        $this->_city = SKTest_TH::createLocationCity();
        $this->_user1 = SKTest_TH::createUser();
        $this->_user2 = SKTest_TH::createUser();
        $this->_user1->setBirthdate(new DateTime('1988-04-04'));
        $this->_data = array(
            'username'  => 'blahma',
            'email'     => 'ayayayay@asd.com',
            'birthdate' => array('year' => '1988', 'month' => '11', 'day' => '16'),
            'location'  => CM_Model_Location::LEVEL_CITY . '.' . $this->_city->getId(),
        );
    }

    public function testProcess() {
        $data = $this->_data;
        $response = $this->_processSaveAction($data);
        $user = new SK_User($this->_user1->getId());
        $this->assertSame('blahma', $user->getUsername());
        $this->assertSame('ayayayay@asd.com', $user->getEmail());
        $this->assertSame(implode('-', $data['birthdate']), $user->getBirthdate()->format('Y-m-d'));
        $this->assertEquals($this->_city, $user->getLocation());
        $this->assertFormResponseSuccess($response);
    }

    public function testProcessExistentUsername() {
        $data = $this->_data;
        $data['username'] = $this->_user2->getUsername();
        $data['email'] = $this->_user1->getEmail();
        $response = $this->_processSaveAction($data);
        $this->assertFormResponseError($response, 'Username already taken', 'username');
    }

    public function testProcessExistentEmail() {
        $data = $this->_data;
        $data['username'] = $this->_user1->getUsername();
        $data['email'] = $this->_user2->getEmail();
        $response = $this->_processSaveAction($data);
        $this->assertFormResponseError($response, 'This email address is already used.', 'email');
    }

    public function testProcessEmailChange() {
        $this->_user1->setEmailVerified(true);
        $this->assertTrue($this->_user1->getEmailVerified());
        $data = $this->_data;
        $data['username'] = $this->_user1->getUsername();
        $data['email'] = time() . '+' . $this->_user1->getEmail();
        $this->_processSaveAction($data);
        SKTest_TH::reinstantiateModel($this->_user1);
        $this->assertFalse($this->_user1->getEmailVerified());
    }

    /**
     * @param array   $data
     * @return CM_Response_View_Form|\Mocka\ClassTrait
     */
    private function _processSaveAction(array $data) {
        $form = new SK_Form_Account();
        $formAction = new SK_FormAction_Account_Save($form);
        $request = $this->createRequestFormAction($formAction, $data);
        $request->mockMethod('getViewer')->set($this->_user1);
        return $this->processRequest($request);
    }
}
