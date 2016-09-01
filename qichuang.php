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
        $date=date('Y-m-d');
        $time=date('H:i:s');
        $openid= $fromUsername;
                $textTpl = "<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>%s</CreateTime>
              <MsgType><![CDATA[%s]]></MsgType>
              <Content><![CDATA[%s]]></Content>
              <FuncFlag>0</FuncFlag>
              </xml>";             
        if(!empty( $keyword ))
                {
                    $str1 = $keyword;
          $arr = array();
          for($i = 0, $len = strlen($str1); $i < $len; ++$i) {
              if(!count($arr) || $arr[count($arr) - 1] !== ' ' || $str1{$i} !== ' ') {
            $arr[] = $str1{$i};
           }    
          }   
          $keyword= implode('', $arr);
          
         $myarr_str=explode(" ",$keyword);
         $keyword=$myarr_str[0];
         
         //=========================================================================
         
          switch($keyword){
            case '起床':
              $sql2="select `name` from `user` where `openid`='{$openid}'" ;
              $query2=mysql_query($sql2);
              $rs= mysql_num_rows($query2);
              $rs_name= mysql_fetch_array($query2);
              if(!empty($rs)&&$rs==1){
               $name = urldecode( $rs_name[name]);
               $date=date('Y-m-d',time());
            $s_time=strtotime($date.' 06:30:00');
             $e_time=strtotime($date.' 08:00:00');
               if (time()>=$s_time&&time()<$e_time)//设置起床报道时间，
                {
                  $sql="SELECT count(*) FROM `db_morning` WHERE `time` >= '$date'";
                  $query=mysql_query($sql);
                  $Y=mysql_fetch_row($query);
                  $sql_num="SELECT count(*) FROM `db_morning` WHERE time >= '$date' and openid ='{$openid}'";
              
                  $query_num=mysql_query($sql_num);
                  
                  $rs_num=mysql_fetch_row($query_num);
                  $chongfunum=$rs_num[0];
                  $num=$Y[0];
                $num1=$num+1;
                 if ($chongfunum!=0){
                    $str= "你今天已经起床了哟!";
                    } else
                    {
                      $sql1="INSERT INTO `db_morning` (id,user,time,paiming,openid)values(NULL,'{$name}',NULL,'{$num1}','{$openid}')";
                      $result=mysql_query($sql1);
                      $sql_out="select * from db_morning where `time` >= '$date' order by paiming asc limit 10";
                      $query_out=mysql_query($sql_out);
                                          $sql3="select * from db_morning order by id desc limit 1";
                      $query3=mysql_query($sql3);
                      $last=mysql_fetch_array($query3);
                                          $myarr = array(); //定义数组
                                          $i=0;
                       while ($rs_out=mysql_fetch_array($query_out)){//循环赋值，将结果放入组数中。
                        $i++;
                                               $sql_int="SELECT * FROM `user` WHERE `openid`='{$openid}'";
                        $query_int=mysql_query($sql_int);
                        $rs_int=mysql_fetch_array($query_int);
                        $integral=$rs_int[integral];
                        $arrname=$rs_out['user'];
                        $arrtime=$rs_out['time'];
                        $arrtime1=date('H:i',strtotime($arrtime));
                        $arrtop=$rs_out[paiming];
                        if ( isset($myarr[$paiming])){
                        $myarr[$paiming].="NO.".$arrtop."      ".$arrname."      ".$arrtime1."\n";
                          
                          

                        }else{
                        $myarr[$paiming]="NO.".$arrtop."      ".$arrname."      ".$arrtime1."\n";
                        
                          

                       }
                       
                       }//while结束
                                          if($rs_int[openid]==$last[openid]){
                          if($last[paiming]>0&&$last[paiming]<10){
                                                        $integral=$integral+10-$arrtop+1;
                            $sql_int_up="UPDATE `user` SET `integral`={$integral} WHERE `openid`='{$openid}'";
                                                       
                            $query_up=mysql_query($sql_int_up);
                                                      }
                                                  else{
                                                       $integral=$integral+1;
                            $sql_int_up="UPDATE `user` SET `integral`={$integral} WHERE `openid`='{$openid}'";
                                                           
                          $query_up=mysql_query($sql_int_up);
                                                      }
                          }
                                          
                                          
                                          
                       if($myarr)
                        {
                          foreach( $myarr as $value )
                          {
                           $qichuang.=$value;

                          }
                          
                        }//数组判断结束
                        $sql3="select * from db_morning order by id desc limit 1";
                      $query3=mysql_query($sql3);
                      $last=mysql_fetch_array($query3);
                      $lasttime=$last['time'];//最后一条记录的时间
                      $lasttime1=date('H:i',strtotime($lasttime));//时间取消秒
                      $lastpaiming=$last[paiming];//最后一条记录的排名
                      $sql_last="SELECT * FROM `user` WHERE `openid`='{$openid}'";
                      $query_last=mysql_query($sql_last);
                      $rs_last=mysql_fetch_array($query_last);
                      $str="四川大学起床排行榜："."\n"."\n".$qichuang."\n"."你的起床时间为：".$lasttime1."\n"."是今天第".$lastpaiming."个起床的哟!你目前的积分有:".$rs_last[integral]."分"."\n"."小伙伴们，小川的福利是不是很棒？希望大家能把早起的习惯保持，期末门门90+！";
                      
                    
                    }
                }//起床时间if设置结束
                else if(time()<$s_time)
                {
                  $str=$name."，现在离起床还早呢，再睡一会吧！"."六点半到八点来哦";//这里是如果起床时间早于4点，进行的提示
                  } 
                  else if(time()>=$e_time&&$time<=19)
                  {
                  $str=$name."，亲，小伙伴们已经起床了呢，你迟到了哦！"."六点半到八点再来哦~";//这里是如果起床时间是中午12点过后，进行的提示
                  } 
                    else {
                    $str=$name."，太阳都下山啦！你还是明天再来吧！"."六点半到八点再来哦~";//这里是如果起床时间19点后，进行的提示
                    
                    }
                  
                  
                  } else if($rs==0){
                  
                   $str="请输入\"叫我 姓名\"进行绑定，如：叫我 MT。一定要在中间加空格哦！绑定能参与起床排名哦！";//这里是如果没有进行绑定，进行的提示
                  
                  
                  }
              
              
              
              
                        
            break;//起床的结束
            case '活动规则':
	$str="1.参加起床大作战，首先你需要绑定自己的昵称，输入\"叫我 昵称\"进行绑定，如：叫我 MT。一定要在中间加空格哦！绑定能参与起床排名。\n 2.修改昵称，输入\"修改昵称 昵称\"进行绑定，如：修改昵称 TM。\n 3.每天六点半到八点你都可以通过输入“起床”来签到起床，每天前十名起床的筒子们可以获得10个积分，其他在规定时限起床的筒子们可以获得1个积分。\n 4.输入“起床排行榜”查看每天起床前十。\n 5.输入“积分”膜拜校园早起达人。\n 6.输入“我的积分”查看自己已经获得的积分。\n";
           break;

            case '叫我':
            $sql_named="select `name` from `user` where `openid`='{$openid}'" ;
            $query_named=mysql_query($sql_named);
             $rs_named= mysql_fetch_array($query_named);
            if(($rs_named)&&$rs_named>=1){
             $str="您的昵称已绑定，需要更改绑定的昵称，请发送 【修改昵称 新昵称】，中间一定要有空格哦~";
            }
            else{
                    $srt_len=count($myarr_str);
                    if($srt_len>2){
                      unset($myarr_str[0]);
                      foreach($myarr_str as $value){
                      $name=$name.' '.$value;
                      }
                    
                    
                    }else {$name=$myarr_str[1];}  
          $sql3="INSERT INTO `user` (id,name,openid,integral) VALUES  (null,'{$name}','{$openid}',0) " ;
             
            
            $query3=mysql_query($sql3);
             
            
        
          $sql_name="select `name` from `user` where `openid`='{$openid}' " ;
          $query_name=mysql_query($sql_name);
          $rs_name= mysql_fetch_array($query_name);
           $str="绑定成功，您的昵称是：$rs_name[name]；请输入起床，参与四川大学排名争霸！";//这里是绑定成功，进行的提示
      
        }
            break;//绑定的结束
            case '起床排行榜':
            
            $sql2="select * from db_morning where `time` >= '$date' order by paiming asc limit 10";//进行结果的输出，将满足条件的记录输出出来。
            $query2=mysql_query($sql2);

            $myarr = array(); //定义数组

                while ($q=mysql_fetch_row($query2)){//循环赋值，将结果放入组数中。
                     
                     $arrname=$q[1];//结合我前面表的结果，这里$q[1]是user字段
                     $arrtime=$q[2];//结合我前面表的结果，这里$q[2]是time字段
                     $arrtime1=date('H:i',strtotime($arrtime));//将时间截取小时和分钟，去掉秒
                     $arrpaiming=$q[3];//结合我前面表的结果，这里$q[3]是paiming字段
                  
                       
                       if ( isset($myarr[$paiming]))
                       $myarr[$paiming].="NO.".$arrpaiming."      ".$arrname."      ".$arrtime1."\n";
                       else
                       $myarr[$paiming]="NO.".$arrpaiming."      ".$arrname."      ".$arrtime1."\n";
                       
                  
                } 

            if($myarr)
            {

              foreach( $myarr as $value )
              {

                      $qichuang.=$value;

              }
              
            }
                        
              $str="四川大学起床排行榜："."\n"."\n".$qichuang."\n，亲！你可以来参与四川大学起床排行哦！输入起床即可查看参与方式！";         
            break;//起床排行榜的结束
            case '积分':
            $top=1;
            $sql2="select * from `user` order by integral DESC limit 10";//进行结果的输出，将满足条件的记录输出出来。
            $query2=mysql_query($sql2);
            while($rs=mysql_fetch_array($query2)){
              $str.="NO.".$top."     ".$rs[name]."     "."积分是".$rs[integral]."分\n";
            $top++;
            }
            $str="四川大学积分排行榜："."\n"."\n".$str;
            
            break;//积分排行榜的结束
                       case '我的积分':
            $sql="SELECT * FROM `user` WHERE `openid`='{$openid}'";
            $query=mysql_query($sql);
            $rs=mysql_fetch_array($query);
            $str="你的积分是：".$rs[integral];
            
            
                    
            break;//我的积分的结束
            case '帮助':
            $str="亲要注意空格哦~"."\n"."发送【BD 学号 密码】进行绑定，如【BD 123 123】"."\n"."发送【课表X】查询星期X的课表，如【课表 1】，中间记得有空格~";
            break;
            case '修改昵称':
                   if(empty($myarr_str[1])){
                        $str="您输入的名称有误，请重新输入！!";
                      }
                      else{
                      $sql_name="select `name` from `user` where `openid`='{$openid}'" ;//先查我们的用户表中有没有绑定openID
            $query_name=mysql_query($sql_name);//提交SQL语句，返回一个资源类的数组
            $rs_name_num= mysql_num_rows($query_name);//得到执行SQL语句受影响的行数，返回一个INT类型的值,用来判断是不是已绑定openID
            $rs_name= mysql_fetch_array($query_name);//得到执行SQL语句返回的数组
          
          if(!empty($rs_name_num)&&$rs_name_num==1){//判断受影响的行数的值，判断是不是已绑定openID
             $name = urldecode( $rs_name[name]);//得到用户的名字
            
             $sql_up="UPDATE `user` SET `name`='{$myarr_str[1]}' WHERE `openid`='".$fromUsername."' ";//插入数据
             $query_con=mysql_query($sql_up);
                         $sql_name="select `name` from `user` where `openid`='".$fromUsername."' " ;//先查我们的用户表中有没有绑定openID
             $query_name=mysql_query($sql_name);//提交SQL语句，返回一个资源类的数组
                       $rs_name= mysql_fetch_array($query_name);//得到执行SQL语句返回的数组
                         $name = urldecode( $rs_name[name]);//得到用户的名字
                      $str="修改昵称成功！你新的昵称是:".$name;
            
          
          }else {
          
           $str="请输入【叫我 昵称】进行绑定，如：【叫我 MT】。需要修改昵称，请输入【修改昵称 名称】进行修改！一定要在中间加空格哦！";//这里是如果没有进行绑定，进行的提示
          
          
          }
                      }
            break;
            
                      
                      
          
          }//switch 结束
         
         
         //=========================================================================
                    
                  $msgType = "text";
                  $contentStr = $str;
                  $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                  echo $resultStr;
                }else{
                  echo "Input something...";
                }

        }else {
          echo "";
          exit;
        }
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