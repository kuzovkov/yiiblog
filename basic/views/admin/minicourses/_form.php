<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Minicourses */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="minicourses-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'id')->textInput(['disabled' => 'disabled']) ?>

    <?= $form->field($model, 'did')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'img')->fileInput(['label' => 'Image', 'id' => 'minicourses-img']) ?>

    <?php if ($model->img && file_exists($model->relative_images_dir.'/'.basename($model->img))):?>
        <img id="img-preview" src="<?php echo $model->img; ?>" style="max-width: 50px; max-height: 50px;" />
    <?php else: ?>
        <img id="img-preview" src="<?php echo $model->no_img_url; ?>" style="max-width: 50px; max-height: 50px;" />
    <?php endif;?>

    <?= $form->field($model, 'default')->checkbox(['value' => true, 'label' => 'Default']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<div id="error-msg"></div>

<script>
    (function (){
        var inputElement = document.querySelector("#minicourses-img");
        inputElement.addEventListener("change", handleFiles, false);
        function handleFiles() {
            document.querySelector('div#error-msg').innerHTML = "";
            var fileList = this.files;
            var file = fileList[0];
            console.log(file);
            if ( <?php echo $model->max_file_size;?> <  file.size ){
                this.files = null;
                this.value = '';
                document.querySelector('div#error-msg').innerHTML = "Selected file is too big";
            }else{
                var img = document.getElementById("img-preview");
                img.classList.add("obj");
                img.file = file;
                //console.log(file);
                var reader = new FileReader();
                reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
                reader.readAsDataURL(file);
            }
        }
    })();
</script>
