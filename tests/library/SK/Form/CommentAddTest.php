<?php

class SK_Form_CommentAddTest extends SKTest_TestCase {

    public function testProcess() {
        $form = new SK_Form_CommentAdd();
        $formAction = new SK_FormAction_CommentAdd_Process($form);
        $entity = SKTest_TH::createPhoto();
        $data = [
            'entityType' => $entity->getType(),
            'entityId'   => $entity->getId(),
            'text'       => 'foo',
        ];
        $request = $this->createRequestFormAction($formAction, $data);

        $viewer = $this->_createViewer(SK_Role::PREMIUMUSER);
        $response = $this->processRequestWithViewer($request, $viewer);

        $this->assertFormResponseSuccess($response);
        $commentList = $entity->getComments()->get();
        $this->assertSame(1, $commentList->getCount());
        $this->assertSame('foo', $commentList->getItem(0)->getText());
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage Entity of type `6` cannot be commented on
     */
    public function testProcessInvalidEntityType() {
        $form = new SK_Form_CommentAdd();
        $formAction = new SK_FormAction_CommentAdd_Process($form);
        $entity = SKTest_TH::createConversation();
        $data = array(
            'entityType' => $entity->getType(),
            'entityId'   => $entity->getId(),
            'text'       => 'foo',
        );
        $request = $this->createRequestFormAction($formAction, $data);

        $viewer = $this->_createViewer(SK_Role::PREMIUMUSER);
        $this->processRequestWithViewer($request, $viewer);
    }
}
