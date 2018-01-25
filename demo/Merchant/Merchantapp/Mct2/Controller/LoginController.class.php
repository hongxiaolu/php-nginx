<?php
namespace Mct2\Controller;
use Think\Controller;
class LoginController extends Controller {
	
	#登录首页
    public function index(){
		$login_user_name=cookie('login_user_name');
		if(!empty($login_user_name)){
			$this->login_user_name=$login_user_name;
		}else{
			$this->login_user_name=null;
		}
		$this->assign('timerand',randCode(32));
		$this->verify_code=C('mct_verify_code');
		if(file_exists('./Me/login_bg.jpg')){
			$this->login_bg=1;
		}else{
			$this->login_bg=0;
		}
		$this->display();
    }
	
	#校验登录
	public function checklogin(){
		$map=I('');
		//验证码校验
		if(C('mct_verify_code')){
			$verify = new \Think\Verify();
			if(!$verify->check($map['code'])){
				$this->error(L('_LOGIN_CODE_ERR_'),'/');	
				exit();				
			}
		}
		//清空下session，防止重复登录问题
		session(null);
		$get['lang']=strtolower(LANG_SET);
		if(empty($map['user_name'])){
			$this->error(str_replace('INPUT_NAME',L('_LOGIN_SIGN_NAME_'),L('_NOT_NULL_')),'/');	
			exit();
		}
		if(empty($map['password'])){
			$this->error(str_replace('INPUT_NAME',L('_LOGIN_SIGN_PASS_'),L('_NOT_NULL_')),'/');	
			exit();
		}
		#密码容错
		if(strlen($map['password'])!=40){
			$map['password']=sha1($map['password']);
		}
		$map['device_code']=md5(time());
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx(0);
		$result=$Tlinx->login($get,$map);
		if($map['remember_acct']=='on'){
			cookie('login_user_name',$map['user_name'],60*60*24*30);
		}else{
			cookie('login_user_name',null);
		}
		if(!empty($result['data'])){
			session('token',$result['data']['token']);
			session('aes_key',$result['data']['aes_key']);
			session('mct_no',$result['data']['mct_no']);
			#查询本地商户证书是否存在,如果配置文件不存在，就去下载证书
			$public_key_path=C('mct_cert_path').$result['data']['mct_no'].'.pem';
			#不存在下发验证码，获取商户证书
			if(!file_exists($public_key_path)){
				$result=$Tlinx->get('merchant/cert_mobile');
				if($result['errcode']==0){
					cookie('cert_mobile',$result['data'],500);
					$this->success(L('_LOGIN_SUCCESS_'),'/login/cert');
				}else{
					session(null);
					$this->error($result['msg']);	
				}
				exit();
			}else{
				$Tlinx->public_key=file_get_contents($public_key_path);	
				$result=$Tlinx->get('user');
				if($result){
					if($result['data']['org_no']!=C('mct_open_id')){
						session(null);
						$this->error(L('_LOGIN_NOT_CROSS_'),'/');
						exit();
					}
					#检查用户有没有登录商户云平台权限
					$find107=false;
					foreach($result['data']['auths'] as $value){
						foreach($value['auth'] as $key2=>$value2){
							if($key2==107){
								$find107=true;
								 break;
							}
						}
					}
					if($find107){
						session('admin',$result['data']);
						$this->success(L('_LOGIN_SUCCESS_'),'/');
					}else{
						session(null);
						$this->error(L('_LOGIN_NO_ACCESS_'),'/login');	
					}				
				}else{
					session(null);
					$this->success(L('_LOGIN_ERR_SERVER_BUSY_'),'/');	
				}
			}
		}else{
			session(null);
			$this->error($result['msg'],'/login');
		}
	}
	
	#下载证书，发验证码
	public function cert(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(0); //可以访问此页面的权限
		$cert_mobile=cookie('cert_mobile');
		if(empty($cert_mobile)){
			redirect('/');
			exit();	
		}
		$this->cert_mobile=$cert_mobile;
		$this->display();
	}	
	
	#下载证书
	public function checkcert(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(0); //可以访问此页面的权限
		$cert_mobile=cookie('cert_mobile');
		if(empty($cert_mobile)){
			redirect('/');
			exit();	
		}
		$map=I('');
		if(!empty($cert_mobile['sms_id'])){
			$map['sms_id']=$cert_mobile['sms_id'];
		}else{
			$map['eml_id']=$cert_mobile['eml_id'];
		}
		$result=$Tlinx->get('merchant/cert_down',$map);
		if($result['errcode']==0){
			cookie('cert_mobile',null);
			if(!file_exists(C('mct_cert_path'))&&!mkdir(C('mct_cert_path'),0777,true)){
				$this->error('证书目录无权写入文件');
				exit();	
			}
			$filePath=C('mct_cert_path').$result['data']['mct_no'].'.pem';
			$public_key=$result['data']['public_key'];
			#写入文件
			$fh = fopen($filePath, "w"); //w从开头写入 a追加写入
			fwrite($fh, $public_key);
			fclose($fh);
			#获取用户资料
			$Tlinx=new \Tlinx();
			$Tlinx->public_key=$public_key;
			$result=$Tlinx->get('user');
			session('admin',$result['data']);
			$this->success(L('_LOGIN_SUCCESS_'),'/');
		}else{
			$this->error($result['msg']);
		}
		
	}
	
	#退出
	public function logout(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(0); //可以访问此页面的权限
		$result=$Tlinx->get('user/logout');
		session(null); 
		redirect('/login');
	}
	
	#忘记密码
	public function forgetpass(){
		cookie('sms_id',null);
		$this->assign('sms_id',null);
		$this->assign('timerand',randCode(32));
		$map=I('');
		if(empty($map['user_name'])){
			$map['user_name']=null;	
		}
		$this->map=$map;
		$this->display();
	}	
	
	#忘记密码1，发送验证码
	public function checkmobile(){
		//验证码校验
		$map=I('');
		$verify = new \Think\Verify();
    	if(!$verify->check($map['code'])){
			$this->error(L('_LOGIN_CODE_ERR_'));	
			exit();				
		}
		
		if(empty($map['user_name'])){
			$this->error(str_replace('INPUT_NAME',L('_LOGIN_SIGN_NAME_'),L('_NOT_NULL_')),'/login/forgetpass');
			exit();
		}
	
		$get['lang']=strtolower(LANG_SET);
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx(0);
		$post['data']=$Tlinx->rsa_encode(json_encode($map));
		$result=$Tlinx->api('user/forgetpass',$get,$post);
		if(!empty($result['data'])){
			$result['data']=$Tlinx->rsa_decode($result['data']);		
			$result['data']=json_decode($result['data'],true);
		}
		
		if(empty($result['data']['sms_id']) && empty($result['data']['eml_id'])){
			$this->error($result['msg'],'/login/forgetpass');
		}else{
			session('forgetpass2',$result);
			$this->success($result['msg'],'/login/forgetpass2?user_name='.$map['user_name']);
		}

	}	
	
	#忘记密码2，发送验证码
	public function forgetpass2(){
		$map=I('');
		$result=session('forgetpass2');
		if(!empty($result['data']['sms_id'])){
			cookie('sms_id',$result['data']['sms_id'],500);
			$this->assign('sms_id',$result['data']['sms_id']);
			$this->assign('tips',L('_LOGIN_MOBILE_CODE_TIP_',array('mobile'=>$result['data']['mobile'])));	
		}else{
			cookie('eml_id',$result['data']['eml_id'],500);
			$this->assign('eml_id',$result['data']['eml_id']);
			$this->assign('tips',L('_LOGIN_EMAIL_CODE_TIP_',array('email'=>$result['data']['email'])));			
		}
		$this->map=$map;
		$this->display();	
	}	
	
	#忘记密码3，难证及提交
	public function forgetpass3(){
		$map=I('');
		
		if(empty($map['sms_id']) && empty($map['eml_id'])){
			$this->error(L('_LOGIN_RE_SEND_SMS_'));
			exit();
		}
		if(empty($map['sms_code']) && empty($map['eml_code'])){
			$this->error(str_replace('INPUT_NAME',L('_LOGIN_SIGN_CODE_'),L('_NOT_NULL_')));
			exit();
		}
		if(empty($map['password'])){
			$this->error(str_replace('INPUT_NAME',L('_LOGIN_SIGN_PASS_'),L('_NOT_NULL_')));
			exit();
		}
		if($map['password']!=$map['password2']){
			$this->error(L('_LOGIN_TWO_PASS_ERR_'));
			exit();
		}		
		if(strlen($map['password'])!=40){
			$map['password']=sha1($map['password']);
		}
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx(0);
		if(empty($map['eml_id'])){
			$map=array(
				'sms_id'=>$map['sms_id'],
				'sms_code'=>$map['sms_code'],
				'password'=>$map['password'],
			);
		}else{
			$map=array(
				'eml_id'=>$map['eml_id'],
				'eml_code'=>$map['eml_code'],
				'password'=>$map['password'],
			);
		}
		$get['lang']=strtolower(LANG_SET);
		$post['data']=$Tlinx->rsa_encode(json_encode($map));
		$result=$Tlinx->api('user/forgetpass',$get,$post);
		if(!empty($result['data'])){
			$result['data']=$Tlinx->rsa_decode($result['data']);		
			$result['data']=json_decode($result['data'],true);
		}
		if($result['errcode']==0){
			session('forgetpass2',null);
			$this->success($result['msg'],'/login');	
		}else{
			$this->error($result['msg']);
		}

	}	
	
}