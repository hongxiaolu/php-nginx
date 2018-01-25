<?php
return array(
	'mct_verify_code'			=>	false,	//是否启用验证码
	'mct_open_id'				=>	860000023,	//运营平台编号
	'mct_open_url'				=>	'http://api.s.bankgz.com/mct1/',    //交易服务器地址
	'mct_upload_url'			=>	'http://up.s.bankgz.com/fileup1/',	//文件上传服务器地址
	'mct_public_key' 			=>	'../Common/tlinx_public_key.pem',   //Tlinx公共公钥路径
	'mct_cert_path'				=>	'../TlinxFiles/cert/merchant/',     //云平台商户证书保存路
	
	'mct_app_name'				=>	'www', //应用类型径
	'mct_qrcode_list'			=>	array(7,8), //二维码模板
	'mct_site_name'				=>	'赣州银行商户云服务平台', //站点名称	
	'mct_beian_code'			=>	'',  //备案号
	'mct_kefu_tel'				=>	'xxx', //客服电话
	'mct_kefu_open_hours'		=>	'服务时间为工作日9:00- 18:00', //服务时间
	'mct_kefu_email'			=>	'kefu@xxx.com', //邮箱
	'mct_aboutus_url'			=>	'',   //关于我们链接
	'mct_contactus_url'			=>	'',  //联系我们链接
);