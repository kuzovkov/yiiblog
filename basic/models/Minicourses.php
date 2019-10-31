<?php
namespace app\models;

use yii\db\ActiveRecord;

class Minicourses extends ActiveRecord
{
	public function afterFind() {
		$this->img = "/web/images/minicourses/".$this->img;
	}
}
?>