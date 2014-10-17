<?php

class SK_Form_BlogpostTest extends SKTest_TestCase {

    public function testProcessAdd() {
        $form = new SK_Form_Blogpost();
        $formAction = new SK_FormAction_Blogpost_Add($form);
        $data = array(
            "blogpost" => "",
            "title"    => "asdf",
            "text"     => "asdfasdf",
            "tags"     => "asdf, dd, ee, ww",
            'privacy'  => SK_ModelAsset_Entity_PrivacyAbstract::NONE,
        );
        $user = SKTest_TH::createUserPremium();
        $request = $this->createRequestFormAction($formAction, $data);
        $request->mockMethod('getViewer')->set($user);
        $this->processRequest($request);

        $blogpost = $user->getBlogposts()->getItem(0);
        $this->assertEquals('asdf', $blogpost->getTitle());
        $this->assertContainsAll(array("asdf", "dd", "ee", "ww"), $blogpost->getTags()->get());
        $this->assertContains('asdfasdf', $blogpost->getText());
    }

    public function testProcessEdit() {
        $form = new SK_Form_Blogpost();
        $formAction = new SK_FormAction_Blogpost_Save($form);
        $user = SKTest_TH::createUserPremium();
        $blogpost = SKTest_TH::createBlogpost($user);
        $data = array(
            "blogpost" => $blogpost->getId(),
            "title"    => "asdf",
            "text"     => "asdfasdf",
            'privacy'  => SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY,
        );
        $request = $this->createRequestFormAction($formAction, $data);
        $request->mockMethod('getViewer')->set($user);
        $this->processRequest($request);
        $blogpost->_change();

        $this->assertEquals('asdf', $blogpost->getTitle());
        $this->assertContains('asdfasdf', $blogpost->getText());
    }

    public function testWrongUser() {
        $viewer = SKTest_TH::createUserPremium();
        $blogpostOwner = SKTest_TH::createUserPremium();

        $form = new SK_Form_Blogpost();
        $formAction = new SK_FormAction_Blogpost_Save($form);

        $blogpost = SKTest_TH::createBlogpost($blogpostOwner);
        $data = array(
            "blogpost" => $blogpost->getId(),
            "title"    => "asdf",
            "text"     => "asdfasdf",
            "tags"     => "asdf dd ee ww",
            'privacy'  => SK_ModelAsset_Entity_PrivacyAbstract::NONE,
        );
        $request = $this->createRequestFormAction($formAction, $data);
        $request->mockMethod('getViewer')->set($viewer);
        $response = $this->processRequest($request);
        $this->assertViewResponseError($response, 'CM_Exception_NotAllowed');
    }
}
