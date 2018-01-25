<?php
namespace Mct2\Controller;
use Think\Controller;
class PosorderController extends Controller {

	#交易流水
    public function index(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(99); 
		
		//取得所有参数
		$map=array();
		$map=I('');
		$map['page']=I('p');
		//每页显示数量
		$map['pagesize']=15;
		//调接口取数据
		$result=$Tlinx->get('order',$map);
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
		$this->assign('empty','<tr><td colspan="10"><article class="boxRightCon"><div class="zanwujilu"><div class="zanwujilu_icon">暂无相关信息</div></div></article></td></tr>');			
		$this->paylist=session('paylist');
		if(!isset($map['ord_trade_no'])){
			$map['ord_trade_no']=null;
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
		$this->assign('left_menuid',3);	
		$this->display();
    }	
	
	#交易明细
	public function view(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(99); 
		
		//取得所有参数
		$map=array();
		$map=I('');
		$this->map=$map;
		$result=$Tlinx->get('order/view',$map);
		$this->rows=$result['data'];
		$this->map=$map;
		$this->assign('menuid',8);	
		$this->assign('left_menuid',3);	
		$this->display();
	}
	
	#取消订单
	public function cancel(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(99); 
		
		//取得所有参数
		$map=array();
		$map=I('');
		$this->map=$map;
		$result=$Tlinx->get('order/view',$map);
		$rows=$result['data'];
		if($rows['status']!=2 && $rows['status']!=9){
			$this->error('当前订单不能取消');
			exit();
		}
		$this->rows=$rows;
		$this->map=$map;
		$this->assign('menuid',8);	
		$this->assign('left_menuid',3);	
		$this->display();
	}	
	
	#退款
	public function refund(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(99); 
		
		//取得所有参数
		$map=array();
		$map=I('');
		$this->map=$map;
		$result=$Tlinx->get('order/view',$map);
		$rows=$result['data'];
		if($rows['ord_type']==2){
			$this->error('当前订单不能退款');
			exit();
		}
		#获取订单号
		$result_ord_no=$Tlinx->get('payorder/get_ord_no',array('ver_code'=>9));
		$this->refund_ord_no=$result_ord_no['data']['ord_no'];
		$this->tml_token=session('tml_token');

		$this->rows=$rows;
		$this->map=$map;
		$this->assign('menuid',8);	
		$this->assign('left_menuid',3);	
		$this->display();
	}		
}