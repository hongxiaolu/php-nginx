<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">  
<html>  
 <head>  
  <title> 抽奖活动 </title>  
  <meta name="Generator" content="EditPlus">  
  <meta name="Author" content="">  
  <meta name="Keywords" content="">  
  <meta name="Description" content="">  
 </head>  
  
 <body  style="background:url(gzbankbgnew.png) repeat;">  
  
<html>  
<head>  
<link rel="stylesheet" href="common.css">
<meta http-equiv="Content-Type" content="text/html;charset=gb2312"/>  
<meta http-equiv="Content-Language" content="zh-CN"/>    
<title>抽奖活动</title>  
<style type="text/css">  

body {padding-top:100px;font:12px "\5B8B\4F53",sans-serif;text-align:center;}  
.result_box {margin:0 auto;width:700px;padding:100px 0;text-align:center;border:3px solid #40AA53;background:#efe;}  
.result_box1 {margin-left:5%;width:1250px;color:#555;font-size:12pt;font-family:Verdana;text-align:left;border:0px solid red;}  
.result_boxpic {margin:0 auto;width:700px;height:352px;padding:100px 0;text-align:center;border:0px solid #40AA53;background:#efe;background:url(jiangnew.png) center 0 no-repeat;}  
.result_boxpicparent {margin:0 auto;width:700px;height:100px;padding:100px 0;text-align:center;border:0px solid #40AA53;}  
.login_logoright{margin:250px 0px 0px 1200px; position:absolute;}

.result_top{margin:-100px 0px 5px 0px; color:#9400D3; font-size:28px; text-align:center; font-family:"microsoft yahei"; text-shadow:3px 3px 2px rgba(0,0,0,0.2)}
.result_lottery{margin:100px 0px 5px 0px; color:white; font-size:16px;text-align:center; font-family:"microsoft yahei"; text-shadow:3px 3px 2px rgba(0,0,0,0.2)}
.result_download{margin:20px 0px 5px 0px; color:#555; font-size:20px;text-align:center; font-family:"microsoft yahei"; text-shadow:2px 2px 2px rgba(0,0,0,0.2)}

.result_clock{ margin:-60px 20px 0px 20px;  position:absolute; color:white; font-size:32px; font-weight: bold; text-align:center; font-family:"microsoft yahei"; text-shadow:3px 3px 2px rgba(0,0,0,0.2);border:0px solid red;}


.result_box #oknum {width:700px;color:#cc0000;font-size:50pt;font-family:Verdana;text-align:center;border:none;background:#efe;}  
.button_box {margin:5px 0 0 0;}  
.button_box .btn {cursor:pointer;padding:0 30px;margin:0 10px;color:#555;font-family:"\5B8B\4F53",sans-serif;font-size:40px;}  
</style>  
</head>  


<?php
 //$conn=mysql_connect("10.20.188.105", "root", "root");      
 $conn=mysql_connect("10.19.131.118", "root", "root");      
 mysql_query("set character set 'utf8'");//读库      
 $sql_str="
SELECT ord_id,ord_mct_id,mct_name,tbl_order.ord_pmt_name,tbl_order.ord_trade_amount/100, tbl_order.ord_trade_time,tbl_order.ord_status FROM tbl_order  left join tbl_merchant on  tbl_order.ord_mct_no=tbl_merchant.mct_no where  ord_trade_time>='2017-07-20' and ord_status=1 and ord_trade_amount>=1000 order by rand() limit 3000";
 $sql_str="SELECT ord_id,ord_mct_id,mct_name,tbl_order.ord_pmt_name,tbl_order.ord_trade_amount/100, tbl_order.ord_trade_time,tbl_order.ord_status,tbl_agent.agent_name FROM ((select * from tbl_order where ord_trade_time>='2017-07-20' and ord_status=1 and ord_trade_amount>=1000 ) as tbl_order  left join tbl_merchant on  tbl_order.ord_mct_no=tbl_merchant.mct_no) left join tbl_agent on tbl_order.ord_agent_no=tbl_agent.agent_no order by rand() limit 3000";
 $sql_str="SELECT ord_no,ord_mct_no,mct_name,ord_pmt_name,ord_trade_amount/100, ord_trade_time,ord_status,tbl_agent.agent_name FROM ((select * from tbl_order_copy1 where  ord_trade_pay_time>='2017-08-28' and ord_status=1 and ord_trade_amount>=1000  and ord_agent_no in(860000893,860000925,860001062,860001111,860001113,860001115,860001117,860001118,860001512) ) as tbl_order_copy  left join tbl_merchant on  tbl_order_copy.ord_mct_no=tbl_merchant.mct_no) left join tbl_agent on tbl_order_copy.ord_agent_no=tbl_agent.agent_no order by rand() limit 3000";
 $result=mysql_db_query("spos_work",  $sql_str, $conn);
    // 获取查询结果
     $row=mysql_fetch_row($result);
    
    /*
   
   // echo '<font face="verdana">';
    echo '<table border="1" cellpadding="1" cellspacing="2">';

    // 显示字段名称
    echo "</b><tr></b>";
    for ($i=0; $i<mysql_num_fields($result); $i++)
    {
      echo '<td bgcolor="#cc0000"><b>'.
      mysql_field_name($result, $i);
      echo "</b></td></b>";
    }
    echo "</tr></b>";
    // 定位到第一条记录
    mysql_data_seek($result, 0);
    // 循环取出记录
    while ($row=mysql_fetch_row($result))
    {
      echo "<tr></b>";
      for ($i=0; $i<mysql_num_fields($result); $i++ )
      {
        echo '<td bgcolor="#00FF00">';
        echo $row[$i];
        echo '</td>';
      }
      echo "</tr></b>";
    }
   
    echo "</table></b>";
   // echo "</font>";
   
   */
    $temp_str="";   
    mysql_data_seek($result, 0);
    // 循环取出记录
    while ($row=mysql_fetch_row($result))
    {
    	//var_dump($row);
    	$temp_num=round($row[4],2);
    	$temp_row='';
    	$temp_row="$row[2] $row[3] ".$temp_num."元 $row[5] $row[7] 订单号:$row[0]";
    	$temp_row= str_replace(',','',$temp_row);
      $temp_str=$temp_str . $temp_row . ',';
    }
  //   echo $temp_str;
  //返回行记录
$totalrow=mysql_affected_rows($conn);
echo "<!--行数 $totalrow -->" ;
    // 释放资源
    mysql_free_result($result);
    // 关闭连接
    mysql_close($conn);  
?> 
   
<body>  
<script type="text/javascript">  
var alldata = "<?php echo $temp_str; ?>";  
var alldataarr = alldata.split(",");  
var num = alldataarr.length-1;  
var timer; 
var result_str=""; 
var clicknum=0;
var firstagentname="";
function change(){     
    document.getElementById("oknum").value = alldataarr[GetRnd(0,num)];    

}  
function start(){  
	  if(clicknum>=1){
	     alert("抽奖完毕，每期只抽1笔！");
	     return false;
	  }
	  
	  stopflag=0;    
	  result_str=document.getElementById("oknum").value;
    clearInterval(timer);     
    timer = setInterval('change()',20); //随机数据变换速度，越小变换的越快     
} 
var stopflag=-1; 
function ok(){   


    clearInterval(timer); 

	        
    //以下代码表示获得奖的，不能再获奖了。  重置刷新页面即可。  
    alldata = alldata.replace(document.getElementById("oknum").value,"").replace(",,",",");  
    // 去掉前置，最末尾的,  
    if (alldata.substr(0,1)==",")  
    {  
      alldata = alldata.substr(1,alldata.length);  
    }  
    if (alldata.substr(alldata.length-1,1)==",")  
    {  
      alldata = alldata.substring(0,alldata.length-1);  
    }  
    alldataarr = alldata.split(",");    
    num = alldataarr.length-1;    
    if(stopflag==0){
    	 	try{
    	firstagentname=(document.getElementById("oknum").value).split(" ")[0];
      }
	  catch(e){}
	     stopflag=1; 
      clicknum=clicknum+1;
      
    	document.getElementById("resultDiv").style.display="block";
    	var levelname="";
    	if(clicknum<=4){
    	  levelname="三等奖";
    	}
    	if(clicknum>=5 && clicknum<=6){
    	  levelname="<b>二等奖</b>";
    	}
    	if(clicknum==7){
    	  levelname="<font color=red><b>一等奖</b></font>";
    	}
    	levelname="<font color=red><b>终极大奖</b></font>";
    document.getElementById("resultDiv").innerHTML = document.getElementById("resultDiv").innerHTML +"<br>"+ ""+levelname+" "+document.getElementById("oknum").value +"";
 
    try{
        var tempArray=new Array();
        var newMerchantStr="";
        for(i=0;i<alldataarr.length;i++){
        	var tempMerchantStr=alldataarr[i];
        	var tempResult=document.getElementById("resultDiv").innerText;
	        var resultArray=tempResult.split("\n")
	        var sameflag=0;
        	for(j=1;j<=clicknum;j++){
        	    var lotteryMerchantName=resultArray[j].split(" ")[1];
        	    if(lotteryMerchantName==(tempMerchantStr.split(" ")[0])){
        	    	//alert('same');
        	    	sameflag=1;
        	    }

        	}
        	if(sameflag==0){
        	  newMerchantStr=newMerchantStr+tempMerchantStr+",";
        	}
        }
        newMerchantStr=newMerchantStr.substring(0,newMerchantStr.length-1);
       // alert(newMerchantStr);
        alldataarr = newMerchantStr.split(",");    
        num = alldataarr.length-1;  
	  }
	  catch(e){alert(e);}
	  
       if(clicknum>=1){
	     	var tempobj=document.getElementById("startbtn");
	      tempobj.innerText="抽奖完毕";
	      document.getElementById("endbtn").style.display="none";
	      
	       document.getElementById("startDiv").style.display="none";
	        document.getElementById("endDiv").style.display="block";
	      
	      var tempResult=document.getElementById("resultDiv").innerText;
	      var resultArray=tempResult.split("\n")
	      var totalmsg="";
	      /*
	      totalmsg=totalmsg+"<font size='4'><b>一等奖:</b></font><br>"
	      totalmsg=totalmsg+resultArray[7].split(" ")[1] +"<br>";
	      totalmsg=totalmsg+"<font size='4'><b>二等奖:</b></font><br>"
	      totalmsg=totalmsg+resultArray[5].split(" ")[1] +"<br>"+resultArray[6].split(" ")[1] +"<br>";
	      totalmsg=totalmsg+"<font size='4'><b>三等奖:</b></font><br>"
	      totalmsg=totalmsg+resultArray[1].split(" ")[1] +"<br>"+resultArray[2].split(" ")[1] +"<br>"+resultArray[3].split(" ")[1] +"<br>"+resultArray[4].split(" ")[1] +"<br>";
	      */
	       totalmsg=totalmsg+"<font size='6'><b>终极大奖:</b></font><br>"
	      totalmsg=totalmsg+"<font size='8'>"+resultArray[1].split(" ")[1] +"</font><br>";
	      
	      document.getElementById("endnameDiv").innerHTML=totalmsg
	      //document.getElementById("endnameDiv").innerHTML=resultArray[1].split(" ")[1] +"<br><font size='2'>("+resultArray[1].split(" ")[6] +")</font><br>"+resultArray[2].split(" ")[1] +"<br><font size='2'>("+resultArray[2].split(" ")[6]+")</font>";
	      document.saveform.msg.value=document.getElementById("resultDiv").innerText;
	      document.saveform.submit();
        //tempobj.disabled="disabled";
	     }
    }
}     
function GetRnd(min,max){     
    return parseInt(Math.random()*(max-min+1));     
}  




 //第五种方法  
        var idTmr;  
        function  getExplorer() {  
            var explorer = window.navigator.userAgent ;  
            //ie  
            if (explorer.indexOf("MSIE") >= 0) {  
                return 'ie';  
            }  
            //firefox  
            else if (explorer.indexOf("Firefox") >= 0) {  
                return 'Firefox';  
            }  
            //Chrome  
            else if(explorer.indexOf("Chrome") >= 0){  
                return 'Chrome';  
            }  
            //Opera  
            else if(explorer.indexOf("Opera") >= 0){  
                return 'Opera';  
            }  
            //Safari  
            else if(explorer.indexOf("Safari") >= 0){  
                return 'Safari';  
            }  
        }  
        function method5(tableid) {  
            if(getExplorer()=='ie')  
            {  
                var curTbl = document.getElementById(tableid);  
                var oXL = new ActiveXObject("Excel.Application");  
                var oWB = oXL.Workbooks.Add();  
                var xlsheet = oWB.Worksheets(1);  
                var sel = document.body.createTextRange();  
                sel.moveToElementText(curTbl);  
                sel.select();  
                sel.execCommand("Copy");  
                xlsheet.Paste();  
                oXL.Visible = true;  
  
                try {  
                    var fname = oXL.Application.GetSaveAsFilename("Excel.xls", "Excel Spreadsheets (*.xls), *.xls");  
                } catch (e) {  
                    print("Nested catch caught " + e);  
                } finally {  
                    oWB.SaveAs(fname);  
                    oWB.Close(savechanges = false);  
                    oXL.Quit();  
                    oXL = null;  
                    idTmr = window.setInterval("Cleanup();", 1);  
                }  
  
            }  
            else  
            {  
                tableToExcel(tableid)  
            }  
        }  
        function Cleanup() {  
            window.clearInterval(idTmr);  
            CollectGarbage();  
        }  
        var tableToExcel = (function() {  
            var uri = 'data:application/vnd.ms-excel;base64,',  
                    template = '<html><head><meta charset="UTF-8"></head><body><table>{table}</table></body></html>',  
                    base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) },  
                    format = function(s, c) {  
                        return s.replace(/{(\w+)}/g,  
                                function(m, p) { return c[p]; }) }  
            return function(table, name) {  
                if (!table.nodeType) table = document.getElementById(table)  
                var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}  
                window.location.href = uri + base64(format(template, ctx))  
            }  
        })()  
  
</script>  
<div class="result_clock" id="clockdiv"></div>
<div class="login_logo"><img  src="iphone7.png" alt="logo" />
</div>
<div class="login_logoright"><img  src="gzbanktower1.png" alt="logo" />
</div>
<div class="login_infonew" >南康赣商村镇银行抽奖(终极大奖)</div>

<div class="result_box1" id="resultDiv" style="display:none"><font color=#FF0000 size="3" ><b>中奖明细：</b></font> </div> 


<div id="endDiv" style="display:none" class="result_boxpic">
	<div  class="result_top"  >  </div>
	 <div  class="result_lottery" id="endnameDiv"></div>
	 <div  class="result_download" ><a href="###" onclick="method5('resultDiv')">下载抽奖结果</a></div>

</div>  

<div id="startDiv" >
<div class="result_box" ><input type="text" id="oknum" name="oknum" value="点击开始进行抽奖" /></div>  
<div class="button_box"><button class="btn" onclick="start()" accesskey="s" id="startbtn" name="startbtn" >开始(<U>S</U>)</button><button class="btn" onclick="ok()" accesskey="o" id="endbtn" name="endbtn">停止(<U>O</U>)</button></div>  
</div>

<div class="remarkdiv" >

<ul>
	<li><b>活动说明：</b></li>
<li>1. 本次抽奖活动连续开展3期（即8月28日-9月6日、9月9日-9月18日、9月21日-9月30日）</li>
<li>2. 活动对象为南康赣商村镇银行商户通签约商户（仅限开立南康赣商村镇银行结算账户的商户）</li>
<li>3. 活动随机抽取期内7笔10元以上（含）交易，交易归属商户获得礼品，每期一等奖1名；二等奖2名；三等奖4名</li>
<li>4. 在三期活动结束后，在以上期间所有大于10元（含）的交易中抽取终极大奖一名</li>
<li>5. 同一商户每期只可抽中一次</li>

</ul>
</div>
</body>  

<form name="saveform" id="saveform" action="abcsave.php" method="post" target="saveframe">  
    <input type=hidden name="msg" id="msg" value=""/>
</form>  
<iframe name="saveframe" id="saveframe" src=""  style="display:none">
</iframe>

<script type="text/javascript">
//在网页上输出：今天的日期、星期、现在的时间（动态时钟）
function timestart()
{
  var today=new Date();
  var year=today.getFullYear();
  var month=today.getMonth()+1;
  var day=today.getDate();
  var hours=today.getHours();
  var minutes=today.getMinutes();
  var seconds=today.getSeconds();
  //如果是单位数字，前面补0
  month=month<10? "0"+month :month;
  day=day<10? "0"+day :day;
  hours=hours<10? "0"+hours :hours;
  minutes=minutes<10? "0"+minutes :minutes;
  seconds=seconds<10? "0"+seconds :seconds;
  //时间信息连成字符串
  var str=year+"年"+month+"月"+day+"日 "+hours+":"+minutes+":"+seconds;
  //获取id=result的内容
  var obj=document.getElementById("clockdiv");
  obj.innerHTML=str;
  //延时器
  window.setTimeout("timestart()",1000);
}
timestart();
</script>  
</html>  
