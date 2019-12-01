<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Minicourses extends ActiveRecord
{
	public $max_file_size = 5000000;
    public $images_url = "/images/minicourses/";
    public $relative_images_dir = "images/minicourses";
    public $no_img_url = "/images/default.png";

    public function afterFind() {
		$this->img = $this->images_url.$this->img;
	}

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->db->tablePrefix . 'minicourses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'did', 'default'], 'integer'],
            [['title'], 'safe'],
            [['img'], 'file', 'extensions' => 'jpeg, jpg, png']

        ];
    }
}
?>