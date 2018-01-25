<?php
return array(
	//多语言支持
	'app_begin' => array('Behavior\CheckLangBehavior'),
     // 支持表单令牌验证功能
	'view_filter' => array('Behavior\TokenBuildBehavior')
);
