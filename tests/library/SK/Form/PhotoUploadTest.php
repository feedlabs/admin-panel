<?php

class SK_Form_PhotoUploadTest extends SKTest_TestCase {

    public function testChangePrivacy() {
        $user = SKTest_TH::createUser();
        $fileSource = new CM_File(DIR_TEST_DATA . 'img/test.jpg');
        $fileTmpList = array(
            CM_File_UserContent_Temp::create('foo1.jpg', $fileSource->read()),
            CM_File_UserContent_Temp::create('foo2.jpg', $fileSource->read()),
        );
        $fileTmpIdList = Functional\map($fileTmpList, function (CM_File_UserContent_Temp $fileTmp) {
            return $fileTmp->getUniqid();
        });

        $form = new SK_Form_PhotoUpload();
        $formAction = new SK_FormAction_PhotoUpload_Process($form);
        $data = [
            'userId' => $user->getId(),
            'photo'  => $fileTmpIdList
        ];
        $request = $this->createRequestFormAction($formAction, $data);

        $response = $this->processRequestWithViewer($request, $user);
        $this->assertFormResponseSuccess($response);
        $this->assertCount(2, $user->getPhotos());
    }

    public function testUploadAdminForOtherUser() {
        $user = SKTest_TH::createUser();
        $fileSource = new CM_File(DIR_TEST_DATA . 'img/test.jpg');
        $fileTmpList = array(
            CM_File_UserContent_Temp::create('foo1.jpg', $fileSource->read()),
        );
        $fileTmpIdList = Functional\map($fileTmpList, function (CM_File_UserContent_Temp $fileTmp) {
            return $fileTmp->getUniqid();
        });

        $form = new SK_Form_PhotoUpload();
        $formAction = new SK_FormAction_PhotoUpload_Process($form);
        $data = [
            'userId' => $user->getId(),
            'photo'  => $fileTmpIdList
        ];
        $request = $this->createRequestFormAction($formAction, $data);

        $viewer = $this->_createViewer(SK_Role::ADMIN);
        $response = $this->processRequestWithViewer($request, $viewer);
        $this->assertFormResponseSuccess($response);
        $this->assertCount(1, $user->getPhotos());
    }
}
