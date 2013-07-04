<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 04.07.13
 * Time: 17:05
 * To change this template use File | Settings | File Templates.
 */
/*
 * активация пользователя по ХЕШу из ссылки, из письма
 */
class ActivationForm extends CFormModel {

    public $hash;


    public function rules()
    {
        return array(
            array('hash', 'required'),
            array('hash', 'validateHash'),
        );
    }

    /*
     *  проверим во-первых существует ли данный хеш для юзера
     * во-вторых не активировался уже юзер по данному хешу
     */
    public function validateHash(){
        if(!$this->hasErrors()){

            $sql = 'SELECT hash, confirm FROM {{user}} WHERE hash=:hash';

            $query = Yii::app()->db->createCommand($sql)->bindValue(':hash', $this->hash, PDO::PARAM_STR)->queryRow();

            if(empty($query)){
                $this->addError('hash', 'По указанным вами данным, пользователь для активации не найден.');
            }

            if($query['confirm']==User::STATUS_YES){
                $this->addError('hash', 'По указанным вами данным, пользователь уже активирован, можете авторизоваться.');
            }
        }
    }
}