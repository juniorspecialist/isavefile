<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 04.07.13
 * Time: 17:31
 * To change this template use File | Settings | File Templates.
 */

/*
 * личный кабинет юзера, отображдаем список его заметок и файлов, по разным категориям
 */
class UserController extends Controller {


    /*
     * отображаем список файлов, которые он загрузил на сервер
     * в взависимости от выбранной категории отображаем список файлов пользователю либо весь список
     */
    public function actionCategory($title){

        $count = Yii::app()->db->createCommand('SELECT COUNT(*) FROM {{file}} WHERE user_id=:user_id')->bindValue(':user_id', Yii::app()->user->id, PDO::PARAM_INT)->queryScalar();

        $sql = 'SELECT * FROM {{file}} WHERE user_id='.Yii::app()->user->id;

        $dataProvider=new CSqlDataProvider($sql, array(
            'totalItemCount'=>$count,
//            'sort'=>array(
//                'attributes'=>array(
//                    'id', 'username', 'email',
//                ),
//            ),
            'pagination'=>array(
                'pageSize'=>20,
            ),
        ));

        $this->render('file_list', array('dataProvider'=>$dataProvider));
    }

}