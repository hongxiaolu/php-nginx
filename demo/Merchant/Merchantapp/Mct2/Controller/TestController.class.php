<?php
namespace Mct2\Controller;
use Think\Controller;
class TestController extends Controller {
	
	#关于我
    public function index(){
		Vendor('phpqrcode.phpqrcode');
		$errorCorrectionLevel ='L';//容错级别 
		$matrixPointSize = 5;//生成图片大小 
		$QRcode = new \QRcode();
		$QRcode->png('test', false, $errorCorrectionLevel, $matrixPointSize,1,0); 
		unset($QRcode);
	}
	
	
    public function test(){
    	$public_key=file_get_contents(C('mct_public_key'));
		$data['mobile']='18002526847';
		$data['password']=sha1('18002526847');
		$data['referee']='13926597160';
		$data['app']='test';
		$data['senc_code']='1';
		/*
		$data['sms_id']=1493;
		$data['sms_code']=986349;
		*/
		$url='http://api.wulinyan.com/mct1/register/';
		$post['data']=$this->rsa_encode(json_encode($data),$public_key);
		$result=$this->http_query($url,null,$post);
		$result=json_decode($result,true);
		$result['data']=$this->rsa_decode($result['data'],$public_key);
		dump($result);
	}
	
	public function http_query($url,$get=null,$post=null){
		if(isset($get)){
			if(substr_count($url,'?')>0){
				$url.="&".http_build_query($get);
			}else{
				$url.="?".http_build_query($get);
			}
		}
		if(C("SHOW_PAGE_TRACE")){trace($url,"GET数据:");}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		if(isset($post)){
			curl_setopt($curl, CURLOPT_POST, 1); //是否开启post
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post); //post数据
			if(C("SHOW_PAGE_TRACE")){trace(http_build_query($post),"POST数据:");}
		}
		//忽略证书
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);	
		curl_setopt($curl, CURLOPT_HEADER,0);//是否需要头部信息（否）
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//如果成功只将结果返回，不自动输出任何内容。
		curl_setopt($curl, CURLOPT_TIMEOUT,30);//设置允许执行的最长秒数。
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,5);//在发起连接前等待的时间，如果设置为0，则无限等待。
		$curl_result = curl_exec($curl);
		echo $curl_result.'<hr>';
		if(C("SHOW_PAGE_TRACE")){trace($curl_result,"服务器响应数据:");}
		if($curl_result){
			curl_close($curl);
			return $curl_result;
		}else{
			$err_str=curl_error($curl);
			curl_close($curl);	
			return $err_str;
		}
	}
	
	#rsa加密
	public function rsa_encode($str,$public_key=null){
		$pu_key = openssl_pkey_get_public($public_key);
		openssl_public_encrypt($str,$str,$pu_key);//公钥加密
		$str = bin2hex($str);
		openssl_free_key($pu_key);
		return $str;
	}
	
	#rsa解密
	public function rsa_decode($str,$public_key=null){
		$pu_key = openssl_pkey_get_public($public_key);
		openssl_public_decrypt(hex2bin($str),$str,$pu_key);
		openssl_free_key($pu_key);
		return $str;
	}
	
	
}