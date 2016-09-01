<?php
@define("WE_ROOT",  dirname(__FILE__) . "/");            

require_once(WE_ROOT . "wechat.class.php");
require_once(WE_ROOT . "tuling.func.php");
require_once(WE_ROOT . "idioms.func.php");
require_once(WE_ROOT . "UnderCover.func.php");


$options = array(
    'token'=>'weixin', //填写你设定的key
);
$weObj = new Wechat($options);

$weObj->valid();

$type = $weObj->getRev()->getRevType();
$username = $weObj->getRev()->getRevFrom();
$content = $weObj->getRev()->getRevContent();
$content = safe_replace($content);

$mysql = new SaeMysql();
$sql ="select * from wx_users where openid = '$username' ";
$data=$mysql->getLine($sql);
if($data){
	$lock = $data['lock'];
}else{
	$sql ="insert into wx_users(openid) values('$username')";
	$mysql->runSql($sql);
	if ($mysql->errno() != 0){
		die("Error:" . $mysql->errmsg());
	}
	$lock="unlock";
}
$mysql->closeDb();

function safe_replace($string) {
    $string = str_replace('%20','',$string);
    $string = str_replace('%27','',$string);
    $string = str_replace('%2527','',$string);
    $string = str_replace('*','',$string);
    $string = str_replace('"','&quot;',$string);
    $string = str_replace("'",'',$string);
    $string = str_replace('"','',$string);
    $string = str_replace(';','',$string);
    $string = str_replace('<','&lt;',$string);
    $string = str_replace('>','&gt;',$string);
    $string = str_replace("{",'',$string);
    $string = str_replace('}','',$string);
    $string = str_replace(' ','',$string);
    return $string;
}


switch($type) {
	/*关注事件回复*/
	case Wechat::MSGTYPE_EVENT:
    $weObj->text("hello, I'm SCU酷客~\n平台现有功能:\n智能聊天,成语接龙,谁是卧底游戏")->reply();
			exit;
			break;
	/*文本事件回复*/
	case Wechat::MSGTYPE_TEXT:
    if(substr($content, 0, 12) == "成语接龙"){
			$lock = "idioms";
			upuserlock($lock,$username);
			$content = substr($content, 12);
	}
    if(substr($content, 0, 12) == "谁是卧底"){
			$lock = "under_cover";
			upuserlock($lock,$username);
			$content = substr($content, 12);
	}
	if($lock == "idioms"){
		$reply=idioms($content,$username);
		$weObj->text($reply)->reply();
	}elseif ($lock == "under_cover") {
		$reply=UnderCover($content,$username);
		$weObj->text($reply)->reply();
	}
    else{
    	   if(strcasecmp($content) == "help"||$content=="帮助"){
			    $weObj->text("hello, I'm SCU酷客~\n平台现有功能:\n智能聊天,成语接龙,谁是卧底游戏")->reply();
			}else{
				$reply = tuling($content);
				if(gettype($reply)==string){
					$weObj->text($reply)->reply();
				} else if(is_array($reply)) {
					$weObj->news($reply)->reply();
				}
			}
    }
            exit;
			break;
    /*地理事件回复*/
	case Wechat::MSGTYPE_LOCATION:
	        $weObj->text("地理事件")->reply();
	        exit;
			break;
    /*图片事件处理*/
	case Wechat::MSGTYPE_IMAGE:
	        $weObj->text("图片事件")->reply();
			break;
    /*语音事件处理*/
    case Wechat::MSGTYPE_VOICE:
			$weObj->text("语音事件")->reply();
            break;
	default:
			$weObj->text($type."\n "."hello, I'm SCU酷客~\n平台现有功能:\n智能聊天,成语接龙,谁是卧底游戏")->reply();
}

function upuserlock($lock,$username){
	$mysql = new SaeMysql();
	$sql="UPDATE `wx_users` SET  `lock` =  '$lock' WHERE  `openid` =  '$username'";
	$mysql->runSql($sql);
	if ($mysql->errno() != 0)
	{
		die("Error:" . $mysql->errmsg());
	}
	$mysql->closeDb();
}