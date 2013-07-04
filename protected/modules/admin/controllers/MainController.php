<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 01.07.13
 * Time: 17:15
 * To change this template use File | Settings | File Templates.
 */

class MainController extends AdminBaseController{

    /*
     * авторизация админа
     */
    public function actionLogin(){

        $model = new User();

        if(isset($_POST['User']))
        {
            $model->attributes = $_POST['User'];

            if($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        $this->render('login',array('model'=>$model));
    }
}