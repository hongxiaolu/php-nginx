<?php
namespace Mct2\Controller;
use Think\Controller;
class PosappstoreController extends Controller {
	#收银台应用市场
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(100); //可以访问此页面的权限
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=15;
		//调接口取数据
		$result=$Tlinx->get('shopappstore',$map);
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
		if(!isset($map['keyword'])){
			$map['keyword']=null;
		}
		if(empty($map['apt_id'])){
			$map['apt_id']="";
		}
		if(empty($map['app_size'])){
			$map['app_size']="";
		}
		if(empty($map['type'])){
			$map['type']="";
		}				
		$this->app_type=$result['data']['app_type'];
		$this->map=$map;
		$this->assign('menuid',8);
		$this->assign('left_menuid',4);	
		$this->display();
    }
	
	#查看应用
    public function appview(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(100); //可以访问此页面的权限	
		$this->assign('menuid',8);
		$map=I('');
		$result=$Tlinx->get('shopappstore/detail',$map);
		$rows=$result['data'];
		$this->assign('rows',$rows);
		$this->display();
	}	
	
	#购买应用
    public function buy(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(101); //可以访问此页面的权限	
		$this->assign('menuid',8);
		$map=I('');
		$result=$Tlinx->get('shopappstore/detail',$map);
		$rows=$result['data'];
		$this->assign('rows',$rows);
		if(empty($map['tryout'])){
			$map['tryout']=0;	
		}
		$vo['shop_no']=$this->admin['shop_no'];
		$vo['shop_name']=$this->admin['shop_name'];
		$vo['expired_date']=null;
		$this->vo=$vo;
		
		$result=$Tlinx->get('shopappstore/roles',$map);
		$this->role=$result['data'];		
		$this->role_diy=false;	
		foreach($result['data'] as $value){
			if($value['role_mct_no']>0){
				$this->role_diy=true;	
			}
		}
		
		$this->map=$map;
		$this->display();
	}	
	
	/*购买提交*/
	public function buysave(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(101); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('shopappstore/buy',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/posapp');
		}else{
			$this->error($result['msg']);
		}
	}
	
}