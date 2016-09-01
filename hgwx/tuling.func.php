<?php
// 图灵机器人
function tuling($keyword) {
	$key="1629fb99ed7f5d583941191675bbfb89";//到这里申请图灵api
	$api_url = "http://www.tuling123.com/openapi/api?key=".$key."&info=". $keyword;
	
	$result = file_get_contents ( $api_url );
	$result = json_decode ( $result, true );
	
	switch ($result ['code']) {

		case '200000' :
			$text = $result ['text'] . ',<a href="' . $result ['url'] . '">点击进入</a>';
			return $text;
			break;
		case '301000' :
            $length = count($result['list']) > 9 ? 9 :count($result['list']);
			for($i= 0;$i<$length;$i++){ 
				$articles [$i] = array (
						'Title' => $result['list'][$i]['name'],
						'Description' => $result['list'][$i]['author'],
						'PicUrl' => $result['list'][$i]['icon'],
						'Url' => $result['list'][$i]['detailurl'] 
				);
			}
			return $articles;
			break;
		case '302000' :
		    $length = count($result['list']) > 9 ? 9 :count($result['list']);
			for($i= 0;$i<$length;$i++){
				$articles [$i] = array (
						'Title' => $result['list'][$i]['article'],
						'Description' => $result['list'][$i]['source'],
						'PicUrl' => $result['list'][$i]['icon'],
						'Url' => $result['list'][$i]['detailurl'] 
				);
			}
			return $articles;
			break;
		case '304000' :
		    $length = count($result['list']) > 9 ? 9 :count($result['list']);
			for($i= 0;$i< $length;$i++){
				$articles [$i] = array (
						'Title' => $result['list'][$i]['name'],
						'Description' => $result['list'][$i]['count'],
						'PicUrl' => $result['list'][$i]['icon'],
						'Url' => $result['list'][$i]['detailurl'] 
				);
			}
			return $articles;
			break;
		case '305000' :
		    $length = count($result['list']) > 9 ? 9 :count($result['list']);
			for($i= 0;$i<$length;$i++){
				$articles [$i] = array (
						'Title' => $result['list'][$i]['start'] . '--' . $result['list'][$i]['terminal'],
						'Description' => $result['list'][$i]['starttime'] . '--' . $result['list'][$i]['endtime'],
						'PicUrl' => $result['list'][$i]['icon'],
						'Url' => $result['list'][$i]['detailurl'] 
				);
			}
			return $articles;
			break;
		case '306000' :
		    $length = count($result['list']) > 9 ? 9 :count($result['list']);
			for($i= 0;$i<$length;$i++){
				$articles [$i] = array (
						'Title' => $result['list'][$i]['flight'] . '--' . $result['list'][$i]['route'],
						'Description' => $result['list'][$i]['starttime'] . '--' . $result['list'][$i]['endtime'],
						'PicUrl' => $result['list'][$i]['icon'],
						'Url' => $result['list'][$i]['detailurl'] 
				);
			}
			return $articles;
			break;
		case '307000' :
		    $length = count($result['list']) > 9 ? 9 :count($result['list']);
			for($i= 0;$i<$length;$i++){
				$articles [$i] = array (
						'Title' => $result['list'][$i]['name'],
						'Description' => $result['list'][$i]['info'],
						'PicUrl' => $result['list'][$i]['icon'],
						'Url' => $result['list'][$i]['detailurl'] 
				);
			}
			return $articles;
			break;
		case '308000' :
		    $length = count($result['list']) > 9 ? 9 :count($result['list']);
			for($i= 0;$i<$length;$i++){
				$articles [$i] = array (
						'Title' => $result['list'][$i]['name'],
						'Description' => $result['list'][$i]['info'],
						'PicUrl' => $result['list'][$i]['icon'],
						'Url' => $result['list'][$i]['detailurl'] 
				);
			}
			return $articles;
			break;
		case '309000' :
		    $length = count($result['list']) > 9 ? 9 :count($result['list']);
			for($i= 0;$i<$length;$i++){
				$articles [$i] = array (
						'Title' => $result['list'][$i]['name'],
						'Description' => '价格 : ' . $result['list'][$i]['price'] . ' 满意度 : ' . $result['list'][i]['satisfaction'],
						'PicUrl' => $result['list'][$i]['icon'],
						'Url' => $result['list'][$i]['detailurl'] 
				);
			}
			return $articles;
			break;
		case '310000' :
		    $length = count($result['list']) > 9 ? 9 :count($result['list']);
			for($i= 0;$i<$length;$i++){
				$articles [$i] = array (
						'Title' => $result['list'][$i]['number'],
						'Description' => $result['list'][$i]['info'],
						'PicUrl' => $result['list'][$i]['icon'],
						'Url' => $result['list'][$i]['detailurl'] 
				);
			}
			return $articles;
			break;
		case '311000' :
		    $length = count($result['list']) > 9 ? 9 :count($result['list']);
			for($i= 0;$i<$length;$i++){
				$articles [$i] = array (
						'Title' => $result['list'][$i]['name'],
						'Description' => '价格 : ' . $result['list'][$i]['price'],
						'PicUrl' => $result['list'][$i]['icon'],
						'Url' => $result['list'][$i]['detailurl'] 
				);
			}
			return $articles;
			break;
		case '312000' :
		    $length = count($result['list']) > 9 ? 9 :count($result['list']);
			for($i= 0;$i<$length;$i++){
				$articles [$i] = array (
						'Title' => $result['list'][$i]['name'],
						'Description' => '价格 : ' . $result['list'][$i]['price'],
						'PicUrl' => $result['list'][$i]['icon'],
						'Url' => $result['list'][$i]['detailurl'] 
				);
			}
			return $articles;
			break;
		default :
			if (empty ( $result ['text'] )) {
				return false;
			} else {
				return $result ['text'] ;
			}
	}
	
}