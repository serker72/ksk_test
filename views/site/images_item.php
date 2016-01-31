<?php
use yii\helpers\Html;
?>
<?= Html::img(Yii::getAlias('@common_web').'/images/thumbnail/'. $model->filename, []);
