<?php
namespace Mct2\Controller;
use Think\Controller;
class AppsetController extends Controller {
	#我的应用
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(130); //可以访问此页面的权限
		$this->admin=$admin;
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=10;
		//调接口取数据
		$result=$Tlinx->get('merchantapp/shop',$map);
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
		if(!isset($map['keyword'])){
			$map['keyword']=null;
		}		
		$this->map=$map;
		$this->assign('menuid',4);	
		$this->assign('left_menuid',8);	
		$this->display();
    }
	
    public function setauth(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(130); //可以访问此页面的权限
		$this->admin=$admin;
		//取得所有参数
		$map=array();
		$map=I('');
		$result=$Tlinx->get('merchantapp/auth',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$this->rows=$result['data'];
		$this->map=$map;
		$this->assign('menuid',4);	
		$this->assign('left_menuid',8);	
		$this->display();
    }
	
    public function setauth_save(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(130); //可以访问此页面的权限
		$this->admin=$admin;
		//取得所有参数
		$map=array();
		$map=I('');
		$result=$Tlinx->get('merchantapp/authsave',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
		}else{
			$this->success($result['msg'],'/appset');
		}
    }	
	
}