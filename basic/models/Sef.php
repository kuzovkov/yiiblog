<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Sef extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->db->tablePrefix . 'sef';
    }
}
?>