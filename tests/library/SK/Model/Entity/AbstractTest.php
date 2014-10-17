<?php

class SK_Model_Entity_AbstractTest extends SKTest_TestCase {

    public static function setupBeforeClass() {
        CM_Db_Db::exec("CREATE TABLE IF NOT EXISTS `entityMock` (
					`id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
					`userId` VARCHAR(32)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			");
    }

    public static function tearDownAfterClass() {
        CM_Db_Db::exec("DROP TABLE `entityMock`");
        parent::tearDownAfterClass();
    }

    public function testGetUser() {
        $user = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        SK_EntityMock::createStatic(array('userId' => $user->getId()));
        $entityMock = new SK_EntityMock(1);
        $this->assertEquals($user->getId(), $entityMock->getUser()->getId());
        $this->assertInstanceOf('CM_Model_User', $user);
        $this->assertInstanceOf('SK_User', $entityMock->getUser());
        $this->assertNotEquals($user2, $entityMock->getUser());
        CM_Db_Db::delete('cm_user', array('userId' => $user->getId()));
        SKTest_TH::clearCache();
        try {
            $entityMock->getUser();
            $this->fail('User not deleted');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }
}

class SK_EntityMock extends SK_Entity_Abstract {

    public static function getTypeStatic() {
        return 1;
    }

    protected function _loadData() {
        return CM_Db_Db::select('entityMock', array('userId'), array('id' => $this->getId()))->fetch();
    }

    protected function _onDelete() {
        CM_Db_Db::delete('entityMock', array('id' => $this->getId()));
    }

    protected static function _createStatic(array $data) {
        return new self(CM_Db_Db::insert('entityMock', array('userId' => $data['userId'])));
    }

    public function getPath() {
        throw new CM_Exception_NotImplemented();
    }

    public function getTableName() {
        return 'entityMock';
    }
}
