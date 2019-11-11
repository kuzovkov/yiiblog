<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Minicourses extends ActiveRecord
{
	public function afterFind() {
		$this->img = "/web/images/minicourses/".$this->img;
	}

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->db->tablePrefix . 'minicourses';
    }
}
?>