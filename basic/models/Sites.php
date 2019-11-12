<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Sites extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->db->tablePrefix . 'sites';
    }
}
?>