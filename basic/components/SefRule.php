<?php
namespace app\components;

use yii\web\UrlRule;

use app\models\Sef;

class SefRule extends UrlRule
{
	public $connectionID = 'db';
	
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
		$sef = Sef::find()->where(["link" => $link])->one();
		if ($sef) {
			if ($page) return $sef->link_sef.".html?page=$page";
			else return $sef->link_sef.".html";
		}
		return false;
	}
	
	public function parseRequest($manager, $request)
	{
		$pathInfo = $request->getPathInfo();
		if (preg_match('%^(.*)\.html$%', $pathInfo, $matches))
		{
			
			$link_sef = $matches[1];
			$sef = Sef::find()->where(["link_sef" => $link_sef])->one();
			if ($sef) {
				$link_data = explode("?", $sef->link);
				$route = $link_data[0];
				$params = array();
				if ($link_data[1])
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