<?php
namespace Mct2\Controller;
use Think\Controller;
class RoleController extends Controller {
	#角色管理
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(127); //可以访问此页面的权限
		$map=I('');
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=10;
		//调接口取数据
		$result=$Tlinx->get('admin/role',$map);
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
		$this->assign('map',$map);
		$this->assign('menuid',4);	
		$this->assign('left_menuid',7);
		$this->display();
	}
	
	/*查看角色权限*/
	public function auth(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(127); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('admin/role_auth',$map);
		$this->assign('rows',$result['data']);
		$this->assign('menuid',4);	
		$this->assign('left_menuid',7);
		$this->display();
	}
	
	/*修改角色*/
	public function edit(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(127); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('admin/role_auth',$map);
		$this->assign('rows',$result['data']);
		$this->assign('map',$map);
		$this->assign('menuid',4);	
		$this->assign('left_menuid',7);
		$this->display();
	}
		
	/*修改角色保存*/
	public function edit_save(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(127); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('admin/role_edit',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/role');	
		}else{
			$this->error($result['msg'],'/Role/edit/role_id/'.$map['role_id']);
		}
	}
	
	#添加角色
	public function add(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(127); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('admin/role_auth',$map);
		$this->assign('rows',$result['data']);
		$this->assign('map',$map);
		$this->assign('menuid',4);	
		$this->assign('left_menuid',7);
		$this->display();
	}	
	
	/*添加角色保存*/
	public function add_save(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(127); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('admin/role_add',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/role');	
		}else{
			$this->error($result['msg'],'/role/add');
		}
	}	
	
	/*删除角色保存*/
	public function delete(){
		$Model=M();
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(127); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('admin/role_delete',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/role');	
		}else{
			$this->error($result['msg'],'/role');
		}
	}		
}