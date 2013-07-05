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


    public $defaultAction = 'category';

    /*
     * отображаем список файлов, которые он загрузил на сервер
     * в взависимости от выбранной категории отображаем список файлов пользователю либо весь список
     */
    public function actionCategory($title=''){

        $where = '';

        $sql_count = 'SELECT COUNT(*) FROM {{file}} WHERE user_id=:user_id';

        $sql = 'SELECT * FROM {{file}} WHERE user_id='.Yii::app()->user->id;

        if(!empty($title)){

            // валидируем категорию файлов по их текстовому представлению
            $type_id = File::getTypeFile($title);

            // не прошла валидацию указанная категория - выводим весь список файлов
            if(!$type_id){

                $count = Yii::app()->db->createCommand($sql_count)->bindValue(':user_id', Yii::app()->user->id, PDO::PARAM_INT)->queryScalar();

            }else{

                // добавляем фильтр по выбранной категории файлов
                $where = ' AND type_file=:type_file';

                $sql.=  ' AND type_file='.$type_id;

                $count = Yii::app()->db->createCommand($sql_count)->bindValues(array(':user_id'=>Yii::app()->user->id,':type_file'=>$type_id))->queryScalar();
            }

        }else{
            $count = Yii::app()->db->createCommand($sql_count)->bindValue(':user_id', Yii::app()->user->id)->queryScalar();
        }

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