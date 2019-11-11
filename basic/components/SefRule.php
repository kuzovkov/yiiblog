<?php
namespace app\components;

use yii\web\UrlRule;

use app\models\Sef;

class SefRule extends UrlRule
{
	public $connectionID = 'db';
	
	private $routemap = [
        'site/video' => 'video',
        'site/rev' => 'rev',
        'site/sites' => 'sites',
        'site/author' => 'author',
        'admin/admin/login' => 'login',
        'admin/admin/logout' => 'logout',
        'admin/admin/index' => 'dashboard',
        'admin/courses' => 'dashboard/courses',
        'admin/courses/create' => 'dashboard/addcourse',
        'admin/minicourses' => 'dashboard/minicourses',
        'admin/minicourses/create' => 'dashboard/addminicourse'

    ];

    public function init()
	{
		if ($this->name === null) $this->name = __CLASS__;
	}
	
	public function createUrl($manager, $route, $params) {
		if ($route == "site/index")
		{
			if (isset($params["page"])) return "?page=".$params["page"];
			else return "";
		}
		if ($route == "site/search")
		{
			if (isset($params["page"])) return "search.html?q=".$params["q"]."&page=".$params["page"];
			else return "search.html?q=".$params["q"];	
		}
        $link = $route;
		$page = null;
        if (count($params)) {
			$link .= "?";
			$page = false;
			foreach ($params as $key => $value)
			{
				if ($key == "page")
				{
					$page = $value;
					continue;
				}
				$link .= "$key=$value&";
			}
			$link = substr($link, 0, -1);
		}
		//$sef = Sef::find()->where(["link" => $link])->one();
		$sef = isset($this->routemap[$link])? $this->routemap[$link] : false;
        if ($sef) {
			if ($page) return $sef.".html?page=$page";
			else return $sef.".html";
		}
		return false;
	}
	
	public function parseRequest($manager, $request)
	{
		$pathInfo = $request->getPathInfo();
		if (preg_match('%^(.*)\.html$%', $pathInfo, $matches))
		{
			$link_sef = $matches[1];
			//$sef = Sef::find()->where(["link_sef" => $link_sef])->one();
			$sef = array_search($link_sef, $this->routemap);
            if ($sef) {
				$link_data = explode("?", $sef);
				$route = $link_data[0];
				$params = array();
				if (isset($link_data[1]) && $link_data[1])
				{
					$temp = explode("&", $link_data[1]);
					foreach ($temp as $t)
					{
						$t = explode("=", $t);
						$params[$t[0]] = $t[1];
					}
				}
				return [$route, $params];
			}
		}
		return false;
	}
}

?>