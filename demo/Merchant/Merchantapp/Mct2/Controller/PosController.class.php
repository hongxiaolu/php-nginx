<?php
namespace Mct2\Controller;
use Think\Controller;
class PosController extends Controller {
	#收银台
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(98); //收银台权限
		$this->admin=$admin;
		
		#收款方式列表
		$map['pmt_type']='1,2,3,4,5';
		$result=$Tlinx->get('paylist',$map);
		$paylist=$result['data'];
		if($result['errcode']==0){
			session('paylist',$paylist);
		}else{
			$this->error($result['msg']);
			exit();
		}
		$this->paylist=$paylist;
		
		#收银台应用
		$payapp=session('payapp');
		if(empty($payapp)){
			$appdata['cashier']=1;
			$appdata['type']=1;
			$appdata['status']=1;
			$result=$Tlinx->get('shopapp',$appdata);
			$payapp=$result['data']['list'];
			if($result['errcode']==0){
				session('payapp',$payapp);
			}
		}
		$this->payapp=$payapp;
		#机器上线列表
		$tml_token		=session('tml_token');
		$tml_name		=session('tml_name');
		if(empty($tml_token)){
			$device_code	=session('device_code');#机器码
			$tml_no			=session('tml_no');		#机器号
			if(!empty($device_code) && !empty($tml_no)){
				$data['device_code']	=$device_code;	
				$data['tml_no']			=$tml_no;
				$result=$Tlinx->get('terminal/online',$data);
				if($result['errcode']==0){
					session('tml_token',$result['data']['tml_token']);
					session('tml_name',$result['data']['tml_name']);
					$this->tml_msg='您目前已登录到（'.$result['data']['tml_name'].'）';	
				}else{
					$this->tml_msg='机器配置失败，您目前只能使用网络收银';
				}
			}else{
				$this->tml_msg='您目前只能使用网络收银';	
			}
		}else{
			$this->tml_msg='您目前已登录到（'.$tml_name.'）收银台';
		}
		$map=session('gotopay');
		#获取订单号
		$result_ord_no=$Tlinx->get('payorder/get_ord_no',array('ver_code'=>9));
		$map['ord_no']=$result_ord_no['data']['ord_no'];
		
		if(empty($map['trade_no'])){
			$map['ord_name']=$admin['shop_full_name'].'(普通收银)';
			$map['original_amount']='';
			$map['discount_amount']=0;	
			$map['ignore_amount']=0;
			$map['no_discount_amount']=0;
			$map['trade_amount']='';
			$map['remark']='';
			$order_app=null;#订单应用
			$map['card_bag']=1;
		}else{	
			$order["app_id"]	=$map['app_id'];
			$order["trade_no"]	=$map['trade_no'];
			$order["amount"]	=$map['original_amount'];
			$order["type"]		=1;
			$order["discount"]	=$map['discount_amount'];
			$order["remark"]	=$map['remark'];
			$order["status"]	=2; //订单默认应用状态为待支付
			$order["pay_shop"]	=intval($map['pay_shop']);
			$order["print"]		=$map['print'];
			$order_app			=json_encode($order);#订单应用
			if($map['card_bag']==0){
				$map['card_bag']	=0;
			}else{
				$map['card_bag']	=1;
			}
		}
		
		$this->map=$map;
		$this->order_app=$order_app;
		$this->assign('menuid',8);	
		$this->assign('left_menuid',1);	
		$this->display();
    }
	
	#交易提交
    public function payorder(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(98); //登录收银台权限
		
		//取得所有参数
		$map=array();
		$map=I('');
		$this->assign('menuid',8);	
		$this->assign('left_menuid',1);			
		if(!empty($map['app_para'])){
			foreach($map['app_para'] as $key=>$value){
				$map['app_para'][$key]=json_decode(html_entity_decode($value),true);
			}
		}
		$map['original_amount']=$map['original_amount']*100;
		$map['discount_amount']=$map['discount_amount']*100;
		$map['ignore_amount']=$map['ignore_amount']*100;
		$map['trade_amount']=$map['trade_amount']*100;
		$result=$Tlinx->get('payorder',$map);
		if($result['errcode']==0){
			redirect('/pos/qrcode/ord_no/'.$map['ord_no']);
		}else{
			$this->result=$result;
			$this->display();
		}
		exit();
    }	
	
	#交易状态
	public function paystatus(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(98); //收银台权限
		$this->admin=$admin;
		$map=I('');
		$this->map=$map;
		$result=$Tlinx->get('paystatus',$map);
		
		//if($result['data']['status']==1){		}
		
		
		$this->ajaxReturn($result);
	}		
	
	#二维码
	public function qrcode(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(98); //收银台权限
		$this->admin=$admin;
		$map=I('');
		$this->map=$map;
		$result=$Tlinx->get('order/view',$map);
		$this->rows=$result['data'];
		$this->assign('menuid',8);	
		$this->assign('left_menuid',1);	
		$this->display();
	}	
	
	#交易结果
	public function result(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(98); //收银台权限
		$this->admin=$admin;
		$map=I('');
		$this->map=$map;
		$result=$Tlinx->get('order/view',$map);
		$this->rows=$result['data'];
		$this->assign('menuid',8);	
		$this->assign('left_menuid',1);
		if($result){
			$gotopay=session('gotopay');
			if(!empty($gotopay)){
				$app_notice=S('app_notice_'.$admin['scr_id']);
				$app_notice[]=array('app_id'=>$gotopay['app_id'],'trade_no'=>$gotopay['trade_no'],'ord_no'=>$result['data']['ord_no'],'status'=>$result['data']['status']);
				S('app_notice_'.$admin['scr_id'],$app_notice);
				session('gotopay',null);#清掉定单应用session
			}
		}
		$this->display();
	}
	
	#取消订单
    public function paycancel(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(98); //登录收银台权限
		
		//取得所有参数
		$map=array();
		$map=I('');
		$this->assign('menuid',8);	
		$this->assign('left_menuid',1);			
		
		$result=$Tlinx->get('paycancel',$map);
		if($result['errcode']==0){
			redirect('/pos/result/ord_no/'.$map['ord_no']);		
		}else{
			$this->error($result['msg']);		
		}
    }
		
	#订单退款
    public function payrefund(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$admin=$Tlinx->check_auth(98); //登录收银台权限
		
		//取得所有参数
		$map=array();
		$map=I('');
		$this->assign('menuid',8);	
		$this->assign('left_menuid',1);		
		if(strlen($map['shop_pass'])!=40){
			$map['shop_pass']=sha1($map['shop_pass']);	
		}
		$map['refund_amount']=$map['refund_amount']*100;
		#应用退货参数
		if(!empty($map['app_id'])){
			if($map['trade_amount']==$map['refund_amount']){
				$app_status=4;
			}else{
				$app_status=3;	
			}
			foreach($map['app_id'] as $key=>$value){
				$map['app_para'][]=array('app_id'=>$value,'status'=>$app_status);
				$app_notice=S('app_notice_'.$admin['scr_id']);
				$app_notice[]=array('app_id'=>$value,'trade_no'=>$map['trade_no'][$key],'ord_no'=>$map['refund_ord_no'],'status'=>$app_status);
			}
			unset($map['app_id']);#去掉app_id字段
		}
		$result=$Tlinx->get('payrefund',$map);
		if($result['errcode']==0){
			if(!empty($app_notice)){
				S('app_notice_'.$admin['scr_id'],$app_notice);#记录通知	
			}
			$this->success($result['msg'],'/pos/result/ord_no/'.$map['refund_ord_no']);
		}else{
			$this->error($result['msg']);		
		}
    }		
}
