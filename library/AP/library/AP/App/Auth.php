<?php

class AP_App_Auth {

    /**
     * @param CM_Model_User $user
     * @param string        $password
     * @return string
     */
    public static function generateHashUserPassword(CM_Model_User $user, $password) {
        return self::_generateHash($password . ':' . $user->getId(), '6t9qdslghdkasjghdf89g7dfs98ghdfug87afdgzoi827b5f8i2d324deagstzujj');
    }

    /**
     * @param AP_Model_User $user
     * @return string
     */
    public static function generateHashUser(AP_Model_User $user) {
        return self::_generateHash($user->getId(), 'zikc686c8k564sadkfhsda98f7sad97f98asdhfiuasdfadszfa4u938jng94j595gj4f');
    }

    /**
     * @param string $login
     * @param string $password
     * @return AP_Model_User|bool
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
