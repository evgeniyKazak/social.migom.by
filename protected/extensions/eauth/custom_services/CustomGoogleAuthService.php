<?php
/**
 * An example of extending the provider class.
 *
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://github.com/Nodge/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

require_once dirname(dirname(__FILE__)).'/services/GoogleOAuthService.php';

class CustomGoogleAuthService extends GoogleOAuthService {
	
	//protected $jsArguments = array('popup' => array('width' => 450, 'height' => 450));
	
        protected $scope = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';
    
	protected function fetchAttributes() {
		$info = (array)$this->makeSignedRequest('https://www.googleapis.com/oauth2/v1/userinfo');
				
		$this->attributes['soc_id'] = $info['id'];
		$this->attributes['full_name'] = $info['name'];
                $this->attributes['email'] = $info['email'];
                $this->attributes['login'] = $info['given_name'];
                $this->attributes['avatar'] = $info['picture'];
                $this->attributes['sex'] = $info['gender'];
	}
}