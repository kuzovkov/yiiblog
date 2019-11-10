<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Courses extends ActiveRecord
{
	public $link;
	public $img;
	public $order;
	
	public function afterFind() {
		$this->link = "http://srs.myrusakov.ru/".$this->alias;
		$this->img = "/web/images/courses/".$this->alias.".png";
		$this->order = "http://srs.myrusakov.ru/order?product_ids=".$this->srs_id;
	}

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->db->tablePrefix . 'courses';
    }
}
?>