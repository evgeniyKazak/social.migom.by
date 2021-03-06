<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class RegistrationForm extends CFormModel
{
	public $email;
	public $agree;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('email', 'required', 'message' => Yii::t('Site', 'Cannot be blank')),
                        array('email', 'email', 'message' => Yii::t('Site', 'write right')),
			array('agree', 'in', 'range'=>array(1), 'allowEmpty'=>false, 'message' => Yii::t('Site', 'You are not agree with rules?')),
			// password needs to be authenticated
			array('email', 'authenticate'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
                        'email' => Yii::t('Site', 'E-mail'),
			'agree'=>Yii::t('Site', 'I agree with the rules'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
                    $user = Users::model()->find('LOWER(email)=?', array(strtolower($this->email)));
                    if($user){
                        $this->addError('email', Yii::t('Site', 'User with this email was registered'));
                    }
		}
	}

        public function registration($identity = null, $service = null){
            if($service){
                $user = new Users('regByApi');
                $user->attributes = $identity->getAttributes();
                if($user->save()){
                    $identity->setId($user->id);
                    $profile = new Profile();
                    $profile->attributes = $identity->getAttributes();
                    if($identity->getAttribute('avatar')){
                        // upload avatar to self server
                        $profile->avatar = UserService::uploadAvatarFromService($user->id, $identity->getAttribute('avatar'));
                    }
                    $profile->sex = array_search($identity->getAttribute('sex'), $profile->sexs);
                    $profile->user_id = $user->id;
                    $userProviders = new UserProviders();
                    $userProviders->attributes = $identity->getAttributes();
                    $userProviders->user_id = $profile->user_id;
                    $userProviders->provider_id = array_search($service, UserProviders::$providers);

                    $profile->save();
                    $userProviders->save();
                } else {
                    return $user;
                }
            } else {
                $user = new Users('simpleRegistration');
                $user->email = $this->email;
                if(!$user->save()){
                    return false;
                }
                $profile = new Profile();
                $profile->user_id = $user->id;
                $profile->full_name = $user->login;
                $profile->save();
                if(!$identity){
                    $identity = new UserIdentity($user->email, $user->password);
                    $identity->authenticate();
                }
            }
            Yii::app()->user->login($identity);
            return true;
        }
}
