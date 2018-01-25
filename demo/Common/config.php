<?php
return array(
	#本地数据库配置
	'DB_TYPE'   			=> 'mysql', // 数据库类型
	'DB_HOST'  		 	=> 'localhost', // 服务器地址
	'DB_NAME'  		 	=> 'organ_gzyh', // 数据库名
	'DB_USER'   			=> 'tlinx', // 用户名
	'DB_PWD'    			=> 'tlinx', // 密码
	'DB_PORT'   			=> 3306, // 端口
	'DB_PREFIX' 			=> 'tbl_', // 数据库表前缀 
	'DB_CHARSET'			=> 'utf8', // 字符集
	'DB_DEBUG'  			=>  true, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增

	#服务器参数配置
	'URL_MODEL'					=> 2,			# URL路径模式2，不显示index.php
	'SHOW_PAGE_TRACE'			=> false,		# 显示跟踪调试
	'PAGE_TRACE_SAVE'   		=> false,   	# 保存跟踪信息
	'LANG_SWITCH_ON' 			=> true,   		# 开启语言包功能
	'LANG_AUTO_DETECT'			=> true, 		# 自动侦测语言 开启多语言功能后有效
	'DEFAULT_LANG'        		=> 'zh-cn', 	# 默认语言
	'LANG_LIST'        			=> 'zh-cn,zh-tw', # 允许切换的语言列表 用逗号分隔
	'VAR_LANGUAGE'     			=> 'lang',		# 默认语言切换变量
	
	'SIGN_KEY' 					=> 'db3412a2223265e8c8b0ff27a844d2eecd81669dbaa544ebc7db47bfc34d2dac', //内部站点之间数据传输签名密钥
    'CRYPT_KEY' 				=> 'abcdefg',#内部站点之间数据传输密钥
	'F_SERVER'					=> 'http://sf.s.bankgz.com/', 		#公共文件访问服务器
    'UP_SERVER'					=> 'http://sup.s.bankgz.com',		#文件上传服务器，只内部使用，可用非公网地址
	'TEMP_PATH'					=>	'../TlinxFiles/upload/temp/',   #临时文件夹路径
);