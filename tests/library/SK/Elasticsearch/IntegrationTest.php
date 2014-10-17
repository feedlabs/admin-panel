<?php

class SK_Elasticsearch_IntergrationTest extends SKTest_TestCase {

    public function setUp() {
        CM_Config::get()->CM_Elasticsearch_Client->enabled = true;
    }

    public function tearDown() {
        SKTest_TH::clearEnv();
        CM_Redis_Client::getInstance()->flush();
    }

    public function testUser() {
        $this->_testRun(new SK_Elasticsearch_Type_User(), function () {
            $user = SKTest_TH::createUser();
            CM_Model_Location::createAggregation();
            return $user;
        }, function (SK_User $user) {
            $user->delete();
        });
    }

    public function testVideo() {
        $this->_testRun(new SK_Elasticsearch_Type_Video(), function () {
            return SKTest_TH::createVideo();
        }, function (SK_Entity_Video $video) {
            $video->delete();
        });
    }

    public function testPhoto() {
        $this->_testRun(new SK_Elasticsearch_Type_Photo(), function () {
            return SKTest_TH::createPhoto();
        }, function (SK_Entity_Photo $photo) {
            $photo->delete();
        });
    }

    public function testBlogpost() {
        $this->_testRun(new SK_Elasticsearch_Type_Blogpost(), function () {
            return SKTest_TH::createBlogpost();
        }, function (SK_Entity_Blogpost $blogpost) {
            $blogpost->delete();
        });
    }

    public function testPinboard() {
        $this->_testRun(new SK_Elasticsearch_Type_Pinboard(), function () {
            return SKTest_TH::createPinboard();
        }, function (SK_Entity_Pinboard $pinboard) {
            $pinboard->delete();
        });
    }

    private function _testRun(CM_Elasticsearch_Type_Abstract $type, Closure $create, Closure $delete) {
        $objects = array();

        // Create
        for ($i = 0; $i < 5; $i++) {
            $objects[] = $create();
        }
        $searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $searchIndexCli->create($type->getIndex()->getName());
        $type->getIndex()->refresh();
        $this->assertEquals(count($objects), $type->getType()->count());

        // Update
        for ($i = 0; $i < 2; $i++) {
            $objects[] = $create();
        }
        $searchIndexCli->update($type->getIndex()->getName());
        $type->getIndex()->refresh();
        $this->assertEquals(count($objects), $type->getType()->count());

        // Search
        foreach ($objects as $object) {
            $id = $type::getIdForItem($object);
            $filter = new Elastica\Filter\Ids($type->getType(), array($id));
            $resultSet = $type->getType()->search($filter);
            $this->assertEquals(1, $resultSet->count());
            $this->assertEquals($id, $resultSet->current()->getId());
        }

        // Delete
        for ($i = 0; $i < 2; $i++) {
            $delete(array_pop($objects));
        }
        $searchIndexCli->update($type->getIndex()->getName());
        $type->getIndex()->refresh();
        $this->assertEquals(count($objects), $type->getType()->count());

        // Cleanup
        $type->getIndex()->delete();
        while ($object = array_pop($objects)) {
            $delete($object);
        }
    }

    public function testFailingUpdate() {
        $index = new SK_Elasticsearch_Type_User();
        $objects = array();
        $searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $searchIndexCli->create($index->getIndex()->getName());
        for ($i = 0; $i < 5; $i++) {
            $objects[] = SKTest_TH::createUser();
        }
        CM_Model_Location::createAggregation();

        // Invalid ES-connection
        try {
            $searchIndexCli->update($index->getIndex()->getName(), 'localhost', 9999);
            $this->fail('search-index update with invalid connection did succeed');
        } catch (CM_Exception $e) {
            $this->assertContains('test_user-updates failed.', $e->getMessage());
            $this->assertContains('Re-adding 5 ids to queue.', $e->getMessage());
            $this->assertContains('Couldn\'t connect to host, Elasticsearch down?', $e->getMessage());
        }
        $index->getIndex()->refresh();
        $this->assertEquals(0, $index->getType()->count());

        // Valid ES-connection
        $searchIndexCli->update($index->getIndex()->getName());
        $index->getIndex()->refresh();
        $this->assertEquals(count($objects), $index->getType()->count());

        // Cleanup
        $index->getIndex()->delete();
    }

    public function testReadOnlyIndex() {
        $index = new SK_Elasticsearch_Type_User();
        $objects = array();
        $searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $searchIndexCli->create($index->getIndex()->getName());
        for ($i = 0; $i < 5; $i++) {
            $objects[] = SKTest_TH::createUser();
        }
        CM_Model_Location::createAggregation();

        // Read-only
        $index->getIndex()->getSettings()->setReadOnly(true);
        try {
            $searchIndexCli->update($index->getIndex()->getName());
            $this->fail('search-index update with invalid connection did succeed');
        } catch (CM_Exception $e) {
            $this->assertContains('test_user-updates failed.', $e->getMessage());
            $this->assertContains('Re-adding 5 ids to queue.', $e->getMessage());
            $this->assertContains('Error in one or more bulk request actions', $e->getMessage());
        }
        $index->getIndex()->getSettings()->setReadOnly(false);
        $index->getIndex()->refresh();
        $this->assertEquals(0, $index->getType()->count());

        // Read-write
        $searchIndexCli->update($index->getIndex()->getName());
        $index->getIndex()->refresh();
        $this->assertEquals(count($objects), $index->getType()->count());

        // Cleanup
        $index->getIndex()->delete();
    }
}
