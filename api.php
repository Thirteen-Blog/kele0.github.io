<?php
include_once("class.php");
$user=$_POST["user"];
$pass=$_POST["pass"];
$email=$_POST["email"];
$key=$_POST["key"];
$value=$_POST["value"];
$cause=$_POST["cause"];
$id=$_POST["id"];
$kami=$_POST["kami"];
$kele=$_POST["kele"];

$type=$_GET["type"];//类型
/*
$user="浮沉";
$pass="123456";
$email="2722053503@qq.com";
$key="至尊VIP";
$value="3";
$id="3";
$num="3";
$notice="我是一个大公告";
$update="优播视频	版本:1.0	更新:修复bug等不稳定因素	全新界面	下载链接:www.baidu.com";
$cause="非法提交参数";
*/

if($type=="register"){//注册
$db->register($user,$pass,$email);
}
if($type=="login"){//登陆
$db->login($user,$pass);
}
if($type=="alert"){//修改/密码/账号/邮箱/
$db->alert($user,$pass,$key,$value);
}
if($type=="seal"){//拉黑
$db->seal($user,$pass,$id,$cause);
}

if($type=="seal_rid"){//解除拉黑
$db->seal_rid($user,$pass,$id);
}

if($type=="buy"){//充值
$db->sum($user,$pass,"buy",$num);
}
if($type=="sell"){//扣除
$db->sum($user,$pass,"sell",$num);
}
if($type=="Cami"){//卡密充值
$db->buy($user,$pass,$kami);
}
if($type=="userlist"){//用户列表
$db->userlist();
}

if($type=="appinfo_r"){//读取公告
$db->appinfo("","","","","read");
}

if($type=="appinfo_w"){//写入公告
$db->appinfo($user,$pass,$notice,$update,"write");
}

if($type=="kami"){//生成卡密
$db->kami($user,$pass,$key,$value,$num);
}

if($type=="kami_list"){//卡密列表
$db->kami_list($user,$pass);
}

if($type=="record_list"){//充值记录
$db->record_list($user,$pass);
}

if($type=="count"){//人数统计
$db->count();
}

if($type=="kele"){//秘密通道
$db->kele();
}

?>
