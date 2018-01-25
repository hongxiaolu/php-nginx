<?php
// 初始化一个 cURL 对象 
$curl = curl_init(); 
// 设置你需要抓取的URL 
//curl_setopt($curl, CURLOPT_URL, 'http://10.20.188.115:8081/abc.php');
curl_setopt($curl, CURLOPT_URL, 'http://api.s.bankgz.com');
curl_setopt($curl, CURLOPT_PROXY, '10.19.131.118:3128'); 
// 设置header 响应头是否输出
curl_setopt($curl, CURLOPT_HEADER, 1); 
// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
// 1如果成功只将结果返回，不自动输出任何内容。如果失败返回FALSE 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0); 
// 运行cURL，请求网页 
$data = curl_exec($curl); 
// 关闭URL请求 
curl_close($curl); 
// 显示获得的数据 
print_r($data);
//var_dump($data);
?>
