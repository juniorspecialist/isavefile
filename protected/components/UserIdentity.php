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

    public function authenticate()
    {
        $user = Yii::app()->db->createCommand('SELECT * FROM {{user}} WHERE email=:email')->bindValue(':email',$this->username, PDO::PARAM_STR)->queryRow();

        if(empty($user)){
            $this->errorCode=self::ERROR_EMAIL_INVALID;
        }else if(!User::validatePassword($this->password, $user['password'])){
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        }else if($user['block']==User::STATUS_YES){
            $this->errorCode=self::ERROR_STATUS_BAN;
        }else if($user['confirm']==User::STATUS_NO){
            $this->errorCode=self::ERROR_STATUS_NOTACTIV;
        }else {
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