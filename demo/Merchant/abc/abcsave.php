
<?php

$tempmsg=$_REQUEST["msg"];
if(isset($tempmsg)){
	 $conn=mysql_connect("10.20.188.115", "root", "root");       

	 mysql_query("set character set 'utf8'");//读库 
   mysql_query("set names 'utf8'");//写库 
	 mysql_select_db("lottery", $conn); 
	 $sql_str="insert into abc(msg) values('".$tempmsg."')";  
	 $result = mysql_query($sql_str);
      if ($result) 
         echo "<p>更新成功！</p>";
      else 
         echo "<p>更新失败。</p>";

	
	/*
	 $result=mysql_db_query("spos_work",  $sql_str, $conn);
	    // 获取查询结果
	     $row=mysql_fetch_row($result);
	    
	  
	    $temp_str="";   
	    mysql_data_seek($result, 0);
	
	$totalrow=mysql_affected_rows($conn);
	echo "<!--行数 $totalrow -->" ;
	*/
	    // 释放资源
	    mysql_free_result($result);
	    // 关闭连接
	    mysql_close($conn);
}  
?> 
