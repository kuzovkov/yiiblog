<?php
namespace app\models;

use yii\base\Model;

class SiteForm extends Model
{
	public $address;
	public $description;
	
	public function rules()
	{
		return [
			['address', 'required', 'message' => 'Введите адрес сайта'],
			['description', 'required', 'message' => 'Введите описание сайта'],
			['address', 'url', 'message' => 'Некорректный адрес сайта']
		];
	}
}

?>