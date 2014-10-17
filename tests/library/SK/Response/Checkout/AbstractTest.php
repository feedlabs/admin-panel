<?php

class SK_Response_Checkout_AbstractTest extends SKTest_TestCase {

    public function testFactory() {
        $request = new CM_Request_Get('/payment-callback/zombaio/');
        $this->assertInstanceOf('SK_Response_Checkout_Zombaio', CM_Response_Abstract::factory($request));

        $request = new CM_Request_Post('/payment-callback/ccbill/');
        $this->assertInstanceOf('SK_Response_Checkout_CCBill', CM_Response_Abstract::factory($request));
    }
}
