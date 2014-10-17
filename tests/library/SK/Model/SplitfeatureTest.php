<?php

class SK_Model_SplitfeatureTest extends SKTest_TestCase {

    public function testGetEnabled() {
        CM_Config::get()->CM_Model_Splitfeature->withoutPersistence = false;
        CM_Model_Splitfeature::createStatic(array('name' => 'foo', 'percentage' => 0));

        $user = CMTest_TH::createUser();
        $splitfeature = new SK_Model_Splitfeature('foo');
        $this->assertFalse($splitfeature->getEnabled($user));

        $user->getRoles()->add(SK_Role::DEVELOPER);
        $this->assertTrue($splitfeature->getEnabled($user));
    }
}
