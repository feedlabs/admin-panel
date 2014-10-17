<?php

class SK_ModelAsset_Entity_CommentsTest extends SKTest_TestCase {

    public function testSet() {
        $video = SKTest_TH::createVideo();
        $commentId = SK_Entity_Comment::create(SKTest_TH::createUser(), $video, 'Test Comment')->getId();
        $this->assertRow('sk_entity_comment', array('id' => $commentId));
    }

    public function testGetCount() {
        $video = SKTest_TH::createVideo();
        $user = SKTest_TH::createUser();

        SK_Entity_Comment::create($user, $video, 'Test Comment 1');
        SK_Entity_Comment::create($user, $video, 'Test Comment 2');
        SK_Entity_Comment::create($user, $video, 'Test Comment 3');
        SK_Entity_Comment::create($user, $video, 'Test Comment 4');

        SKTest_TH::reinstantiateModel($video);
        $this->assertEquals(4, $video->getComments()->getCount());

        $comment = SK_Entity_Comment::create($user, $video, 'Test Comment 5');
        SKTest_TH::reinstantiateModel($video);
        $this->assertEquals(5, $video->getComments()->getCount());
        $comment->delete();
        SKTest_TH::reinstantiateModel($video);
        $this->assertEquals(4, $video->getComments()->getCount());
    }

    public function testGet() {
        $video = SKTest_TH::createVideo();
        $user = SKTest_TH::createUser();

        SK_Entity_Comment::create($user, $video, 'Test Comment 1');
        SK_Entity_Comment::create($user, $video, 'Test Comment 2');
        SK_Entity_Comment::create($user, $video, 'Test Comment 3');
        SK_Entity_Comment::create($user, $video, 'Test Comment 4');
        SK_Entity_Comment::create($user, $video, 'Test Comment 5');
        $comments = $video->getComments()->get(true);
        $this->assertEquals(5, $comments->getCount());
        SKTest_TH::reinstantiateModel($video);
        $this->assertEquals(5, $video->getComments()->getCount());
    }

    public function testGetBlocked() {
        $video = SKTest_TH::createVideo();
        $user = SKTest_TH::createUser();
        $userBlocked = SKTest_TH::createUser();
        $userBlocked->setBlocked();

        SK_Entity_Comment::create($user, $video, 'Test Comment 1');
        SK_Entity_Comment::create($userBlocked, $video, 'Test Comment 2');
        SK_Entity_Comment::create($userBlocked, $video, 'Test Comment 3');
        SK_Entity_Comment::create($userBlocked, $video, 'Test Comment 4');
        SK_Entity_Comment::create($user, $video, 'Test Comment 5');
        $commentsIncludingBlocked = $video->getComments()->get();
        $this->assertEquals(5, $commentsIncludingBlocked->getCount());
        $comments = $video->getComments()->get(true);
        $this->assertEquals(2, $comments->getCount());
        SKTest_TH::reinstantiateModel($video);
        $this->assertEquals(2, $video->getComments()->getCount());
    }

    public function testDeleteEntity() {
        $video = SKTest_TH::createVideo();
        $user = SKTest_TH::createUser();
        $userBlocked = SKTest_TH::createUser();
        $userBlocked->setBlocked();

        $comment = SK_Entity_Comment::create($user, $video, 'Test');
        $commentBlocked = SK_Entity_Comment::create($userBlocked, $video, 'Test');

        $video->delete();
        try {
            SKTest_TH::reinstantiateModel($comment);
            $this->fail('Could instantiate deleted comment.');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
        try {
            SKTest_TH::reinstantiateModel($commentBlocked);
            $this->fail('Could instantiate deleted comment.');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }
}
