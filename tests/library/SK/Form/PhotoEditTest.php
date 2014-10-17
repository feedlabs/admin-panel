<?php

class SK_Form_PhotoEditTest extends SKTest_TestCase {

    public function testChangePrivacy() {
        $form = new SK_Form_PhotoEdit();
        $formAction = new SK_FormAction_PhotoEdit_Save($form);

        $user = SKTest_TH::createUser();
        $photo = SKTest_TH::createPhoto($user);
        $data = [
            'photoId' => $photo->getId(),
            'privacy' => SK_ModelAsset_Entity_PrivacyAbstract::PERSONAL
        ];
        $request = $this->createRequestFormAction($formAction, $data);

        $response = $this->processRequestWithViewer($request, $user);
        $this->assertFormResponseSuccess($response);

        $photo->getVerification()->setPending();
        $response = $this->processRequestWithViewer($request, $user);
        $this->assertFormResponseError($response, 'Personal is not allowed for verification photo…', 'privacy');
    }
}
