<?php

namespace tests\unit\fixtures\common\models;

use Sil\yii\test\ActiveFixture;

class EmailFixture extends ActiveFixture
{
    public $modelClass = 'common\models\Email';
    public $dataFile = 'tests/unit/fixtures/data/common/models/Email.php';
    public $depends = [];
}
