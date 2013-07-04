<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 04.07.13
 * Time: 17:37
 * To change this template use File | Settings | File Templates.
 */

$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$dataProvider,
    'itemView'=>'_file',   // refers to the partial view named '_post'
//    'sortableAttributes'=>array(
//        'title',
//        'create_time'=>'Post Time',
//    ),
));