<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Posts extends ActiveRecord
{
	public $number;
	public $link;
	
	public function afterFind() {
		$monthes = [
			1 => 'Января', 2 => 'Февраля', 3 => 'Марта', 4 => 'Апреля',
			5 => 'Мая', 6 => 'Июня', 7 => 'Июля', 8 => 'Августа',
			9 => 'Сентября', 10 => 'Октября', 11 => 'Ноября', 12 => 'Декабря'
		];
		$this->date = date('j ', $this->date).$monthes[date('n', $this->date)].date(', Y', $this->date);
		$this->intro_text = $this->replaceContent($this->intro_text);
		$this->full_text = $this->replaceContent($this->full_text);
		$this->link = Yii::$app->urlManager->createUrl(["site/post", "id" => $this->id]);
		$this->img = "/web/images/posts/".$this->img;
	}
	
	public function replaceContent($text) {
		$text = $this->youtube($text);
		$text = $this->flowplayer($text);
		return $text;
	}
	
	private function youtube($text) {
		if (strpos($text, "youtube") === false) return $text;
		$reg = "/{youtube:([\w-_]*)?,(\d*)?,(\d*)?}/i";
		$text = preg_replace($reg, str_replace(array("%name%", "%width%", "%height%"), array("\\1", "\\2", "\\3"), file_get_contents(Yii::$app->basePath.Yii::$app->params["dir_tmpl"]."youtube.tpl")), $text);
		return $text;
	}
	
	private function flowplayer($text) {
		if (strpos($text, "flowplayer") === false) return $text;
		$reg = "/{flowplayer:([\w-_]*)?,(\d*)?,(\d*)?}/i";
		$text = preg_replace($reg, str_replace(array("%name%", "%width%", "%height%"), array("\\1", "\\2", "\\3"), file_get_contents(Yii::$app->basePath.Yii::$app->params["dir_tmpl"]."flowplayer.tpl")), $text);
		return $text;
	}
	
	public static function setNumbers($posts) {
		$all_releases = Posts::find()->where(['is_release' => 1])->orderBy("date")->all();
		$number = 1;
		foreach ($all_releases as $release) {
			foreach ($posts as $post) {
				if ($post->id == $release->id) $post->number = $number;
			}
			$number++;
		}
	}

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->db->tablePrefix . 'posts';
    }
}
?>