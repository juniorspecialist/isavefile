<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

    public function filters()
    {
        return array(
            'ajaxOnly+upload',
        );
    }
    /*
     * контроллер для загрузки файлов
     */
    public function actionUpload(){

        $tempFolder=Yii::getPathOfAlias('webroot').'/upload/';

        @mkdir($tempFolder, 0777, TRUE);
        @mkdir($tempFolder.'chunks', 0777, TRUE);

        Yii::import("ext.EFineUploader.qqFileUploader");

        $uploader = new qqFileUploader();
        $uploader->allowedExtensions = array('rar','zip');
        $uploader->sizeLimit = 5 * 1024 * 1024;//maximum file size in bytes
        $uploader->chunksFolder = $tempFolder.'chunks';

        $result = $uploader->handleUpload($tempFolder);
        $result['filename'] = $uploader->getUploadName();
        $result['folder'] = Yii::getPathOfAlias('webroot.upload');

        $uploadedFile=$tempFolder.$result['filename'];

        header("Content-Type: text/plain");
        $result=htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        echo $result;
        Yii::app()->end();
    }

	/**
	 * загрузка файла
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

    /*
     * подтверждаем регистрацию пользователя
     */
    public function actionConfirm($hash=''){

        $model = new ActivationForm();

        $model->hash = Yii::app()->request->getParam('hash');

        if($model->validate()){
            // активируем юзера и сообщим об успешной активации
            Yii::app()->db->createCommand('UPDATE {{user}} SET confirm=:confirm WHERE hash=:hash')->bindValues(array(':confirm'=>User::STATUS_YES, ':hash'=>$model->hash))->execute();

            Yii::app()->user->setFlash('confirm','Спасибо, активация вашего аккаунта прошла успешно.');

        }

        $this->render('confirm', array(
            'model'=>$model
        ));
    }


	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * авторизация-регистрация юзера
     * если юзер зареган, то считаем что он хочет авторизоваться,
     * если юзер новый - то считаем, что он хочет зарегаться
	 */
	public function actionLogin()
	{
		$model = new LoginForm;

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];

            // активирон ? заблокирован ? зар
			if($model->validate()){//&&

                //проверяем существует ли данный юзер или мы регаем нового
                if($model->existUser){
                    //пробуем авторизовать юзера
                    if($model->login()){
                        $this->redirect(Yii::app()->user->returnUrl);
                    }
                }else{

                    //регаем нового юзера
                    $user = new User();
                    $user->email = $model->username;
                    $user->password = User::encrypted($model->password);
                    $user->role = User::ROLE_USER;
                    $user->hash = md5(time());
                    if($user->validate()){
                        $user->save();
                        Yii::app()->user->setFlash('registration','Вы успешно зарегистрировались в системе, вам было выслано письмо на почту с активацией аккаунта.');

                        $this->refresh();

                    }else{
                        print_r($user->errors);
                    }
                }
            }

		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}