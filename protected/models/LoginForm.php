<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;
    public $existUser = false;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username, password', 'required'),
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean'),
			// password needs to be authenticated
			//array('password', 'authenticate'),
            // проверим заблокирован ли юзер ?, активирован ?
            array('username', 'isExistUser'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>'Логин(email)',
            'password'=>'Пароль',
		);
	}

    /*
     *  проверим зареган ли текущий юзер
     * и если зареган - активирован ли или нужно отправить ему письмо снова с авторизацией
     */
    public function isExistUser(){
        if(!$this->hasErrors()){

            $sql = 'SELECT confirm, block, hash, email FROM {{user}} WHERE email=:email';

            $user = Yii::app()->db->createCommand($sql)->bindValue(':email', $this->username, PDO::PARAM_STR)->queryRow();

            // пользователь зареган в БД
            if(!empty($user)){

                $this->existUser = true;

                // проверим заблокирован ли он, подтвердил ли он авторизацию
                if($user['block']==User::STATUS_YES){
                    $this->addError('username', 'Ваш пользователь заблокирован администрацией');
                }
                if($user['confirm']==User::STATUS_NO){

                    $this->addError('username', 'Ваш пользователь не подтвердил регистрацию через ссылку в письме, вам отправлено письмо с активацией повторно');

                    // отправка письма, с ссылкой на активацию аккаунта
                    HelperFile::sendEmail($user['email'], 'Активация аккаунта', User::createActivateEmail($user['hash']));
                }

                //проверка совпадения пароля
                if(!$this->hasErrors())
                {
                    $this->_identity=new UserIdentity($this->username,$this->password);
                    if(!$this->_identity->authenticate())
                        $this->addError('password','Не верно указан логин или пароль.');
                }
            }
        }
    }

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password','Не верно указан логин или пароль.');
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}
