<?php

class AP_App_Auth {

    /**
     * @param CM_Model_User $user
     * @param string        $password
     * @return string
     */
    public static function generateHashUserPassword(CM_Model_User $user, $password) {
        return self::_generateHash($password . ':' . $user->getId(), '6t9q58679grt34078nhuwefpwipneklhmjktikz8itseydxaxyoi827b5f8i2d324deagstzujj');
    }

    /**
     * @param AP_Model_User $user
     * @return string
     */
    public static function generateHashUser(AP_Model_User $user) {
        return self::_generateHash($user->getId(), 'zikc686c8k5647khkbjkhlbjk45iogrepi7887wuguw5768o576niugt94u938jng94j595gj4f');
    }

    /**
     * @param string $login
     * @param string $password
     * @return bool|AP_Model_User
     */
    public static function checkLogin($login, $password) {
        $userId = CM_Db_Db::exec("SELECT `userId`  FROM `ap_user` WHERE (`username` = ? OR `email` = ?)", array($login, $login))->fetchColumn();
        if (!$userId) {
            return false;
        }
        $user = new AP_Model_User($userId);
        $hash = self::generateHashUserPassword($user, $password);
        $hashMatchesProfile = CM_Db_Db::count('ap_user', array('userId' => $userId, 'password' => $hash));
        if (!$hashMatchesProfile) {
            return false;
        }
        return $user;
    }

    /**
     * @param string $base
     * @param string $salt
     * @return string
     */
    private static function _generateHash($base, $salt) {
        $hash = hash('sha256', $salt . ':' . $base);
        return $hash;
    }
}