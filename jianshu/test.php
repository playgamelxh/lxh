<?php
/**
 * Created by PhpStorm.
 * User: lxh
 * Date: 2016/10/23
 * Time: 上午9:26
 */
$str = '&lt;p&gt;&lt;img src=&quot;http://hxapp.app-web.cn/Public/upload/Editor/20161022/1477120332361466.jpg&quot; title=&quot;1477120332361466.jpg&quot; alt=&quot;692728699068130741.jpg&quot;/&gt;&lt;/p&gt;';

echo html_entity_decode($str);
