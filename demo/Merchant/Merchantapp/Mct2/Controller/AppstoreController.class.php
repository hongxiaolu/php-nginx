<?php
namespace Mct2\Controller;
use Think\Controller;
class AppstoreController extends Controller {
	#首页
    public function apphot(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(114); //可以访问此页面的权限	
		$this->assign('menuid',2);	
		$map['best']=5;
		$map['host']=5;
		$map['new']=5;
		$result=$Tlinx->get('appstore/hot',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$this->rows=$result['data'];
		$this->display();
	}
	
	#应用列表
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(114); //可以访问此页面的权限
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=20;
		//调接口取数据
		$result=$Tlinx->get('appstore',$map);
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
		$this->apt_list=$result['data']['apt_list'];
		if(!isset($map['keyword'])){
			$map['keyword']=null;
		}
		if(empty($map['apt_id'])){
			$map['apt_id']="";
		}
		if(empty($map['app_size'])){
			$map['app_size']="";
		}
		$this->map=$map;
		$this->assign('menuid',2);	
		$this->display();
    }
	
	#查看应用
    public function appview(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(114); //可以访问此页面的权限	
		$this->assign('menuid',2);
		$map=I('');
		$result=$Tlinx->get('appstore/view',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$rows=$result['data'];
		$this->assign('rows',$rows);
		$this->display();
	}	
	
	#购买应用
    public function buy(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(115); //可以访问此页面的权限	
		$this->admin=$admin;
		$this->assign('menuid',2);
		$map=I('');
		if(empty($map['shop_no'])){
			$map['shop_no']=null;	
		}
		if(empty($map['buy_num'])){
			$map['buy_num']=1;	
		}	
		
		#判断有无商户Ｔ账户权限，方便输出单选还是多选效果
		$findauth=false;
		foreach($admin['auths'] as $value){
			foreach($value['auth'] as $key2=>$value2)
			if($key2==118){
				$findauth=true;	
			}
		}
		if($findauth){
			$this->inputType='checkbox';
		}else{
			$this->inputType='radio';
		}
		
		$result=$Tlinx->get('appstore/view',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$rows=$result['data'];
		$this->assign('rows',$rows);
		$result=$Tlinx->get('appstore/buy_shop',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$rows2=$result['data'];		
		$this->assign('rows2',$rows2);
		if(empty($map['tryout'])){
			$map['tryout']=0;	
		}
		$this->map=$map;
		$this->display();
	}	
	
	/*购买应用，提交*/
	public function buysave(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(115); //可以访问此页面的权限
		$map=I('');
		$result=$Tlinx->get('appstore/buy',$map);
		if($result['errcode']==0){
			$this->success($result['msg'],'/myapp/setinfo/app_id/'.$map['app_id'].'/shop_no/'.implode(',',$map['shop_no']));
		}else{
			if($result['errcode']==4007 && IS_AJAX){
				$data['info']   =   $result['msg'];
            	$data['status'] =   $result['errcode'];
            	$data['url']    =   '';
				$data['act_balance']    =   $result['data']['act_balance'];
				$data['currency']    	=   $result['data']['currency'];
				$data['currency_sign']  =   $result['data']['currency_sign'];
				$this->ajaxReturn($data);
			}else{
				$this->error($result['msg']);
			}
		}
	}
	
	#购买确认
	public function confirm(){
		$this->display();
	}	
	
}