<?php
// +----------------------------------------------------------------------
// | TTG Tlinx sdk
// +----------------------------------------------------------------------
class Tlinx{
	
	#服务器地址
	public $open_url		=	null;
	
	#应用名称
	public $app_name		=	null;
	
	#公钥
	public $public_key		=	null;
	
	#权限编号
	public $auth_id		=	0;
	
	#构造方法
    public function __construct($cklogin=1){
		$this->open_url		=	C('mct_open_url');
		$this->app_name		=	C('mct_app_name');
		if(!empty(session('mct_no')) && !empty(session('admin'))){
			$public_key_path=C('mct_cert_path').session('mct_no').'.pem';
			if(!file_exists($public_key_path)){
				$this->public_key	=	file_get_contents(C('mct_public_key'));
			}else{
				$pubkey=file_get_contents($public_key_path);
				$this->public_key	=	$pubkey;
			}
		}else{
			$this->public_key	=	file_get_contents(C('mct_public_key'));	
		}
		if($cklogin){
			$this->checklogin();
		}
    }
	
	#获取数据(接口，请求数据，缓存时间，缓存键值)
	public function get($api,$data=null,$cache=0,$cache_key=null){
		$get['token']=session('token');//token令牌
		$get['lang']=strtolower(LANG_SET);#多语言支持
		#缓存处理
		if($cache){
			if(C("SHOW_PAGE_TRACE")){trace(json_encode($cache_key),"缓存键值");}
			$cache_key=md5($cache_key);
			$cache_value=S($cache_key);
			if(!empty($cache_value)){
				if(C("SHOW_PAGE_TRACE")){trace(json_encode($cache_value),"缓存中读取数据");}
				return $cache_value;
				exit();	
			}
			
		}
		if(is_array($data)){
			if(C("SHOW_PAGE_TRACE")){trace(json_encode($data),"POST数据");}
			//对提交数据进行公钥加密
			$post['data']=$this->aes_encode(json_encode($data),session('aes_key'));
		}else{
			$post=null;
		}
		#参数签名
		if($this->auth_id>0){
			if(empty($post)){
				if(C("SHOW_PAGE_TRACE")){trace(json_encode($get),"请求签名数据");}
				if(C("SHOW_PAGE_TRACE")){trace(md5(json_encode($get)),"请求签名数据md5");}
				$post['sign']=$this->rsa_encode(md5(json_encode($get)),$this->public_key);
			}else{
				if(C("SHOW_PAGE_TRACE")){trace(json_encode($get).json_encode($post),"请求签名数据");}
				if(C("SHOW_PAGE_TRACE")){trace(md5(json_encode($get).json_encode($post)),"请求签名md5 ");}
				$post['sign']=$this->rsa_encode(md5(json_encode($get).json_encode($post)),$this->public_key);	
			}
			if(C("SHOW_PAGE_TRACE")){trace($post['sign'],"签名RSA加密");}
		}
		$result=$this->api($api,$get,$post);
		if(!empty($result['data'])){
			//验签
			$sign=$result['sign'];
			if(C("SHOW_PAGE_TRACE")){trace($sign,"服务器签名");}
			$decode_sign=$this->rsa_decode($sign,$this->public_key);//rsa解密字符
			#if(C("SHOW_PAGE_TRACE")){trace($this->public_key,"解密公钥");}
			if(C("SHOW_PAGE_TRACE")){trace($decode_sign,"服务器签名RSA解密");}
			unset($result['sign']);
			$md5_str=json_encode($result);
			if(C("SHOW_PAGE_TRACE")){trace($md5_str,"验签字符串");}
			$md5_str=md5($md5_str);#签名字符
			if(C("SHOW_PAGE_TRACE")){trace($md5_str,"验签字符串md5");}
			if($decode_sign!=$md5_str){
				$str='<h1>SIGN ERROR</h1>';
				$str.='<hr>GET_RESULT:'.json_encode($result);
				$str.='<hr>GET_SIGN:'.$sign;
				$str.='<hr>DECODE_SIGN:'.$decode_sign;
				$str.='<hr>MD5_RESULT:'.$md5_str;
				if(APP_DEBUG){
					echo $str;
					die();
				}else{
					\Think\Log::record($str,'ERR');
					$arr_result['errcode']=1;
					$arr_result['msg']='服务器繁忙，请稍候重试';
				}	
			}
			
			//对返回数据进行公钥解密
			$result['data']=$this->aes_decode($result['data'],session('aes_key'));
			if(C("SHOW_PAGE_TRACE")){trace($result['data'],"AES解密数据得到的原始json数据");}
			//把json处理成数组
			$result['data']=json_decode($result['data'],true);
			if(C("SHOW_PAGE_TRACE")){trace(str_replace('\/','/',json_encode($result['data'],JSON_UNESCAPED_UNICODE)),"JSON转数组");}
		}
		#缓存处理
		if($cache){
			$cache_value=S($cache_key,$result,$cache);
			if(C("SHOW_PAGE_TRACE")){trace(json_encode($result),"缓存数据");}
		}
		return $result;
	}
	
	#检查登录状态
	public function checklogin(){
		if(empty(session('token'))){
			redirect('/login?backurl='.$_SERVER['REQUEST_URI']);	
			exit();
		}
	}
	
	#检查权限
	public function check_auth($auth_id=0){
		$admin=session('admin');
		if($auth_id>0){
			$is_true=0;
			foreach($admin['auths'] as $value){
				foreach($value['auth'] as $key2=>$value2){
					if($auth_id==$key2){
						$is_true=1;
						break;	
					}
				}
			}
			
			if(!$is_true){
				redirect('/tips/auth');
				exit();
			}
		}
		$this->auth_id=$auth_id;
		return $admin;
		
		
	}	
	
	#登录
	public function login($array_get,$array_post){
		if(!empty($array_get['mct_no'])){
			$get['mct_no']		=$array_get['mct_no'];
		}
		$get['lang']		=$array_get['lang'];
		$data['user_name']	=$array_post['user_name'];
		$data['password']	=$array_post['password'];
		$data['app']		=substr($this->app_name,0,10);
		$data['org_no']		=C('mct_open_id');
		$post['data']		=$this->rsa_encode(json_encode($data),$this->public_key);
		$result=$this->api('user/login',$get,$post);
		if(!empty($result['data'])){
			$result['data']=$this->rsa_decode($result['data'],$this->public_key);		
			$result['data']=json_decode($result['data'],true);
		}
		return $result;
	}

	#rsa加密
	public function rsa_encode($str,$public_key=null){
		if(empty($public_key)){$public_key=$this->public_key;}
		$pu_key = openssl_pkey_get_public($public_key);
		openssl_public_encrypt($str,$str,$pu_key);//公钥加密
		$str = bin2hex($str);
		openssl_free_key($pu_key);
		return $str;
	}
	
	#rsa解密
	public function rsa_decode($str,$public_key=null){
		if(empty($public_key)){$public_key=$this->public_key;}
		$pu_key = openssl_pkey_get_public($public_key);
		openssl_public_decrypt(hex2bin($str),$str,$pu_key);
		openssl_free_key($pu_key);
		return $str;
	}
	
	#AES解密
	public function aes_decode($str,$aes_key){
		$objAes=new \Think\Crypt\Driver\Aes();
		$str=$objAes->decrypt($str,$aes_key);
		return $str;
		
	}
	
	#AES加密
	public function aes_encode($str,$aes_key){
		$objAes=new \Think\Crypt\Driver\Aes();
		$str=$objAes->encrypt($str,$aes_key);
		return $str;
	}
	
	#类内部API接口
	public function api($api,$get=null,$post=null){
		$url =$this->open_url.$api;
		$result=$this->http_query($url,$get,$post);
		if($result){
			//将返回json转换为数组
			$arr_result=json_decode($result,true);
			if(!is_array($arr_result) || $arr_result['errcode']==5){
				$str='<h1>ERROR</h1>';
				$str.='<hr>GET:'.$url;
				if(is_array($post)){
					$str.='<hr>POST:'.http_build_query($post);
				}
				$str.='<hr>RETURN:'.$result;
				\Think\Log::record($str,'ERR');
				if(APP_DEBUG){
					die($str);
				}else{
					$arr_result['errcode']=1;
					$arr_result['msg']='服务器繁忙，请稍候重试';
				}
			}
			#令牌已经过期
			if($arr_result['errcode']==6){
				session(null);
				redirect('/');
				exit();
			}
			#无权访问接口
			if($arr_result['errcode']==10 || $arr_result['errcode']==11){
				redirect('/tips/auth');
				exit();
			}
			return $arr_result;
		}else{
			$str=  '<h1>ERROR</h1>';
			$str.= '<hr>GET:'.$url;	
			$str.= '<hr>POST:'.http_build_query($post_data);
			$str.= '<hr>RETURN:'.$result;
			echo $str;
			\Think\Log::record($str,'ERR');
			die();
			return false;	
		}
	}

	#http数据交互接口
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
		if(APP_DEBUG){\Think\Log::record($curl_result,'WARN',true);}
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
	
}

