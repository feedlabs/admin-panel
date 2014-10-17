<?php

class SK_Response_Checkout_RocketgateTest extends SKTest_TestCase {

    /**
     * @expectedException CM_Exception
     * @expectedExceptionMessage foo
     */
    public function testErrorLogging() {
        $request = new CM_Request_Post('/payment-callback/rocketgate/');

        $responseMock = $this->mockObject('SK_Response_Checkout_Rocketgate', array($request));
        $responseMock->mockMethod('_process')->set(function(){
            throw new CM_Exception('foo');
        });

        /** @var SK_Response_Checkout_Rocketgate $responseMock */
        $responseMock->process();
    }
}
