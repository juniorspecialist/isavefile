<?php

/**
 * This is the model class for table "{{file}}".
 *
 * The followings are the available columns in table '{{file}}':
 * @property integer $id
 * @property integer $user_id
 * @property string $shot_url
 * @property string $file_size
 * @property integer $create_at
 * @property string $ip
 * @property string $title_file
 * @property integer $type_file
 * @property string $hash_file
 * @property string $real_name
 */
class File extends CActiveRecord
{

    const TYPE_IMAGES = 1;
    const TYPE_TEXT = 2;
    const TYPE_ARCHIVES = 3;
    const TYPE_AUDIO = 4;
    const TYPE_VIDEO = 5;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return File the static model class
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
		return '{{file}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, user_id, shot_url, file_size, create_at, ip, title_file, type_file, hash_file, real_name', 'required'),
			array('id, user_id, create_at, type_file', 'numerical', 'integerOnly'=>true),
			array('shot_url, title_file, real_name', 'length', 'max'=>255),
			array('file_size', 'length', 'max'=>100),
			array('ip', 'length', 'max'=>20),

			array('hash_file', 'length', 'max'=>40),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, shot_url, file_size, create_at, ip, title_file, type_file, hash_file, real_name', 'safe', 'on'=>'search'),
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
            'author' => array(self::HAS_ONE, 'User', 'user_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'Пользователь',
			'shot_url' => 'Короткая ссылка',
			'file_size' => 'Размер',
			'create_at' => 'Дата',
			'ip' => 'Ip',
			'title_file' => 'Заголовок',
			'type_file' => 'Категория',
			'hash_file' => 'Хеш',
			'real_name' => 'Имя',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('shot_url',$this->shot_url,true);
		$criteria->compare('file_size',$this->file_size,true);
		$criteria->compare('create_at',$this->create_at);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('title_file',$this->title_file,true);
		$criteria->compare('type_file',$this->type_file);
		$criteria->compare('hash_file',$this->hash_file,true);
		$criteria->compare('real_name',$this->real_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /*
     * по текстовому представлению возвращаем ID типа файла
     */
    static function getTypeFile($title){

        if($title=='image'){
            return File::TYPE_IMAGES;
        }

        if($title=='video'){
            return File::TYPE_VIDEO;
        }

        if($title=='audio'){
            return File::TYPE_AUDIO;
        }

        if($title=='archive'){
            return File::TYPE_ARCHIVES;
        }

        if($title=='text'){
            return File::TYPE_TEXT;
        }

        // не найдено соотвествие текстовому представлению категории
        return false;
    }

    /*
     * список расширений для файлов, которые можно загружать
     * получаем по каждой категории строку, в которой доступные расширения для загрузки
     * формируем массив доступных расширений
     */
    static function getExtensionList(){

        $list_category = File::getCategoryList();

        $ext_result = array();

        //$title - текстовое представление, $id- числовое соотвествие категории
        foreach($list_category as $title=>$id){

            $list = explode(' ', Yii::app()->config->get('FILE_TYPE_'.$id));

            foreach($list as $extension){
                $ext_result[] = trim($extension);
            }
        }

        return $ext_result;
    }

    /*
     * список типов файлов
     */
    static function getCategoryList(){
        return array(
            'audio'=>File::TYPE_AUDIO,
            'text'=>File::TYPE_TEXT,
            'video'=>File::TYPE_VIDEO,
            'image'=>File::TYPE_IMAGES,
            'archive'=>File::TYPE_ARCHIVES,
        );
    }
}