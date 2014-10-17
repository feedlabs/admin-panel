<?php

class SK_RoleTest extends SKTest_TestCase {

    public function testCanChange() {
        $user = SKTest_TH::createUser();
        $userAdmin = SKTest_TH::createUser();
        $userAdmin->getRoles()->add(SK_Role::ADMIN);
        $userSupporter = SKTest_TH::createUser();
        $userSupporter->getRoles()->add(SK_Role::SUPPORTER);

        $this->assertSame(false, SK_Role::canChange(SK_Role::ADMIN, $user));
        $this->assertSame(true, SK_Role::canChange(SK_Role::ADMIN, $userAdmin));
        $this->assertSame(false, SK_Role::canChange(SK_Role::ADMIN, $userSupporter));

        $this->assertSame(false, SK_Role::canChange(SK_Role::PREMIUMUSER, $user));
        $this->assertSame(true, SK_Role::canChange(SK_Role::PREMIUMUSER, $userAdmin));
        $this->assertSame(true, SK_Role::canChange(SK_Role::PREMIUMUSER, $userSupporter));

        $this->assertSame(false, SK_Role::canChange(SK_Role::VERIFIED_PHOTO, $user));
        $this->assertSame(true, SK_Role::canChange(SK_Role::VERIFIED_PHOTO, $userAdmin));
        $this->assertSame(true, SK_Role::canChange(SK_Role::VERIFIED_PHOTO, $userSupporter));

        $this->assertSame(false, SK_Role::canChange(SK_Role::VERIFIED_MANUALLY, $user));
        $this->assertSame(true, SK_Role::canChange(SK_Role::VERIFIED_MANUALLY, $userAdmin));
        $this->assertSame(true, SK_Role::canChange(SK_Role::VERIFIED_MANUALLY, $userSupporter));
    }
}
