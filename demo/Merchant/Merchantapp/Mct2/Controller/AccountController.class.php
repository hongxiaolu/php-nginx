<?php
namespace Mct2\Controller;
use Think\Controller;
class AccountController extends Controller {
	#商户T账户
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('');
		#如果门店号为空，判断有无商户Ｔ账户权限
		if(empty($map['shop_no'])){
			$findauth=false;
			foreach($admin['auths'] as $value){
				foreach($value['auth'] as $key2=>$value2)
				if($key2==118){
					$findauth=true;	
				}
			}
			if(!$findauth){
				redirect('/account/shop');	
				exit();
			}
			$this->hidelist=0;
		}
		$result=$Tlinx->get('account',$map);
		if($result['errcode']==5001){
			$map['accept']=1;
			$result=$Tlinx->get('account/register',$map);
			if($result['errcode']==0){
				$result=$Tlinx->get('account');
			}else{
				$this->error($result['msg']);
				exit();
			}
		}
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$this->rows=$result['data'];
		#T账户数据报表
		$map['sdate']='today';
		$map['edate']='today';
		$result=$Tlinx->get('account/report',$map);
		$report=$result['data'];
		$this->report=$report;
		if(empty($map['shop_no'])){
			$map['shop_no']=null;
		}
		$this->assign('map',$map);	
		$this->assign('menuid',5);	
		$this->assign('left_menuid',1);	
		$this->display();
    }
	
	#门店T账户
    public function shop(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(131); //可以访问此页面的权限
		$this->admin=$admin;

		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=10;
		//调接口取数据
		$result=$Tlinx->get('account/shop',$map);
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
			$this->assign('list',$result['data']['list']);
		}else{
			$this->page=null;
			$this->list=null;
		}

		if(empty($map['sdate'])){
			$map['sdate']='';
		}	
		if(empty($map['edate'])){
			$map['edate']='';
		}	
		if(empty($map['status'])){
			$map['status']='';
		}	
		if(empty($map['type'])){
			$map['type']='';
		}		
		if(empty($map['shop_no'])){
			$map['shop_no']='';
		}			
		$this->map=$map;
		$this->assign('left_menuid',4);	
		$this->assign('menuid',5);
		$this->display();
    }
	
	#激活
    public function register(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		$map=I('');
		$map['accept']=1;
		$result=$Tlinx->get('account/register',$map);
		if($result['errcode']==0){
			$this->success($result['msg']);
		}else{
			$this->error($result['msg']);
		}
		
    }	
	
	#T账户详情
    public function info(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		$result=$Tlinx->get('account');
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$rows=$result['data'];
		if(empty($map['shop_no'])){
			$map['shop_no']='';
		}	
		$this->rows=$rows;
		$this->assign('menuid',5);	
		$this->assign('left_menuid',1);	
		$this->display();
    }
	
	#修改提现密码
    public function password(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		$map=I('');
		if($map['password']!=$map['password2']){
			$this->error(L('_ACCOUNT_PASS_ERR_'));
			exit();	
		}
		if(strlen($map['password'])!=40){
			$map['password']=sha1($map['password']);
		}
		if(!empty($map['old_password']) && strlen($map['password'])!=40){
			$map['old_password']=sha1($map['old_password']);
		}
		$result=$Tlinx->get('account/password',$map);
		if($result['errcode']==0){
			$this->success(L('_ACCOUNT_PASS_SET_OK_'),'/account/info');
		}else{
			$this->error($result['msg']);
		}
    }
	
	#T账户充值
    public function recharge(){
		C('TOKEN_ON',true); #开启表单令牌
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		$map=I('');
		#查询门店Ｔ账户数据
		if(empty($map['shop_no'])){
			$map['shop_no']='';
		}else{
			if(empty($map['box'])){
				$result=$Tlinx->get('account',$map);
				if($result['errcode']!=0){
					$this->error($result['msg']);
					die();
				}
				$this->account=$result['data'];
			}
		}
		#获取支付方式
		$result=$Tlinx->get('recharge/paylist');
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$this->paylist=$result['data'];				
		if(!empty($map['amount'])){
			$map['amount2']=preg_replace('/[^\d]/','',$map['amount']);;
		}
		$this->assign('map',$map);	
		$this->assign('menuid',5);	
		$this->assign('left_menuid',1);	
		if($map['box']=="mini"){
			$this->display('recharge_mini');
		}else{
			$this->display();
		}
    }		
	
	#T账户充值（扫码）
    public function recharge_to(){
		$Model=M();
		C('TOKEN_ON',true); #开启表单令牌
		if (!$Model->autoCheckToken($_POST)){
			//$this->error('不能重复提交请求！',$backurl);
			//exit();
		}
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		$map=I('');
		#查询门店Ｔ账户数据
		if(empty($map['shop_no'])){
			$map['shop_no']='';
		}else{
			if(empty($map['box'])){
				$result=$Tlinx->get('account',$map);
				if($result['errcode']!=0){
					$this->error($result['msg']);
					die();
				}
				$this->account=$result['data'];	
			}
		}
		#获取二维码
		$map['amount']=$map['amount']*100;
		$result=$Tlinx->get('recharge/getqrcode',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$this->rows=$result['data'];
	
		$this->assign('map',$map);	
		$this->assign('menuid',5);	
		$this->assign('left_menuid',1);	
		if($map['box']=="mini"){
			$this->display('recharge_to_mini');
		}else{
			$this->display();
		}
    }			
	
	#T账户充值（成功）
    public function recharge_ok(){
		$Model=M();
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		$map=I('');
		
		$this->assign('map',$map);	
		$this->assign('menuid',5);	
		$this->assign('left_menuid',1);	
		$this->display();
    }			
	
	#T账户充值(状态查询)
    public function recharge_status(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('recharge/paystatus',$map);
		$this->ajaxReturn($result);
    }		

	#T账户充值（取消）
    public function recharge_cancel(){
		$Model=M();
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('recharge/cancel',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/account/');
		}else{
			$this->error($result['msg']);
		}
    }		

	#Ｔ账户交易流水
    public function tradelog(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=10;
		//调接口取数据
		$result=$Tlinx->get('account/tradelog',$map);
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
			$this->assign('list',$result['data']['list']);
		}else{
			$this->page=null;
			$this->list=null;
		}
		$this->rows=$result['data'];
		$this->assign('count',$result['data']['count']);

		if(empty($map['sdate'])){
			$map['sdate']='';
		}	
		if(empty($map['edate'])){
			$map['edate']='';
		}	
		if(empty($map['status'])){
			$map['status']='';
		}	
		if(empty($map['type'])){
			$map['type']='';
		}		
		if(empty($map['shop_no'])){
			$map['shop_no']='';
		}					
		$this->map=$map;
		$this->assign('left_menuid',2);	
		$this->assign('menuid',5);
		$this->display();
    }
	
	#应用购买记录
    public function applog(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=10;
		//调接口取数据
		$result=$Tlinx->get('account/applog',$map);
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
			$this->rows=$result['data'];
		}else{
			$this->page=null;
			$this->list=null;
			$this->app_count=0;
			$this->app_amount=0;
		}
		

		if(empty($map['sdate'])){
			$map['sdate']='';
		}	
		if(empty($map['edate'])){
			$map['edate']='';
		}	
		if(empty($map['status'])){
			$map['status']='';
		}
		if(empty($map['shop_no'])){
			$map['shop_no']='';
		}	
		if(empty($map['app_name'])){
			$map['app_name']='';
		}			
		if(empty($map['shop_name'])){
			$map['shop_name']='';
		}			
		$this->map=$map;
		$this->assign('menuid',5);
		$this->assign('left_menuid',3);
		$this->display();
    }	
	
	#提现
    public function cash(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(131); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('account',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$rows=$result['data'];
		$this->rows=$rows;
		if(empty($map['shop_no'])){
			$map['shop_no']='';
		}
		$this->assign('map',$map);
		$this->assign('menuid',5);	
		$this->assign('left_menuid',0);	
		$this->display();
    }	

	#提现
    public function cashsave(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		$map=I('');
		if($map['amount']<0){
			$this->error('提现金额不能小于0');	
		}else{
			$map['amount']=$map['amount']*100;
		}
		$result=$Tlinx->get('account/cash',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/account/cash?shop_no='.$map['shop_no']);
		}else{
			$this->error($result['msg']);
		}
    }	
	
	#转账
    public function transfer(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		$this->admin=$admin;
		$map=I('');
		if(empty($map['type'])){
			$map['type']=1;
		}
		#查询商户Ｔ账户数据
		if($map['type']==1){
			$result=$Tlinx->get('account');
			if($result['errcode']!=0){
				$this->error($result['msg']);
				die();
			}
			$this->account=$result['data'];		
			#获取门店列表
			$maps['pagesize']=100;
			$maps['mct_no']=$admin['mct_no'];
			$result=$Tlinx->get('account/shop',$maps);
			if($result['errcode']!=0){
				$this->error($result['msg']);
				die();
			}
			$this->shoplist=$result['data']['list'];
		
		}else{
			if(empty($map['shop_no'])){
				$this->error(L('_ACCOUNT_SHOP_NULL_'));
				die();
			}
			#查询门店Ｔ账户数据
			$result=$Tlinx->get('account',$map);
			if($result['errcode']!=0){
				$this->error($result['msg']);
				die();
			}
			$this->account=$result['data'];						
		}
		if(empty($map['shop_no'])){
			$map['shop_no']='';
		}
		$this->assign('map',$map);	
		$this->assign('menuid',5);	
		$this->assign('left_menuid',1);	
		$this->display();
    }
	
	#转账提交
    public function transfer_to(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(107); //可以访问此页面的权限
		$map=I('');
		$map['amount']=$map['amount']*100;
		$result=$Tlinx->get('account/transfer',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/account');
		}else{
			$this->error($result['msg']);
		}
    }
}