<?php

class SK_ModelAsset_Entity_Profile_FieldListTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testGetSet() {
        $profile = SKTest_TH::createUser()->getProfile();
        $fields = $profile->getFields();

        $this->assertNotRow('sk_entity_profile', array('id' => $profile->getId()));

        $fields->set('match_education', array(1, 2, 8, 32));
        $this->assertEquals(array(1, 2, 8, 32), $fields->get('match_education'));

        try {
            $fields->set('match_education', 0);
            $this->fail('should throw an exception');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('Invalid value `0` for multicheckbox', $ex->getMessage());
        }
        try {
            $fields->set('key_that_does_not_exist', 123);
            $this->fail('should throw an exception');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('No such profile field `key_that_does_not_exist`', $ex->getMessage());
        }
        try {
            $fields->get('key_that_does_not_exist');
            $this->fail('should throw an exception');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('No such profile field `key_that_does_not_exist`', $ex->getMessage());
        }
    }

    public function testGetSetScalar() {
        $profile = SKTest_TH::createUser()->getProfile();
        $fields = $profile->getFields();

        $this->assertNotRow('sk_entity_profile', array('id' => $profile->getId()));
        $this->assertNull($fields->getScalar('general_description'));
        $fields->setScalar('general_description', 'test1');
        $this->assertRow('sk_entity_profile', array('id' => $profile->getId()));
        $this->assertEquals('test1', $fields->getScalar('general_description'));
        $this->assertRow('sk_entity_profile', array(
            'id'     => $profile->getId(),
            'fields' => json_encode(array(
                'general_description' => 'test1',
            )),
        ));

        $fields->setScalar('religion', 3);
        $this->assertEquals(null, $fields->getScalar('religion'));
        $this->assertRow('sk_entity_profile', array(
            'id'     => $profile->getId(),
            'fields' => json_encode(array(
                'general_description' => 'test1',
            )),
        ));

        $fields->setScalar('height', 16);
        $this->assertEquals(null, $fields->getScalar('height'));
        $this->assertRow('sk_entity_profile', array(
            'id'     => $profile->getId(),
            'fields' => json_encode(array(
                'general_description' => 'test1',
            )),
        ));

        $fields->setScalar('match_education', 0);
        $this->assertEquals(null, $fields->getScalar('match_education'));
        $this->assertRow('sk_entity_profile', array(
            'id'     => $profile->getId(),
            'fields' => json_encode(array(
                'general_description' => 'test1',
            )),
        ));

        $fields->setScalar('match_education', 8);
        $this->assertEquals(8, $fields->getScalar('match_education'));
        $this->assertRow('sk_entity_profile', array(
            'id'     => $profile->getId(),
            'fields' => json_encode(array(
                'general_description' => 'test1',
                'match_education'     => 8,
            )),
        ));

        $fields->setScalar('my_homepage', 'www.blabla.com');
        $this->assertRow('sk_entity_profile', array(
            'id'     => $profile->getId(),
            'fields' => json_encode(array(
                'general_description' => 'test1',
                'match_education'     => 8,
                'my_homepage'         => 'www.blabla.com',
            )),
        ));

        $fields->setScalar('my_homepage', null);
        $this->assertNull($fields->getScalar('my_homepage'));
        $this->assertRow('sk_entity_profile', array(
            'id'     => $profile->getId(),
            'fields' => json_encode(array(
                'general_description' => 'test1',
                'match_education'     => 8,
            )),
        ));

        $fields->setScalar('match_education', 256);
        $this->assertEquals(null, $fields->getScalar('match_education'));
        $this->assertRow('sk_entity_profile', array(
            'id'     => $profile->getId(),
            'fields' => json_encode(array(
                'general_description' => 'test1',
            )),
        ));

        try {
            $fields->setScalar('key_that_does_not_exist', 123);
            $this->fail('should throw an exception');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('No such profile field `key_that_does_not_exist`', $ex->getMessage());
        }
        try {
            $fields->setScalar('my_homepage', str_repeat('a', SK_ModelAsset_Entity_Profile_FieldList::VALUES_MAX_LENGTH));
            $this->fail('should throw an exception');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('Profile field values length exceeds maximum', $ex->getMessage());
        }
        try {
            $fields->getScalar('key_that_does_not_exist');
            $this->fail('should throw an exception');
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('No such profile field `key_that_does_not_exist`', $ex->getMessage());
        }
    }

    public function testOnModelDelete() {
        $profile = SKTest_TH::createUser()->getProfile();
        $profile->getFields()->set('my_homepage', 'www.blabla.com');

        $this->assertRow('sk_entity_profile', array('id' => $profile->getUserId()));
        $profile->delete();

        $this->assertNotRow('sk_entity_profile', array('id' => $profile->getUserId()));
    }
}
