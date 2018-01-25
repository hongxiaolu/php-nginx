<?php
#文件容量转换
function formatBytes($size) { 
	$units = array(' B', ' KB', ' MB', ' GB', ' TB'); 
	for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024; 
	return round($size, 2).$units[$i]; 
}

/**
	+----------------------------------------------------------
	* 生成随机字符串
	+----------------------------------------------------------
	* @param int       $length  要生成的随机字符串长度
	* @param string    $type    随机码类型：0，数字+大小写字母；1，数字；2，小写字母；3，大写字母；4，特殊字符；-1，数字+大小写字母+特殊字符
	+----------------------------------------------------------
	* @return string
	+----------------------------------------------------------
	echo randCode(6,1);
*/
function randCode($length = 32, $type = 0) {
	$arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
	if ($type == 0) {
		array_pop($arr);
		$string = implode("", $arr);
	} elseif ($type == "-1") {
		$string = implode("", $arr);
	} else {
		$string = $arr[$type];
	}
	$count = strlen($string) - 1;
	$code = '';
	for ($i = 0; $i < $length; $i++) {
		$code .= $string[rand(0, $count)];
	}
	return $code;
}

#传入时间戳转换
function wordTime($datetime) {
	if(empty($datetime)){
		return '';
		exit();	
	}
	$time=strtotime($datetime);
	
	$int = time() - $time;
	$str = '';
	if ($int <= 2){
		$str = L('_TIME_LIMIT_1_');#刚刚
	}elseif ($int < 60){
		$str = sprintf(L('_TIME_LIMIT_2_'), $int);#%d秒前
	}elseif ($int < 3600){
		$str = sprintf(L('_TIME_LIMIT_3_'), floor($int / 60));#%d分钟前
	}elseif ($int < 86400){
		$str = sprintf(L('_TIME_LIMIT_4_'), floor($int / 3600));#%d小时前
	}else{
		$str = sprintf(L('_TIME_LIMIT_5_'), floor($int / 86400));#%d天前
	}
	return $str;
}

