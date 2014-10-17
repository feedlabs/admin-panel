<?php

abstract class SKTest_TestCase extends CMTest_TestCase {

    public function runBare() {
            $siteDefault = $this->getMockSite('SK_Site_Abstract', null, array(
                'url'                    => 'http://www.default.dev',
                'urlCdn'                 => 'http://cdn.default.dev',
                'name'                   => 'Default',
                'emailAddress'           => 'default@default.dev',
                'emailAddressComplaints' => 'default@default.dev',
                'emailAddressSupport'    => 'default@default.dev',
                'twitterAccount'         => 'default',
                'googlePlusAccount'      => 'default',
                'tumblrAccount'          => 'default',
            ));
            CM_Config::get()->CM_Site_Abstract->class = get_class($siteDefault);
        parent::runBare();
    }

    public function getResponse(CM_Request_Abstract $request) {
        $response = parent::getResponse($request);
        if ($response instanceof CM_Response_View_Form) {
            $response->mockMethod('reloadComponent');
        }
        return $response;
    }

    /**
     * @param CM_Request_Abstract|\Mocka\ClassTrait $request
     * @param SK_User                               $user
     * @return CM_Response_Abstract|\Mocka\ClassTrait
     */
    public function processRequestWithViewer(CM_Request_Abstract $request, SK_User $user) {
        $request->mockMethod('getViewer')->set($user);
        return $this->processRequest($request);
    }

    /**
     * @param int|null $role
     * @return SK_User
     */
    protected function _createViewer($role = null) {
        $viewer = SKTest_TH::createUser();
        if (!is_null($role)) {
            $viewer->getRoles()->add($role);
        }
        return $viewer;
    }
}
