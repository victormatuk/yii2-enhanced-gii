<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator mootensai\enhancedgii\crud\Generator */

$urlParams = $generator->generateUrlParams();
$tableSchema = $generator->getTableSchema();
$pk = empty($tableSchema->primaryKey) ? $tableSchema->getColumnNames()[0] : $tableSchema->primaryKey[0];
$fk = $generator->generateFK($tableSchema);
echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' =>
<?= ($generator->pluralize) ? $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) : $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>,
'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">

    <div class="flex justify-content-between align-items-center mb-3">
        <h1 class="text-3xl font-bold">
            <?= "<?= " ?><?= $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>.'
            '. Html::encode($this->title) ?>
        </h1>
        <div class="btn-group">
            <?php if ($generator->pdf): ?>
                <?= "<?= " ?>
                <?= "
             Html::a('<i class=\"fas fa-hand-o-up\"></i> ' . " . $generator->generateString('PDF') . ", 
                ['pdf', $urlParams],
                [
                    'class' => 'btn btn-danger btn-sm d-flex align-items-center',
                    'target' => '_blank',
                    'data-toggle' => 'tooltip',
                    'title' => " . $generator->generateString('Will open the generated PDF file in a new window') . "
                ]
            )?>\n"
                    ?>
            <?php endif; ?>
            <?php if ($generator->saveAsNew): ?>
                <?= "            <?= Html::a(" . $generator->generateString('Save As New') . ", ['save-as-new', " . $generator->generateUrlParams() . "], ['class' => 'btn btn-info btn-sm d-flex align-items-center']) ?>" ?>
            <?php endif; ?>

            <?php foreach ($relations as $name => $rel): ?>
                <?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
                    <?= "<?= Html::a(Yii::t('app', 'Add " . Inflector::camel2words($rel[1]) . "'), ['/" . Inflector::camel2id($rel[1]) . "/create', 'id_" . Inflector::camel2id(StringHelper::basename($generator->modelClass), '_') . "' => \$model->" . $pk . "], ['class' => 'btn btn-primary btn-sm d-flex align-items-center']) ?>\n" ?>
                <?php endif; ?>
            <?php endforeach; ?>

            <?= "
            <?= Html::a(" . $generator->generateString('Update') . ", ['update', " . $generator->generateUrlParams() . "], ['class' => 'btn btn-success btn-sm d-flex align-items-center']) ?>
            <?= Html::a(" . $generator->generateString('Delete') . ", ['delete', " . $generator->generateUrlParams() . "], [
                'class' => 'btn btn-danger btn-sm d-flex align-items-center',
                'data' => [
                    'confirm' => " . $generator->generateString('Are you sure you want to delete this item?') . ",
                    'method' => 'post',
                ],
            ])
            ?>\n" ?>
        </div>
    </div>

    <div class="card border-secondary mb-3">
        <div class="card-header bg-secondary text-white">
            <p class="text-lg font-bold">
                <!-- <i class="fa-solid fa-window-maximize" style="margin-right: 10px;"></i> -->
                <?= "<?= " ?><?= $generator->generateString(StringHelper::basename($generator->modelClass)) ?><?= "?>" ?>
            </p>
        </div>
        <?= "<?php \n" ?>
        $gridColumn = [
        <?php
        if ($tableSchema === false) {
            foreach ($generator->getColumnNames() as $name) {
                if (++$count < 6) {
                    echo "            '" . $name . "',\n";
                } else {
                    echo "            // '" . $name . "',\n";
                }
            }
        } else {
            foreach ($tableSchema->getColumnNames() as $attribute) {
                if (!in_array($attribute, $generator->skippedColumns)) {
                    echo "        " . $generator->generateDetailViewField($attribute, $fk, $tableSchema);

                }
            }
        } ?>
        ];
        echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
        ]);
        ?>
    </div>

    <?php
    // Ordenar relações filhas primeiro
    uasort($relations, function ($a, $b) {
        if ($a[2] === $b[2]) {
            return 0;
        }
        return ($a[2] === false) ? -1 : 1;
    });
    ?>

    <?php foreach ($relations as $name => $rel): ?>
        <?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
            <div class="row mb-3">
                <?= "<?php\n" ?>
                if($provider<?= $rel[1] ?>->totalCount){
                $gridColumn<?= $rel[1] ?> = [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'contentOptions' => ['class' => 'whitespace-nowrap px-2 w-1']
                ],
                <?php
                $relTableSchema = $generator->getDbConnection()->getTableSchema($rel[3]);
                $fkRel = $generator->generateFK($relTableSchema);
                if ($tableSchema === false) {
                    foreach ($relTableSchema->getColumnNames() as $attribute) {
                        if (!in_array($attribute, $generator->skippedColumns)) {
                            echo "            '" . $attribute . "',\n";
                        }
                    }
                } else {
                    foreach ($relTableSchema->getColumnNames() as $attribute) {
                        if (!in_array($attribute, $generator->skippedColumns)) {
                            echo '            ' . $generator->generateGridViewField($attribute, $fkRel, $relTableSchema);
                        }
                    }
                }
                ?>
                ];
                echo Gridview::widget([
                'dataProvider' => $provider<?= $rel[1] ?>,
                'pjax' => true,
                'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-<?= Inflector::camel2id($rel[3]) ?>']],
                'panel' => [
                'type' => GridView::TYPE_SECONDARY,
                'heading' => '<p class="text-lg font-bold"><span class="fas fa-book"></span> ' .
                    Html::encode(<?= $generator->generateString(
                        Inflector::pluralize(Inflector::camel2words($rel[1]))
                    ) ?>) . '</p>',
                ],
                <?php if (!$generator->pdf): ?>
                    'export' => false,
                <?php endif; ?>
                'toggleData' => false,
                'columns' => $gridColumn<?= $rel[1] . "\n" ?>
                ]);
                }
                <?= "?>\n" ?>
            </div>
        <?php elseif (empty($rel[2])): ?>
            <div class="card border-secondary mb-3">
                <div class="card-header bg-secondary text-white">
                    <p class="text-lg font-bold">
                        <!-- <i class="fa-solid fa-window-maximize" style="margin-right: 10px;"></i> -->
                        <?= "<?= Yii::t('app', '" . $rel[1] . "'); ?>" ?>
                    </p>
                </div>
                <?= "<?php \n" ?>
                $gridColumn<?= $rel[1] ?> = [
                <?php
                $relTableSchema = $generator->getDbConnection()->getTableSchema($rel[3]);
                $fkRel = $generator->generateFK($relTableSchema);
                foreach ($relTableSchema->getColumnNames() as $attribute) {
                    if ($attribute == $rel[5]) {
                        continue;
                    }
                    if ($relTableSchema === false) {
                        if (!in_array($attribute, $generator->skippedColumns)) {
                            echo "        '" . $attribute . "',\n";
                        }
                    } else {
                        if (!in_array($attribute, $generator->skippedColumns)) {
                            echo "        " . $generator->generateDetailViewField($attribute, $fkRel);
                        }
                    }
                }
                ?>
                ];
                echo DetailView::widget([
                'model' => $model-><?= $name ?>,
                'attributes' => $gridColumn<?= $rel[1] ?>
                ]);
                ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>