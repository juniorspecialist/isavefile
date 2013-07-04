<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 04.07.13
 * Time: 17:20
 * To change this template use File | Settings | File Templates.
 */
?>
<?php if(Yii::app()->user->hasFlash('confirm')): ?>

    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('confirm'); ?>
    </div>

    <?php
        echo '<h2>'.CHtml::link(Yii::app()->createAbsoluteUrl('login'), 'Авторизация').'<h2>';
    ?>

<?php else: ?>
    <div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'contact-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
    )); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php $this->endWidget(); ?>
    </div><!-- form -->

<?php endif; ?>