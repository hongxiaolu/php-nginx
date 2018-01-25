<?php
namespace Mct2\Controller;
use Think\Controller;
class NewsController extends Controller {
	#文章列表
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
		$result=$Tlinx->get('news',$map);
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
			$this->assign('empty','<div class="no_data"><img src="/Public/mct2/images/2.0/no_data.png"><p>'.L('_NO_DATA_').'</p></div>');
			$this->page=null;
			$this->list=null;
		}
		
		if(empty($map['news_type'])){
			$map['news_type_name']='文章列表';
		}else{
			if($map['news_type']==1){
				$map['news_type_name']=L('_NEWS_LIST_1_');
			}elseif($map['news_type']==2){
				$map['news_type_name']=L('_NEWS_LIST_2_');
			}else{
				$map['news_type_name']=L('_NEWS_LIST_');
			}
		}
		$this->assign('menuid',0);	
		$this->map=$map;
		$this->display();
    }
	
    public function detail(){
		import("Mct2.Util.Tlinx");
		$Tlinx=new \Tlinx();
		$this->admin=$Tlinx->check_auth(114); //可以访问此页面的权限
		//取得所有参数
		$map=array();
		$map=I('');
		$result=$Tlinx->get('news/detail',$map);
		if($result['errcode']!=0){
			$this->error($result['msg']);
			die();
		}
		$this->rows=$result['data'];
		if($this->rows['news_type']==1){
			$map['news_type_name']=L('_NEWS_LIST_1_');
		}elseif($this->rows['news_type']==2){
			$map['news_type_name']=L('_NEWS_LIST_2_');
		}
		$this->assign('menuid',0);	
		$this->map=$map;
		$this->display();
    }
}