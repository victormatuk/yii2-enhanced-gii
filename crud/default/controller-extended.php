<?php
use yii\helpers\StringHelper;

$modelClass = StringHelper::basename($generator->modelClass);
$urlParams = $generator->generateUrlParams();
$skippedRelations = array_map(function($value){
    return "'$value'";
},$generator->skippedRelations);

echo "<?php\n";
?>

namespace <?= $generator->nsController ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else : ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \<?= $generator->nsController ?>\base\<?= $className ?> as Base<?= $className ?>;

class <?= $className ?> extends Base<?= $className . "\n" ?>
{
    public function actionCreate()
    {
        $model = new <?= $modelClass ?>();

        <?php
        foreach ($generator->getTableSchema()->foreignKeys as $fk) {
            $fkColumn = array_keys($fk)[1]; // normalmente só tem uma chave (id_application)
            echo "\$model->$fkColumn = Yii::\$app->request->get('$fkColumn');\n";
        }
        ?>        

        if ($model->loadAll(Yii::$app->request->post()<?= !empty($generator->skippedRelations) ? ", [".implode(", ", $skippedRelations)."]" : ""; ?>) && $model->save(<?= !empty($generator->skippedRelations) ? "[".implode(", ", $skippedRelations)."]" : ""; ?>)) {
            return $this->redirect(['view', <?= $urlParams ?>]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
}