<?php

class AP_Role {

    const FREEUSER = 1;
    const PREMIUMUSER = 2;
    const ADMIN = 3;

    /**
     * @return int[]
     */
    public static function getRoles() {
        return array(
            self::FREEUSER,
            self::PREMIUMUSER,
            self::ADMIN,
        );
    }
}
