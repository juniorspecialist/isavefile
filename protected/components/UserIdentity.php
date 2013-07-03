<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{

    private $_id;
    const ERROR_EMAIL_INVALID=3;
    const ERROR_STATUS_NOTACTIV=4;
    const ERROR_STATUS_BAN=5;

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
    /*
	public function authenticate()
	{
		$users=array(
			// username => password
			'demo'=>'demo',
			'admin'=>'admin',
		);
		if(!isset($users[$this->username]))
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		elseif($users[$this->username]!==$this->password)
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else
			$this->errorCode=self::ERROR_NONE;
		return !$this->errorCode;
	}*/

    public function authenticate()
    {
        $user = Yii::app()->db->createCommand('SELECT * FROM {{user}} WHERE email=:email')->bindValue(':email',$this->username, PDO::PARAM_STR)->queryRow();

        if(empty($user)){
            $this->errorCode=self::ERROR_EMAIL_INVALID;
        }else if(!User::validatePassword($this->password, $user['hash']))
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else if($user['block']==User::STATUS_YES)
            $this->errorCode=self::ERROR_STATUS_BAN;
        else if($user['confirm']==User::STATUS_YES)
            $this->errorCode=self::ERROR_STATUS_NOTACTIV;
        else {
            $this->_id=$user['id'];

            $this->setState('role',$user['role']);

            $this->errorCode=self::ERROR_NONE;
        }
        return !$this->errorCode;
    }

    /**
     * @return integer the ID of the user record
     */
    public function getId()
    {
        return $this->_id;
    }
}