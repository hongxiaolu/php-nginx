<?php
namespace Mct2\Controller;
use Think\Controller;
class MyappController extends Controller {
	#我的应用
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(116); //可以访问此页面的权限
		$this->admin=$admin;
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=20;
		//调接口取数据
		$map['status']=1;
		$result=$Tlinx->get('merchantapp',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		if($result['data']['pages']['totalnum']>0){
			$Page  = new \Think\Page($result['data']['pages']['totalnum'],$map['pagesize']);
			$Page->setConfig('header',L('_PAGE_HEADER_'));
			$Page->setConfig('theme', '<div class="fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </div><span>%HEADER%</span>');
			foreach($map as $key=>$val) {
				$Page->parameter[$key]   =   urlencode($val);
			}
			// 赋值分页输出
			$this->page=$Page->show();
			$this->list=$result['data']['list'];
		}else{
			$this->page=null;
			$this->list=null;
		}
		$this->app_type=$result['data']['app_type'];
		if(!isset($map['keyword'])){
			$map['keyword']=null;
		}
		if(empty($map['apt_id'])){
			$map['apt_id']="";
		}
		if(empty($map['app_size'])){
			$map['app_size']="";
		}	
		if(empty($map['app_type'])){
			$map['app_type']="";
		}			
		$this->map=$map;
		$this->assign('menuid',3);	
		$this->display();
    }

	#进入应用
    public function entry(){
		C('SHOW_PAGE_TRACE',false);//不显示跟踪调试
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(117); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('');
		$this->map=$map;
		$this->assign('menuid',3);	
		$this->display();
	}
	
	#应用设置
    public function setinfo(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(130); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('');
		$result=$Tlinx->get('merchantapp/setinfo',$map);
		$this->rows=$result['data'];
		$map['shop_no']=explode(',',$map['shop_no']);
		$this->map=$map;
		$this->assign('menuid',3);	
		$this->display();
	}
	
	#应用设置
    public function setinfo_save(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(130); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('');
		$result=$Tlinx->get('merchantapp/setsave',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/myapp');
		}else{
			$this->error($result['msg']);
		}
	}	
	
	#进入应用
    public function main(){
		C('SHOW_PAGE_TRACE',false);//不显示跟踪调试
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(117); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('');
		if(empty($map['app_id'])){
			$this->error('数据有误，缺少app_id');
			exit();	
		}
		#先查询session有没有应用令牌
		$app_token=session('app_token');

		$app_token=null;
		if(empty($app_token[$map['app_id']])){
			$app_token=self::app_token($Tlinx,$admin,$map['app_id']);
		}
		$rows=$app_token[$map['app_id']];
		if(empty($rows['mct_url']) || $rows['mct_url']=="" || strlen($rows['mct_url'])<10){
			$this->error('当前应用暂时无法进入');
			exit();
		}
		$data['mct_no']=$admin['mct_no'];
		$data['shop_no']=$admin['shop_no'];
		if(!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=='on'){
			$data['getway_url']='https://'.$_SERVER['HTTP_HOST'].'/getway';
		}else{
			$data['getway_url']='http://'.$_SERVER['HTTP_HOST'].'/getway';	
		}
		$data['timestamp']=time();
		$data['language']=cookie(C('VAR_LANGUAGE'));
		$data_str=$Tlinx->aes_encode(json_encode($data),$rows['aes_key']);
		$this->app_token=$rows['token'];
		$this->data=$data_str;
		$this->rows=$rows;
		$this->display();
	}	
	
	#交换应用令牌
	public function app_token($Tlinx,$admin,$app_id){
		$app_token=session('app_token');
		#获取应用访问令牌
		$map['app_id']=$app_id;
		$result=$Tlinx->get('merchantapp/entry',$map);
		if($result['errcode']>0){
			$this->error($result['msg']);
			exit();	
		}else{
			$app_token[$app_id]=$result['data'];
		}
		$url=$app_token[$app_id]['token_url'];
		$get['token']=$app_token[$app_id]['token'];
		$get['trade_type']='app_token';
		$data['mct_no']=$admin['mct_no'];
		$data['shop_no']=$admin['shop_no'];
		$data['timestamp']=time();
		$post['data']=$Tlinx->aes_encode(json_encode($data),$app_token[$app_id]['aes_key']);
		
		$result=$Tlinx->http_query($url,$get,$post);
		if($result){
			$arr_result=json_decode($result,true);	
			if(is_array($arr_result)){
				if($arr_result['errcode']>0){
					$this->error($arr_result['msg']);	
					exit();						
				}else{
					$arr_result['data']=json_decode($Tlinx->aes_decode($arr_result['data'],$app_token[$app_id]['aes_key']),true);
					if(!empty($arr_result['data']['app_token'])){
						$app_token[$app_id]['token']=$arr_result['data']['app_token'];
						session('app_token',$app_token);
						return $app_token;
					}else{
						$str='<h1>Exchange app_token error</h1>TOKEN_URL:'.$url;
						$str.='<hr>GET:'.http_build_query($get);
						$str.='<hr>POST:'.http_build_query($post);
						$str.='<hr>RESPONSE:<textarea style="width:100%;height:300px;">'.$result.'</textarea>';	
						$str.='<hr>解密数据失败';						
						if(APP_DEBUG){
							echo $str;
							die();
						}else{
							\Think\Log::write($str,'WARN');
							$this->error('获取令牌失败');	
							exit();	
						}
					}
				}
			}else{
				$str='<h1>Exchange app_token error</h1>TOKEN_URL:'.$url;
				$str.='<hr>GET:'.http_build_query($get);
				$str.='<hr>POST:'.http_build_query($post);
				$str.='<hr>RESPONSE:<textarea style="width:100%;height:300px;">'.$result.'</textarea>';
				if(APP_DEBUG){
					echo $str;
					die();
				}else{
					\Think\Log::write($str,'WARN');
					$this->error('访问应用失败,请稍后重试1');	
					exit();	
				}				
			}
		}else{
			$str='<h1>Exchange app_token error</h1>TOKEN_URL:'.$url;
			$str.='<hr>GET:'.http_build_query($get);
			$str.='<hr>POST:'.http_build_query($post);
			$str.='<hr>RESPONSE:<textarea style="width:100%;height:300px;">'.$result.'</textarea>';
			$str.='<hr>获取数据为空';	
			if(APP_DEBUG){
				echo $str;
				die();
			}else{
				\Think\Log::write($str,'WARN');
				$this->error('访问应用失败,请稍后重试2');	
				exit();	
			}
		}
	}
}
