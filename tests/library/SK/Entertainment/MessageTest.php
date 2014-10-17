<?php

class SK_Entertainment_MessageTest extends SKTest_TestCase {

    public function testCreate() {
        $messageSet = SK_Entertainment_MessageSet::createStatic(array('description' => null));
        /** @var SK_Entertainment_Message $message */
        $message = SK_Entertainment_Message::createStatic(array('set' => $messageSet, 'body' => 'bar'));
        $this->assertInstanceOf('SK_Entertainment_Message', $message);
        $this->assertEquals($messageSet, $message->getSet());
        $this->assertSame('bar', $message->getBody());
    }

    public function testDelete() {
        $message = SKTest_TH::createEntertainmentMessage();

        $message->delete();

        try {
            SKTest_TH::reinstantiateModel($message);
            $this->fail('Could reinstaniate deleted entertainment-message.');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }

    public function testSetBody() {
        $message = SKTest_TH::createEntertainmentMessage(null, 'foo');
        $this->assertSame('foo', $message->getBody());

        $message->setBody('bar');

        $this->assertSame('bar', $message->getBody());
    }

    public function testGetBodyCompiled() {
        $word1 = SKTest_TH::createEntertainmentMessage(null, '{he|she|it}');
        $word2 = SKTest_TH::createEntertainmentMessage(null, '{||||}');
        $word3 = SKTest_TH::createEntertainmentMessage(null, '{|a||b|||}');
        $word4 = SKTest_TH::createEntertainmentMessage(null, '{a,b,c}');
        $word5 = SKTest_TH::createEntertainmentMessage(null, '{a,{b},c}');
        $word6 = SKTest_TH::createEntertainmentMessage(null, '{a,{b,c}');
        $word7 = SKTest_TH::createEntertainmentMessage(null, '{a,{b},c');
        $word8 = SKTest_TH::createEntertainmentMessage(null, '{a|{b}|{c|d}}');
        $word9 = SKTest_TH::createEntertainmentMessage(null, '{a|{b|{c|{d|e}}}|{f|g}}');
        $word10 = SKTest_TH::createEntertainmentMessage(null, 'a{{{}}} { {{}}b');
        $word11 = SKTest_TH::createEntertainmentMessage(null, '{{a}{b}{c}}');
        $sentence = SKTest_TH::createEntertainmentMessage(null, '{he|she} owns {car|dog}');

        $this->assertContains($word1->getBodyCompiled(), array('he', 'she', 'it'));
        $this->assertContains($word2->getBodyCompiled(), array(''));
        $this->assertContains($word3->getBodyCompiled(), array('', 'a', 'b'));
        $this->assertContains($word4->getBodyCompiled(), array('a,b,c'));
        $this->assertContains($word5->getBodyCompiled(), array('a,b,c'));
        $this->assertContains($word6->getBodyCompiled(), array('{a,b,c'));
        $this->assertContains($word7->getBodyCompiled(), array('{a,b,c'));
        $this->assertContains($word8->getBodyCompiled(), array('a', 'b', 'c', 'd'));
        $this->assertContains($word9->getBodyCompiled(), array('a', 'b', 'c', 'd', 'e', 'f', 'g'));
        $this->assertContains($word10->getBodyCompiled(), array('a { b'));
        $this->assertContains($word11->getBodyCompiled(), array('abc'));
        $this->assertContains($sentence->getBodyCompiled(), array('he owns car', 'he owns dog', 'she owns car', 'she owns dog'));
    }
}
