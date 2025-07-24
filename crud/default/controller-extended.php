<?php
echo "<?php\n";
?>

namespace <?= $generator->nsController ?>;

use Yii;
use \<?= $generator->nsController ?>\base\<?= $className ?> as Base<?= $className ?>;

class <?= $className ?> extends Base<?= $className . "\n" ?>
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        if (isset($behaviors['access']['rules'][0]['actions'])) {
            //regras adicionais, exemplo
            // $additionalActions = ['json', 'export', 'sync', 'preview'];
            $additionalActions = [''];

            // Evita duplicatas ao mesclar
            $behaviors['access']['rules'][0]['actions'] = array_unique(array_merge(
                $behaviors['access']['rules'][0]['actions'],
                $additionalActions
            ));
        }

        return $behaviors;
    }

}