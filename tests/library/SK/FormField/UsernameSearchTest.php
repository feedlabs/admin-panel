<?php

class SK_FormField_UsernameSearchTest extends SKTest_TestCase {

    public function testProtectedGetSuggestions() {
        CM_Config::get()->CM_Elasticsearch_Client->enabled = true;

        $viewer = SKTest_TH::createUser(null, 'user1');
        $user = SKTest_TH::createUser(null, 'user2');
        SK_Entity_Abstract::createAggregation();

        $cli = new CM_Elasticsearch_Index_Cli();
        $type = new SK_Elasticsearch_Type_User();
        $cli->create($type->getIndex()->getName());

        $render = new CM_Frontend_Render(new CM_Frontend_Environment(null, $viewer));
        $formField = new SK_FormField_UsernameSearch();
        $method = CMTest_TH::getProtectedMethod($formField, '_getSuggestions');
        $actual = $method->invokeArgs($formField, array('', array(), $render));

        $this->assertCount(1, $actual);
        $this->assertEquals($user->getId(), $actual[0]['id']);
    }
}
