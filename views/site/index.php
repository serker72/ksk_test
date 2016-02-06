<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Список изображений';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-index container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <p>
                <?= Html::button('Загрузка картинок', ['class' => 'btn btn-success', 'id' => 'add-images-button']) ?>
                <?= Html::button('Очистка хранилища', ['class' => 'btn btn-danger', 'id' => 'clear-images-button', 'disabled' => $dataProvider->getCount()<1]) ?>
                <?= Html::button('Обновить список', ['class' => 'btn btn-primary', 'id' => 'refresh-images-button']) ?>
            </p>
        </div>
    </div>
    
    <div id="add-images-div" class="row">
        <div class="col-md-12">
            <div class="images-form">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                <?php
                    echo \kato\DropZone::widget([
                        'options' => [
                            'maxFilesize' => '10',
                            'dictFileTooBig' => 'Файл должен быть не более 10 мб',
                            'maxFiles' => '5',
                            'dictDefaultMessage' => 'Кликните мышкой для выбора файлов или поместите файлы методом перетягивания',
                            'dictMaxFilesExceeded' => 'Максимально за 1 раз можно загрузить не более 5 файлов',
                            'acceptedFiles' => '.jpg,.jpeg,.png,.gif',
                            'dictInvalidFileType' => 'Допускаются только картинки в форматах Jpeg, Png, Gif',
                            'url' =>'/upload' // здесь путь к вышеописанному экшену в контролере
                        ],
                       'clientEvents' => [
                            'complete' => "function(file){ completeDropzoneEvents(file); myDropzone.removeFile(file); }",
                            'removedfile' => "function(file){ removedfileDropzoneEvents(file); }",
                            'reset' => "function(){ canceledDropzoneEvents(); }",
                       ],                        
                    ]);
                ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="listview-images">
                <?php Pjax::begin(['id' => 'listview-images']) ?>
                    <?= ListView::widget( [
                        'dataProvider' => $dataProvider,
                        'itemOptions' => ['class' => 'item'],
                        'itemView' => 'images_item',
                        'pager' => ['class' => \kop\y2sp\ScrollPager::className()],
                    ] ); ?>
                <?php Pjax::end() ?>
            </div>
        </div>
        <div class="col-md-12 clearfix"></div>
    </div>
    

    <!--?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'filename:ntext',
            
           [
                'format' => 'html',
                'label' => 'ImageColumnLable',
                'value' => function ($data) {
                    $s = Yii::getAlias('@common_web').'/images/'. $data->filename;
                    return Html::img($s, ['width' => '200px']);
                },
            ],            

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?-->
</div>

<?php
$script = <<< JS
    function ShowAddImages(eventObject)
    {
        console.log('ShowAddImages');
        jQuery("div#add-images-div").show();
        jQuery("div#myDropzone").click();
        return false;
    }
    
    function RefreshImages(eventObject)
    {
        console.log('RefreshImages');
        jQuery.pjax.reload({container:'#listview-images'});  //Reload GridView
        return false;
    }
    
    function ClearImages(eventObject)
    {
        console.log('ClearImages');
        jQuery.ajax({
           url: '/clear',
           //data: {id: '<id>', 'other': '<other>'},
           success: function(data) {
                // process data
                jQuery.pjax.reload({container:'#listview-images'});  //Reload GridView
                jQuery("#clear-images-button").attr('disabled', 'disabled');
           }
        });        
        return false;
    }
    
    //function DeleteImage(eventObject)
    function DeleteImage(id)
    {
        //var target = eventObject.target;
        //console.log('DeleteImage id - ' + target.dataset.id);
        console.log('DeleteImage id - ' + id);
        jQuery.ajax({
           url: '/delete',
           //data: {id: '<id>', 'other': '<other>'},
           //data: {id: target.dataset.id},
           data: {id: id},
           success: function(data) {
                // process data
                jQuery.pjax.reload({container:'#listview-images'});  //Reload GridView
           }
        });        
        return false;
    }
    
    function completeDropzoneEvents(file)
    {
        console.log('completeDropzoneEvents - ' + file);
        jQuery.pjax.reload({container:'#listview-images'});  //Reload GridView
        jQuery('div#add-images-div').hide();
        jQuery("#clear-images-button").removeAttr('disabled');
    }
    
    function removedfileDropzoneEvents(file)
    {
        console.log('removedfileDropzoneEvents - ' + file);
        jQuery.pjax.reload({container:'#listview-images'});  //Reload GridView
    }
        
    function canceledDropzoneEvents()
    {
        jQuery("div#add-images-div").hide();
    }
JS;

$script1 = <<< JS
    //jQuery("div#add-images-div").hide();
    jQuery("#add-images-button").click(function (eventObject) { ShowAddImages(eventObject); });
    jQuery("#refresh-images-button").click(function (eventObject) { RefreshImages(eventObject); });
    jQuery("#clear-images-button").click(function (eventObject) { ClearImages(eventObject); });
    //jQuery(".delete-image-button").click(function (eventObject) { DeleteImage(eventObject); });
JS;

$this->registerJs($script, yii\web\View::POS_END);
$this->registerJs($script1, yii\web\View::POS_READY);
?>