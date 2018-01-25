<?php
namespace Mct2\Controller;
use Think\Controller;
class SysController extends Controller {
	#关于我
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(108); //可以访问此页面的权限
		$result=$Tlinx->get('user');
		$admin=$result['data'];
		session('admin',$admin);
		shuffle($admin['roles']);
		$this->admin=$admin;
		$this->assign('menuid',4);	
		$this->assign('left_menuid',0);
		$this->display();
	}
	#修改密码
    public function password(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(108); //可以访问此页面的权限
		$this->assign('menuid',4);	
		$this->assign('left_menuid',1);
		$this->display();
	}	
	/*修改个人密码*/
	public function password_save(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(108); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('admin/password',$map);
		if($result['errcode']==0){
			$this->success($result['msg']);
		}else{
			$this->error($result['msg']);
		}
	}
	#更改手机号码
    public function mobile(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(113); //可以访问此页面的权限
		$this->assign('menuid',4);	
		$this->assign('left_menuid',2);
		$this->display();
	}	
	/*更改手机号码，获取验证码*/
	public function mobile_code(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(113); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('admin/mobile_code',$map);
		if($result['errcode']==0){
			$this->assign('menuid',4);	
			$this->assign('left_menuid',2);
			$this->assign('mobile',$map['mobile']);
			$this->assign('sms_id',$result['data']['sms_id']);
			$this->display();
		}else{
			$this->error($result['msg'],'/sys/mobile');
		}
	}
	/*更改手机号码，提交*/
	public function mobile_save(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(113); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('admin/mobile_save',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/sys/mobile');
		}else{
			$this->error($result['msg']);
		}
	}	
	#管理员列表
    public function admin(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(109); //可以访问此页面的权限
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=10;
		//调接口取数据
		$result=$Tlinx->get('admin',$map);
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
		if(!isset($map['status'])){
			$map['status']=null;
		}	
		if(!isset($map['user_name'])){
			$map['user_name']=null;
		}			
		if(!isset($map['true_name'])){
			$map['true_name']=null;
		}
		if(!isset($map['shop_no'])){
			$map['shop_no']=null;
		}		
		$this->assign('empty','<div class="no_data"><img src="/Public/mct2/images/2.0/no_data.png"><p>'.L('_NO_DATA_').'</p></div>');	
		$this->map=$map;
		$this->assign('menuid',4);	
		$this->assign('left_menuid',3);
		$this->display();
    }
	/*添加管理员*/
    public function admin_add(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(109); //可以访问此页面的权限
		$result=$Tlinx->get('admin/admin_add_fields');
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$this->data=$result['data'];
		$this->assign('menuid',4);	
		$this->assign('left_menuid',3);
		$this->display();
    }
	/*添加（保存）*/
	public function admin_addsave(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(109); //可以访问此页面的权限	
		$map=I('post.');//取得所有post参数
		$map['status']=1;
		if(strlen($map['password'])!=40){
			$map['password']=sha1($map['password']);
		}
		$result=$Tlinx->get('admin/addsave',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/sys/admin');
		}else{
			$this->error($result['msg']);
		}
	}
	/*修改管理员*/
    public function admin_edit(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(109); //可以访问此页面的权限
		$map=I('');//取得所有post参数
		$result=$Tlinx->get('admin/admin_add_fields');
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$this->data=$result['data'];
		$result=$Tlinx->get('user',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$this->mydata=$result['data'];		
		$this->assign('menuid',4);	
		$this->assign('left_menuid',3);
		$this->display();
    }
	/*修改（保存）*/
	public function admin_editsave(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(109); //可以访问此页面的权限	
		$map=I('post.');//取得所有post参数
		if(!empty($map['password']) && strlen($map['password'])!=40){
			$map['password']=sha1($map['password']);
		}
		$result=$Tlinx->get('admin/editsave',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/sys/admin');
		}else{
			$this->error($result['msg']);
		}
	}
	/*删除管理员*/
	public function admin_delete(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(109); //可以访问此页面的权限	
		$map=I('');//取得所有post参数
		$result=$Tlinx->get('admin/delete',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/sys/admin');
		}else{
			$this->error($result['msg']);
		}
	}	

}