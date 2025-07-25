<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View  */
/* @var $generator \mootensai\enhancedgii\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
$tableSchema = $generator->getTableSchema();
$baseModelClass = StringHelper::basename($generator->modelClass);
$fk = $generator->generateFK($tableSchema);
echo "<?php\n";
?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "kartik\\grid\\GridView;" : "yii\\widgets\\ListView;" ?>


$this->title = <?= ($generator->pluralize) ? $generator->generateString(Inflector::pluralize(Inflector::camel2words($baseModelClass))) : $generator->generateString(Inflector::camel2words($baseModelClass)) ?>;
$this->params['breadcrumbs'][] = $this->title;
<?php if($generator->advancedSearch){ ?>
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
<? } ?>
?>
<div class="<?= Inflector::camel2id($baseModelClass) ?>-index">

    <h1 class="text-3xl font-bold"><?= "<?= " ?>Html::encode($this->title) ?></h1>
<?php if (!empty($generator->searchModelClass) && $generator->advancedSearch): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

    <p>
        <?= "<?= " ?>Html::a(<?= $generator->generateString('Create ' . Inflector::camel2words($baseModelClass)) ?>, ['create'], ['class' => 'btn btn-success']) ?>
<?php if (!empty($generator->searchModelClass)): ?>
        <?= "<?= " ?>Html::a(<?= $generator->generateString('Advance Search')?>, '#', ['class' => 'btn btn-info search-button']) ?>
<?php endif; ?>
    </p>
<?php if (!empty($generator->searchModelClass) && $generator->advancedSearch): ?>
    <div class="search-form" style="display:none">
        <?= "<?= " ?> $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <?php endif; ?>
<?php 
if ($generator->indexWidgetType === 'grid'): 
?>
<?= "<?php \n" ?>
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
<?php
    if ($generator->expandable && !empty($fk)):
?>
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '50px',
            'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($model, $key, $index, $column) {
                return Yii::$app->controller->renderPartial('_expand', ['model' => $model]);
            },
            'headerOptions' => ['class' => 'kartik-sheet-style'],
            'expandOneOnly' => true
        ],
<?php
    endif;
?>
<?php   
    if ($tableSchema === false) :
        foreach ($generator->getColumnNames() as $name) {
            if (++$count < 6) {
                echo "            '" . $name . "',\n";
            } else {
                echo "            // '" . $name . "',\n";
            }
        }
    else :
        foreach ($tableSchema->getColumnNames() as $attribute): 
            if (!in_array($attribute, $generator->skippedColumns)) :
?>
        <?= $generator->generateGridViewFieldIndex($attribute, $fk, $tableSchema)?>
<?php
            endif;
        endforeach; ?>
        [
            'class' => 'yii\grid\ActionColumn',
<?php if($generator->saveAsNew) { ?>
            'template' => '{save-as-new} {view} {update} {delete}',
        <?php } else { ?>
            'template' => '{view} {update} {delete}',
        <?php } ?>
            'buttons' => [
                'view' => function ($url, $model) {
                    return Html::a('<i class="fa-solid fa-eye"></i>', $url, [
                        'class' => 'btn btn-default btn-sm border-0 btn-view'
                    ]);
                },
                'update' => function ($url, $model) {
                    return Html::a('<i class="fa-solid fa-pencil"></i>', $url, [
                        'class' => 'btn btn-default btn-sm border-0 btn-update',
                    ]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<i class="fa-solid fa-trash"></i>', $url, [
                        'class' => 'btn btn-default btn-sm border-0 btn-delete',
                        'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                    ]);
                },
                <?php if($generator->saveAsNew): ?>
                'save-as-new' => function ($url) {
                    return Html::a('<span class="fas fa-copy"></span>', $url, ['title' => 'Save As New']);
                },
                <?php endif; ?>
            ],
        ],
    ]; 
<?php 
    endif; 
?>
    ?>
    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => \$gridColumn,\n" : "'columns' => \$gridColumn,\n"; ?>
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass))?>']],
        'striped' => true, 
        'bordered' => false,
        'panel' => [
            // 'heading' => false,
           'before' => false,
           'after' => false,
            // 'footer' => false,
            // 'type' => GridView::TYPE_PRIMARY,
            // 'heading' => '<span class="fas fa-book"></span>  ' . Html::encode($this->title),
            // 'headingOptions' => ['class' => 'panel-etc'],
        ],
<?php if(!$generator->pdf) : ?>
        'export' => false,
<?php endif; ?>
        // your toolbar can include the additional full export menu
        'toolbar' => [
        ],
    ]); ?>
<?php 
else: 
?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_index',['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget, 'view' => $this]);
        },
    ]) ?>
<?php 
endif; 
?>

</div>
