<?php
/**
 * 后台语言包配置文件
 * This is the configuration for generating message translations
 * for the Yii framework. It is used by the 'yiic message' command.
 */
return array(
	'sourcePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'messagePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'messages',
	'languages'=>array('zh_tw','en'),
	'fileTypes'=>array('php'),
	'overwrite'=>true,
	'exclude'=>array(
		'.svn',
		'.gitignore',
		'yiilite.php',
		'yiit.php',
		'/i18n/data',
		'/messages',
		'/vendors',
		'/web/js',
	),
    'languageName'=>array(
        'zh_tw'=>'繁体',
        'en'=>'英文',
    ),
    //语言包目录下的各个文件说明，必须的
    'languageInfoMap'=>array(
        'home.php'=>'网站数据管理',
        'test.php'=>'测试',
    ),
);

/* 批量翻译文件

1.新建一个action,加入下面的代码
2.通过.hk域名，访问相关action

set_time_limit(0);
$messagePath = dirname(Yii::getPathOfAlias('application')).'/backend/messages/zh_tw/*.php';
foreach(glob($messagePath) as $file){
    $message = include $file;
    foreach($message as $k=>&$v){
        $v = Yii::t('',$k);
    }
    $content = '<?php'.PHP_EOL.'//语言包文件'.PHP_EOL.'return '.var_export($message,true).';';
    file_put_contents($file,$content);
}
echo 'ok';

*/