<?php 

//dbname = weixin';//这里填写你数据库的名称

$dbname = 'weixin';
 
				   /*从环境变量里取出数据库连接需要的参数*/
				   $host = '127.0.0.1';//数据库的地址
				   $port = '3306';
				   $user = 'root';//数据库的用户名
				   $pwd = '0000';//数据库密码
			 
				   /*接着调用mysql_connect()连接服务器*/
				   //$link = @mysql_connect("{$host}:{$port}",$user,$pwd,true);
					$link = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS); 
				   if(!$link) {
							   die("Connect Server Failed: " . mysql_error($link));
							  }
				   /*连接成功后立即调用mysql_select_db()选中需要连接的数据库*/
				   if(!mysql_select_db($dbname,$link)) {
							   die("Select Database Failed: " . mysql_error($link));
							  }
							  //以上连接数据库


mysql_query("SET NAMES 'utf8'");
?>
