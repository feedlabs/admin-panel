<?php

class SK_App_CliTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testStatus() {
        $outputFile = CM_File::createTmp();
        $outputStream = new CM_OutputStream_File($outputFile);
        $cli = new SK_App_Cli(null, $outputStream);

        $cli->status();
        $statusDecoded = CM_Params::jsonDecode($outputFile->read());
        $this->assertArrayHasKey('entertainment-delay', $statusDecoded);
        $this->assertArrayHasKey('entertainment-queue-size', $statusDecoded);
        $this->assertArrayHasKey('reviewcandidate-queue-size', $statusDecoded);
    }
}
