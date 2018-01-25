<?php
namespace Mct2\Controller;
use Think\Controller;
class PosaccountController extends Controller {
	
	#门店T账户
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(104); 
		$this->admin=$admin;
		$map['shop_no']=$admin['shop_no'];
		$result=$Tlinx->get('accountshop',$map);
		if($result['errcode']==5001){
			redirect('/posaccount/register');
			exit();	
		}
		$rows=$result['data'];
		$this->rows=$rows;
		
		$map['sdate']=date('Ymd');
		$map['edate']=date('Ymd');
		$map['status']=1;
		$result=$Tlinx->get('accountshop/report',$map);
		$this->report=$result['data'];
		
		$this->assign('menuid',8);	
		$this->assign('left_menuid',5);	
		$this->display();
    }
	
	#门店T账户
    public function info(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(104); 
		$this->admin=$admin;
		$map['shop_no']=$admin['shop_no'];
		$result=$Tlinx->get('accountshop',$map);
		if($result['errcode']==5001){
			redirect('/posaccount/register');
			exit();	
		}
		$rows=$result['data'];
		$this->rows=$rows;
		$this->assign('menuid',8);	
		$this->assign('left_menuid',5);	
		$this->display();
    }
	
	#门店T账户充值
    public function recharge(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(104); 
		$this->admin=$admin;
		$map['shop_no']=$admin['shop_no'];
		$this->map=$map;
		$this->assign('menuid',8);	
		$this->assign('left_menuid',5);	
		$this->display();
    }	
	
	#门店激活Ｔ账户
    public function register(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(104); 
		$this->admin=$admin;
		$map['shop_no']=$admin['shop_no'];
		$this->map=$map;
		$this->assign('menuid',8);	
		$this->assign('left_menuid',5);	
		$this->display();
    }	
	
	#门店激活Ｔ账户
    public function registersave(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(104); 
		$this->admin=$admin;
		$map=I('');
		$map['shop_no']=$admin['shop_no'];
		$result=$Tlinx->get('accountshop/register',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/posaccount');
		}else{
			$this->error($result['msg']);
		}
    }		
	
	#门店Ｔ账户交易流水
    public function log(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(104); //可以访问此页面的权限
		$this->admin=$admin;
		//取得所有参数
		$map=array();
		$map=I('');
		$map['shop_no']=$admin['shop_no'];
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=10;
		//调接口取数据
		$result=$Tlinx->get('accountshop/tlog',$map);
		if($result['data']['pages']['totalnum']>0){
			$Page  = new \Think\Page($result['data']['pages']['totalnum'],$map['pagesize']);
			$Page->setConfig('prev', '上一页');
			$Page->setConfig('next', '下一页');
			$Page->setConfig('theme', '	<div class="bg_f3 page">%HEADER%<div class="page_right fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</div></div>');
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
		if(empty($map['type'])){
			$map['type']='';
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
		$this->assign('empty','<tr><td colspan="8"><article class="boxRightCon"><div class="zanwujilu"><div class="zanwujilu_icon">暂无相关数据</div></div></article></td></tr>');			
		$this->map=$map;
		$this->assign('menuid',8);
		$this->assign('left_menuid',5);	
		$this->display();
    }
	
	#门店应用购买记录
    public function applog(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(104); //可以访问此页面的权限
		$this->admin=$admin;
		//取得所有参数
		$map=array();
		$map=I('');
		$map['shop_no']=$admin['shop_no'];
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=10;
		//调接口取数据
		$result=$Tlinx->get('accountshop/applog',$map);
		if($result['data']['pages']['totalnum']>0){
			$Page  = new \Think\Page($result['data']['pages']['totalnum'],$map['pagesize']);
			$Page->setConfig('prev', '上一页');
			$Page->setConfig('next', '下一页');
			$Page->setConfig('theme', '	<div class="bg_f3 page">%HEADER%<div class="page_right fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</div></div>');
			foreach($map as $key=>$val) {
				$Page->parameter[$key]   =   urlencode($val);
			}
			// 赋值分页输出
			$this->page=$Page->show();
			$this->list=$result['data']['list'];
			$this->app_count=$result['data']['app_count'];
			$this->app_amount=$result['data']['app_amount'];
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
		$this->assign('empty','<tr><td colspan="10"><article class="boxRightCon"><div class="zanwujilu"><div class="zanwujilu_icon">暂无相关数据</div></div></article></td></tr>');			
		$this->map=$map;
		$this->assign('menuid',8);
		$this->assign('left_menuid',5);
		$this->display();
    }	
	
	#门店提现
    public function cash(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(104); //可以访问此页面的权限
		$result=$Tlinx->get('accountshop');
		$rows=$result['data'];
		$this->rows=$rows;
		$this->assign('menuid',8);	
		$this->assign('left_menuid',5);	
		$this->display();
    }	

	#门店提现
    public function cashsave(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(104); //可以访问此页面的权限
		$map['amount']=I('amount');
		if($map['amount']<0){
			$this->error('提现金额不能小于0');	
		}else{
			$map['amount']=$map['amount']*100;
		}
		$result=$Tlinx->get('accountshop/cash',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/posaccount/cash');
		}else{
			$this->error($result['msg']);
		}
    }	
}