<?php
echo "<?php\n";
?>

namespace <?= $generator->nsController ?>;

use Yii;
use \<?= $generator->nsController ?>\base\<?= $className ?> as Base<?= $className ?>;

class <?= $className ?> extends Base<?= $className . "\n" ?>
{
}