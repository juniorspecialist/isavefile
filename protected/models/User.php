<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property integer $id
 * @property string $email
 * @property string $salt
 * @property integer $block
 * @property integer $confirm
 * @property string $password
 *
 * The followings are the available model relations:
 * @property File[] $files
 */
class User extends CActiveRecord
{
    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;

    const STATUS_YES = 1;// пользователь заблокирован
    const STATUS_NO = 0;// пользователь разблокирован

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('email, password', 'required'),
			array('block, confirm, role', 'numerical', 'integerOnly'=>true),
			array('email', 'length', 'max'=>255),
            array('hash', 'length', 'max'=>128),
            array('role', 'default', 'value'=>1),
            array('block, confirm', 'default', 'value'=>self::STATUS_NO),
			array('password', 'length', 'max'=>128),

			array('id, email, block, confirm, password, role, hash', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'files' => array(self::HAS_MANY, 'File', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'email' => 'Почта',
			'salt' => 'Соль',
			'block' => 'Блокирован',
			'confirm' => 'Подтверждение',
			'password' => 'Пароль',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('salt',$this->salt,true);
		$criteria->compare('block',$this->block);
		$criteria->compare('confirm',$this->confirm);
		$criteria->compare('password',$this->password,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /*
     * формирование ХЕШа пароля
     */
    static function encrypted($passwordInput){

        $salt = substr(str_replace('+', '.', base64_encode(sha1(microtime(true), true))), 0, 22);

        // 2a is the bcrypt algorithm selector, see http://php.net/crypt
        // 12 is the workload factor (around 300ms on my Core i7 machine), see http://php.net/crypt
        $hash = crypt($passwordInput, '$2a$12$' . $salt);

        return $hash;
    }

    /*
     * метод для проверки пароля с ХЕшем из БД этого пароля
     */
    static function validatePassword($passwordInput, $hash){

        if($hash == crypt($passwordInput, $hash)){
            return true;
        }else{
            return false;
        }
    }

    public function afterValidate(){

        // Передаём эстафетную палочку другим обработчикам// данного события.
        return parent::afterValidate();
    }

    protected function afterSave()
    {
        parent::afterSave();

        // отправим письмо с ссылкой на активацию аккаунта по почте+ создадим каталог(где будем хранить файлы юзера)
        if($this->isNewRecord){
            // отправим письмо с ссылкой на активацию
            HelperFile::sendEmail($this->email, 'Активация аккаунта', User::createActivateEmail($this->hash));

            // создадим каталог, для хранения файлов пользователем
            $userFolder = Yii::getPathOfAlias('webroot.upload.'.$this->id);

            @mkdir($userFolder, 0777, TRUE);
        }
    }

    /*
     * формируем тест письма, для активации аккаунта пользователя в системе
     * $hash - уникальный хеш, по которому активируем юзера
     */
    static function createActivateEmail($hash){

        $link = CHtml::link('Подтвердить регистрацию', Yii::app()->createAbsoluteUrl('/confirm').'?hash='.$hash);

        $msg = 'Уважаемый пользователь! Чтобы закончить регистрацию на сайте перейдите по указанной ссылке '.$link;

        return $msg;
    }

}