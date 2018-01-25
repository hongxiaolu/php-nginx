<?php
return array(
	'organ_verify_code'			=>	false,	//是否启用验证码
	'organ_open_id'				=>	860000023,	//运营平台编号
	'organ_public_key' 			=>	'../Common/860000023_public_key.pem',	//运营平台公钥（绝对路径）
	'organ_private_key' 		=>	'../Common/860000023_private_key.pem',	//运营平台私钥（绝对路径）
	'STATEMENT_PATH'			=>	'../TlinxFiles/statement/',         //对帐单路径
	'organ_open_url'			=>	'http://api.s.bankgz.com/org1/',	//交易服务器地址
	'organ_update_url'			=>	'http://api.s.bankgz.com/Oemver/?type=ORGAN',	//版本更新地址
	'organ_upload_url'			=>	'http://up.s.bankgz.com/fileup1/',	//文件上传服务器地址
	'organ_open_app'			=>	'www', //应用的名称（最长10个字符）	
	'organ_site_name'			=>	'赣州银行运营平台', //站点名称	
	'organ_beian_code'			=>	'', //备案号
	#系统显示层配置参数
	'organ_con_id_btn'			=>	true,#是否开放自动生成合同编号按钮
	'organ_bl_no_check_url'		=>	'http://gsxt.saic.gov.cn',#营业执照核查地址
	'organ_mct_admin'			=>	true,   #是否启用推荐人功能
	'organ_view_bill'			=>	true,#是否启用清算及报表管理功能

	#定制报表下载
	'organ_work_report'			=>array(
		'report1'				=>'日报',	
		'report2'				=>'月报',
		)
);