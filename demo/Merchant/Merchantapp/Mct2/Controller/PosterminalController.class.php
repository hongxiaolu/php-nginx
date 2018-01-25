<?php
namespace Mct2\Controller;
use Think\Controller;
class PosterminalController extends Controller {
	#首页
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(97); 
		$this->device_code=C('mct_device_code');
		$this->tml_no=C('mct_tml_no');
		$this->tml_name=session('tml_name');
		$this->tml_token=session('tml_token');
		$this->cdkey=session('cdkey');
		$this->assign('menuid',8);	
		$this->assign('left_menuid',6);	
		$this->display();
    }
	
	//上线
    public function online(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(97); 
		$map['device_code']=session('device_code');
		$map['tml_no']=session('tml_no');
		$result=$Tlinx->get('terminal/online',$map);
		if($result['errcode']==0){
			session('tml_token',$result['data']['tml_token']);
			session('tml_name',$result['data']['tml_name']);
			$this->success('机器上线成功','/posterminal');	
		}else{
			$this->error($result['msg']);	
		}
    }
	
	//机器下线
    public function offline(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(97); 
		$map['tml_token']=session('tml_token');
		$result=$Tlinx->get('terminal/offline',$map);
		if($result['errcode']==0){
			session('tml_token',null);
			session('tml_name',null);
			session('cdkey',null);
			$this->success('机器已经下线','/posterminal');	
		}else{
			$this->error($result['msg']);	
		}
    }
	
	//机器心跳
    public function heartbeat(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx(0);
		$map['tml_token']=session('tml_token');
		$result=$Tlinx->api('terminal/heartbeat',$map);
		if($result['errcode']==0){
			if($result['count']>0){
				$this->success('心跳成功，当前有'.$result['count'].'条消息','/message');
			}else{
				$this->success('心跳成功，当前有'.$result['count'].'条消息','/posterminal');	
			}
		}else{
			$this->error($result['msg']);
		}
    }
	
	//用户心跳
    public function user_heartbeat(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx(0);
		$map['token']=session('token');
		$result=$Tlinx->api('user/heartbeat',$map);
		if($result['errcode']==0){
			if($result['count']>0){
				$this->success('心跳成功，当前有'.$result['count'].'条消息','/message');
			}else{
				$this->success('心跳成功，当前有'.$result['count'].'条消息','/posterminal');	
			}
		}else{
			$this->error($result['msg']);
		}
    }	
	
	//修改机器名称
    public function edit_name(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(97); 
		$map=I('');
		$map['tml_token']=session('tml_token');
		$result=$Tlinx->get('terminal/edit_name',$map);
		if($result['errcode']==0){
			session('tml_name',$result['data']['tml_name']);
			$this->success('修改机器名称成功','/posterminal');	
		}else{
			$this->error($result['msg']);	
		}
    }
	
	//激活码
    public function cdkey(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(97); 
		$map=I('');
		$map['tml_token']=session('tml_token');
		$result=$Tlinx->get('terminal/cdkey',$map);
		if($result['errcode']==0){
			session('cdkey',$result['data']['cdkey']);
			$this->success($result['msg'],'/posterminal');	
		}else{
			$this->error($result['msg']);	
		}
    }	
	
	//设置机器
    public function set(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(97); 
		$map=I('');
		
		if(!empty($map['tml_no']) && !empty($map['device_code'])){
			session('device_code',$map['device_code']);
			session('tml_no',$map['tml_no']);
			session('tml_type',$map['tml_type']);
			$this->success('机器设置成功');		
		}else if(empty($map['tml_no']) && !empty($map['device_code'])){
			$result=$Tlinx->get('terminal/tml_no',$map);
			if($result['errcode']==0){
				session('device_code',$result['data']['device_code']);
				session('tml_no',$result['data']['tml_no']);
				session('tml_type',$result['data']['tml_type']);
				session('cdkey',$result['data']['cdkey']);
				$this->success('获取机器号成功');	
			}else{
				$this->error($result['msg']);	
			}
		}else{
			$this->error('啥都没填');		
		}
    }
	
	#在线机器列表
    public function online_list(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(97); 
		
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=15;
		//调接口取数据
		$result=$Tlinx->get('terminal',$map);
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
		$this->assign('empty','<tr><td colspan="7"><article class="boxRightCon"><div class="zanwujilu"><div class="zanwujilu_icon">没有在线的机器</div></div></article></td></tr>');			
		$this->paylist=session('paylist');
		if(!isset($map['ord_no'])){
			$map['ord_no']=null;
		}
		if(empty($map['ord_type'])){
			$map['ord_type']="";
		}
		if(empty($map['status'])){
			$map['status']="";
		}	
		if(empty($map['pmt_tag'])){
			$map['pmt_tag']="";
		}
		if(empty($map['sdate'])){
			$map['sdate']="";
		}		
		if(empty($map['edate'])){
			$map['edate']="";
		}		
		$this->map=$map;
		$this->assign('menuid',8);	
		$this->assign('left_menuid',6);	
		$this->display();
    }
	
}