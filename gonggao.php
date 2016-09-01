<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "weixin");
define("HOSTURL","http://youth.scu.edu.cn");
$wechatObj = new wechatCallbackapiTest();
if(!empty($_GET["echostr"])){
  $wechatObj->valid();
}else{
  $wechatObj->responseMsg();
}


class wechatCallbackapiTest
{
  public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
          echo $echoStr;
          exit;
        }
    }

    public function responseMsg()
    {
    //get post data, May be due to the different environments
    //$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	$postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");

        //extract post data
    if (!empty($postStr)){
                include_once('conn.php');
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $type = trim($postObj->MsgType); 
                $openid=$fromUsername;
                $picurl=trim($postObj->PicUrl);
                $label=trim($postObj->Label);//地理位置信息
                $location_X=trim($postObj->Location_X);//地理位置维度
                 $location_Y=trim($postObj->Location_Y);//地理位置精度
                $textTpl = "<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>%s</CreateTime>
              <MsgType><![CDATA[%s]]></MsgType> 
              <Content><![CDATA[%s]]></Content>
              <FuncFlag>0</FuncFlag>
              </xml>";       

              switch ($type) {
                    case 'text':
                        switch ($keyword) {
                          case '公告':
                          $rs_arr=$this->get_notice();
                          foreach ($rs_arr as $key => $value) {
                            $out_str[]=array("【{$value[time]}】".$value['tetli'],"","http://loujianan.gotoip55.com/notice/img/{$key}.png",HOSTURL."{$value[url]}");
                          }

                             
                                $textTpl = $this->fun_xml("news",$out_str,array(count($rs_arr),0));  
                                    $msgType = "news";
                            
                            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                            echo $resultStr;
                            exit;

                            break;
                          
                          default:
                            echo "";
                            exit();
                            break;
                        }
                      break;
                    
                    default:
                     echo "";
                     exit();
                    break;
                  }    
      
        }else {
          echo "";
          exit;
        }
    }
function get_notice(){
        $url="http://youth.scu.edu.cn/index.php/main/web/notice";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $ret =curl_exec($ch);
        curl_close($ch);

        preg_match_all('#<li><span>(.*?)</span><a href="(.*?)">(.*?)</a></li>#',$ret,$con);
        unset($con[0]);
        for($i=0;$i<10;$i++){
            $rs_arr[]=array('time'=>$con[1][$i],'url'=>$con[2][$i],'tetli'=>$con[3][$i]);

        }
        return $rs_arr;
        }

  function fun_xml($type,$value_arr,$o_arr=array(0)){
 //=================xml header============
 $con="<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>";
      //=================type content============
 switch($type){
 
   case "text" : 
 $con.="<Content><![CDATA[{$value_arr}]]></Content>
<FuncFlag>{$o_arr}</FuncFlag>";  
break;
 

case "news" : 
 $con.="<ArticleCount>{$o_arr[0]}</ArticleCount>
<Articles>";
foreach($value_arr as $id=>$v){
if($id>=$o_arr[0]) break; else null; //判断数组数不超过设置数
         $con.="<item>
<Title><![CDATA[{$v[0]}]]></Title> 
<Description><![CDATA[{$v[1]}]]></Description>
<PicUrl><![CDATA[{$v[2]}]]></PicUrl>
<Url><![CDATA[{$v[3]}]]></Url>
</item>";
}
$con.="</Articles>
<FuncFlag>{$o_arr[1]}</FuncFlag>";  
break;
 } //end switch
 
//=================end return============
 return $con."</xml>";
}
  private function checkSignature()
  {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];  
            
    $token = TOKEN;
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );
    
    if( $tmpStr == $signature ){
      return true;
    }else{
      return false;
    }
  }
}

?>