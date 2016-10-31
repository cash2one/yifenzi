<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2016/9/29
 * Time: 15:22
 */
?>

<br/>
<br/>
<br/>
<br/>
<form action="/wpay/cz" method="post">
    <input type="hidden" value="<?php echo Yii::app()->getRequest()->getCsrfToken(); ?>" name="YII_CSRF_TOKEN" />
    <input name="from_c" value="请输入转出GW" type="text">
到：
    <input name="to_c" value="请输入收账GW" type="text" >
    <input type="submit" name="提交">
<form/>
