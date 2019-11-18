<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Sites extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address', 'address'], 'required', 'message' => 'Invalid URL'],
            ['description', 'string'],
            [['active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->db->tablePrefix . 'sites';
    }
}
?>