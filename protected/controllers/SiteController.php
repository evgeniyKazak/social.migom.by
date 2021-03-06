<?php

class SiteController extends Controller {

    /**
     * Declares class-based actions.
     */
//	public function actions()
//	{
//		return array(
//			// captcha action renders the CAPTCHA image displayed on the contact page
//			'captcha'=>array(
//				'class'=>'CCaptchaAction',
//				'backColor'=>0xFFFFFF,
//			),
//		);
//	}

    public function filters() {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow readers only access to the view file
                'actions' => array('index', 'error', 'login', 'test', 'logout', 'registration'),
                'users' => array('*')
            ),
            array('deny', // deny everybody else
                'users' => array('*')
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        echo 'User Name: <b>' . Yii::app()->user->name . '</b>';
        echo '<br/>------------------------<br/>';
        foreach (get_class_methods(__CLASS__) as $methods) {
            if (strpos($methods, 'action') === 0 && $methods !== 'actions') {
                echo CHtml::link(substr($methods, 6), array('site/' . substr($methods, 6)));
                echo '<br/>';
            }
        }
//            d(get_class_methods(__CLASS__));
    }

    public function actionTest() {
        echo 'p0st';
        d($_POST);
        echo 'get';
        d($_GET);
        echo 'delete';
        d($_DELETE);
        echo 'put';
        d($_PUT);
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {
        if (!Yii::app()->user->getIsGuest()) {
            $this->redirect(array('/user/index'));
        }
        $this->layout = 'login';

        $service = Yii::app()->request->getQuery('service');
        if (isset($service)) {

            $authIdentity = Yii::app()->eauth->getIdentity($service);
//            $authIdentity->redirectUrl = Yii::app()->user->returnUrl;
            $authIdentity->redirectUrl = $this->createUrl('/user/index');
            $authIdentity->cancelUrl = $this->createAbsoluteUrl('site/login');

            if ($authIdentity->authenticate()) {
                $identity = new EAuthUserIdentity($authIdentity);

                // successful authentication
                if ($identity->authenticate()) {
                    Yii::app()->user->login($identity);

                    // special redirect with closing popup window
                    $authIdentity->redirect();
                } elseif ($identity->errorCode == EAuthUserIdentity::ERROR_USER_NOT_REGISTERED) {
                    $reg = new RegistrationForm();
                    $reg->registration($identity, $service);
                    
                    // special redirect with closing popup window
                    $authIdentity->redirect();
                } else {
                    // close popup window and redirect to cancelUrl
                    $authIdentity->cancel();
                }
            }

            // Something went wrong, redirect to login page
            $this->redirect(array('/site/login'));
        }

        $model = new LoginForm;

        // if it is ajax validation request
        if (Yii::app()->getRequest()->isAjaxRequest && Yii::app()->getRequest()->getParam('ajax') == 'formLogin') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
//                            $this->redirect(Yii::app()->user->returnUrl);
                $this->redirect('/user/index');
        }
//            d(Yii::app()->getRequest()->getPost());
        // display the login form
        
        $regModel = new RegistrationForm();
        $this->render('login', array('model' => $model, 'regModel' => $regModel));
    }
    
    protected function vkLogin(){
        $this->layout = 'popup';
        $this->render('login/auth');
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionRegistration() {
        $model = new RegistrationForm;

        // if it is ajax validation request
        if (Yii::app()->getRequest()->isAjaxRequest && Yii::app()->getRequest()->getParam('ajax') == 'formReg') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['RegistrationForm'])) {
            $model->attributes = $_POST['RegistrationForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->registration()){
                $this->redirect('/user/index');
            }
        }
        $this->redirect('/site/login');
    }
    
    public function actionSocialReg(){
        
    }
}