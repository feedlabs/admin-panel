<?php

class AP_Model_User extends CM_Model_User {

    const USERNAME_CHARACTERS = "a-zA-Z0-9_-";
    const USERNAME_CHARACTERS_REPLACEMENT = "_";

    /**
     * @return string
     */
    public function getEmail() {
        return $this->_get('email');
    }

    /**
     * @param string $email
     * @return AP_Model_User
     */
    public function setEmail($email) {
        CM_Db_Db::update('ap_user', array('email' => $email), array('userId' => $this->getId()));
        return $this->_change();
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->_get('username');
    }

    /**
     * @param string $username
     * @return AP_Model_User
     */
    public function setUsername($username) {
        CM_Db_Db::update('ap_user', array('username' => $username), array('userId' => $this->getId()));
        return $this->_change();
    }

    /**
     * @param string $password
     * @return AP_Model_User
     */
    public function setPassword($password) {
        $hash = AP_App_Auth::generateHashUserPassword($this, $password);
        CM_Db_Db::update('ap_user', array('password' => $hash), array('userId' => $this->getId()));
        return $this->_change();
    }



    protected function _loadData() {
        $return = CM_Db_Db::exec("SELECT `main`.*, `secondary`.*, `online`.`userId` AS `online`, `online`.`visible`
								  FROM `cm_user` AS `main`
								  JOIN `ap_user` AS `secondary` USING (`userId`)
								  LEFT JOIN `cm_user_online` AS `online` USING(`userId`)
								  WHERE `main`.`userId`=?", array($this->getId()))->fetch();
        return $return;
    }

    protected function _onDelete() {
        CM_Db_Db::delete('ap_user', array('userId' => $this->getId()));
        parent::_onDelete();
    }

    /**
     * @param string $login
     * @param string $password
     * @return AP_Model_User
     * @throws CM_Exception_AuthFailed
     */
    public static function authenticate($login, $password) {
        if (!$login || !$password) {
            throw new CM_Exception_AuthFailed('Username and password required', null, array('messagePublic' => 'Username or Password is empty'));
        }
        $user = AP_App_Auth::checkLogin($login, $password);
        if (!$user) {
            throw new CM_Exception_AuthFailed('Authentication failed', null, array('messagePublic' => 'Password and Username do not match'));
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $user->getUseragents()->add($_SERVER['HTTP_USER_AGENT']);
        }
        return $user;
    }

    /**
     * @param string $username
     * @return bool
     */
    public static function usernameIsValid($username) {
        return (bool) preg_match("/^[" . self::USERNAME_CHARACTERS . "]*$/", $username);
    }

    /**
     * @param string $username
     * @return AP_Model_User|null
     */
    public static function findUsername($username) {
        $cacheKey = self::_getUsernameCacheKey($username);
        $cache = CM_Cache_Shared::getInstance();
        if (($id = $cache->get($cacheKey)) === false) {
            $id = CM_Db_Db::select('ap_user', 'userId', array('username' => $username))->fetchColumn();
            if (!$id) {
                return null;
            }
            $cache->set($cacheKey, (int) $id);
        }
        return new self($id);
    }

    /**
     * @param string $email
     * @return AP_Model_User|null
     */
    public static function findEmail($email) {
        $id = CM_Db_Db::select('ap_user', 'userId', array('email' => $email))->fetchColumn();
        if (!$id) {
            return null;
        }
        return new self($id);
    }


    /**
     * @param array $data
     * @throws CM_Exception|Exception
     * @return AP_Model_User
     */
    public static function _createStatic(array $data) {
        $site = null;
        if (isset($data['site'])) {
            /** @var CM_Site_Abstract $site */
            $site = $data['site'];
        }
        $language = null;
        if (isset($data['language'])) {
            /** @var CM_Model_Language $language */
            $language = $data['language'];
        }
        $username = (string) $data['username'];
        $email = (string) $data['email'];
        $password = (string) $data['password'];
        $user = CM_Model_User::createStatic(array('site' => $site, 'language' => $language));
        $userId = $user->getId();
        $hash = AP_App_Auth::generateHashUserPassword($user, $password);
        $values = array('userId'        => $userId,
                        'username'      => $username,
                        'email'         => $email,
                        'password'      => $hash,
        );
        try {
            CM_Db_Db::insert('ap_user', $values);
        } catch (CM_Exception $e) {
            CM_Db_Db::delete('cm_user', array('userId' => $userId));
            throw $e;
        }

        return new self($userId);
    }

    /**
     * @param string $username
     * @return string
     */
    private static function _getUsernameCacheKey($username) {
        return AP_CacheConst::User . '_username:' . $username;
    }
}
