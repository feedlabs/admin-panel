<?php

class SK_Entertainment_MessageSetTest extends SKTest_TestCase {

    public function testCreate() {
        /** @var SK_Entertainment_MessageSet $messageSet */
        $messageSet = SK_Entertainment_MessageSet::createStatic(array('description' => 'foo'));
        $this->assertInstanceOf('SK_Entertainment_MessageSet', $messageSet);
        $this->assertSame('foo', $messageSet->getDescription());
    }

    public function testDelete() {
        $messageSet = SK_Entertainment_MessageSet::createStatic(array('description' => null));
        $message = SKTest_TH::createEntertainmentMessage($messageSet);

        $messageSet->delete();
        try {
            SKTest_TH::reinstantiateModel($messageSet);
            $this->fail("Could reinstantiate deleted messageSet.");
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
        try {
            SKTest_TH::reinstantiateModel($message);
            $this->fail("Could reinstantiate deleted entertainment-message.");
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }

    public function testSetDescription() {
        /** @var SK_Entertainment_MessageSet $messageSet */
        $messageSet = SK_Entertainment_MessageSet::createStatic(array('description' => 'foo'));
        $this->assertSame('foo', $messageSet->getDescription());

        $messageSet->setDescription('bar');

        $this->assertSame('bar', $messageSet->getDescription());
    }
}
