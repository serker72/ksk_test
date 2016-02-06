<?php
use yii\helpers\Html;
?>
<div id="view_img_<?php echo $model->id; ?>">
    <?= Html::img(Yii::getAlias('@common_web').'/images/thumbnail/'. $model->filename, []); ?>
</div>
<div id="delete_img_<?php echo $model->id; ?>" style="text-align: center; margin-top: 5px;">
    <!--?= Html::button('Удалить', ['class' => 'btn btn-danger delete-image-button', 'data' => ['id' => $model->id]]) ?-->
    <?= Html::a('Удалить', '#', ['class' => 'btn btn-danger delete-image-button', 'data' => ['id' => $model->id], 'onclick' => 'DeleteImage('.$model->id.');']) ?>
</div>
