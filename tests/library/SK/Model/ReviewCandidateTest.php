<?php

class SK_Model_ReviewCandidateTest extends SKTest_TestCase {

    public function testCreate() {
        $entity = SKTest_TH::createPhoto();
        $reviewCandidate = SK_Model_ReviewCandidate::create($entity);
        $this->assertEquals($entity, $reviewCandidate->getEntity());
        $this->assertNull($reviewCandidate->getReserved());
        $this->assertSame(time(), $reviewCandidate->getCreated());
        $this->assertSame($entity->getUserId(), $reviewCandidate->_get('userId'));
    }

    public function testSetReserved() {
        $entity = SKTest_TH::createPhoto();
        $reviewCandidate = SK_Model_ReviewCandidate::create($entity);
        $this->assertNull($reviewCandidate->getReserved());

        $reviewCandidate->setReserved(12345);
        $this->assertSame(12345, $reviewCandidate->getReserved());
    }

    public function testExists() {
        $entity = SKTest_TH::createPhoto();
        $this->assertFalse(SK_Model_ReviewCandidate::exists($entity));

        SK_Model_ReviewCandidate::create($entity);
        $this->assertTrue(SK_Model_ReviewCandidate::exists($entity));

        $this->assertFalse(SK_Model_ReviewCandidate::exists(SKTest_TH::createPhoto()));
    }

    public function testDeleteByUser() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();

        $entity1 = SKTest_TH::createPhoto($user1);
        $entity2 = SKTest_TH::createPhoto($user2);

        SK_Model_ReviewCandidate::create($entity1);
        SK_Model_ReviewCandidate::create($entity2);

        SK_Model_ReviewCandidate::deleteByUser($user2);

        $this->assertSame(true, SK_Model_ReviewCandidate::exists($entity1));
        $this->assertSame(false, SK_Model_ReviewCandidate::exists($entity2));
    }

    public function testDeleteAll() {
        $entity1 = SKTest_TH::createPhoto();
        $entity2 = SKTest_TH::createPhoto();

        SK_Model_ReviewCandidate::create($entity1);
        SK_Model_ReviewCandidate::create($entity2);
        $this->assertSame(true, SK_Model_ReviewCandidate::exists($entity1));
        $this->assertSame(true, SK_Model_ReviewCandidate::exists($entity2));

        SK_Model_ReviewCandidate::deleteAll();

        $this->assertSame(false, SK_Model_ReviewCandidate::exists($entity1));
        $this->assertSame(false, SK_Model_ReviewCandidate::exists($entity2));
        $this->assertSame(0, (new SK_Paging_ReviewCandidate_All())->getCount());
    }
}
