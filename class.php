<title>浮沉-api用户系统</title>
<?php
class way{
	public $host="localhost";
	//数据库地址
	public $user="root";
	//数据库账号
	public $pass="";
	//数据库密码
	public $db="User-system";
	//数据库
	public $utf="utf-8";
	//数据库配置
	public $user_long="5";
	//账号长度，最长:10
	public $state="true";
	//账号状态
	public $sum="10";
	//账号初始金额
	public $grade="普通用户";
	//初始账号等级
	public $expiry="长期";
	//到期时间，长期:永不过期 
	//格式:年-月-日 时:分:秒
	//注册配置
	public $appfile="app.txt";
	//app信息储存文件
	//app信息配置
	public $recordfile="record.txt";
	//充值记录储存文件
	function __construct(){
		$this->connect();
		date_default_timezone_set("Asia/Shanghai");
	}
	function connect(){
		//连接数据库
		$link=@mysql_connect($this->host,$this->user,$this->pass) or die ($this->put("登陆失败",$this->error()));
		mysql_select_db($this->db,$link)or die($this->put("数据库未找到",$this->error()));
		mysql_query("SET NAMES '$this->utf'");
		//设置编码
	}
	function insert($table,$array){
		$count=count($array);
		$coun="1";
		foreach($array as $key=>$value){
			if(!$key){
				continue;
			}
			if($count>$coun){
				$name=$name."`".$key."`,";
				$val=$val."'".$value."',";
			} else{
				$name=$name."`".$key."`";
				$val=$val."'".$value."'";
			}
			$coun++;
		}
		$sql="INSERT INTO`$table`($name)VALUES($val);";
		if(mysql_query($sql)){
			return "true";
		}
		return "false";
	}
	function query($sql){
		$this->fetch=null;
		$back=mysql_query($sql);
		$this->fetch($back);
		return $back;
	}
	function fetch($back){
		$this->fetch=@mysql_fetch_array($back);
	}
	function error(){
		//错误提示
		return mysql_error();
	}
	function delete($table,$id){
		$this->inject($table);
		$this->inject($id);
		$sql="DELETE FROM `$table` WHERE `$table`.`id`=$id";
		if($this->query($sql)){
			return "false";
		} else{
			return "true";
		}
	}
	//--(◔◡◔)----------------自定义操作//
	function register($user,$pass,$email){
		//注册
		if(!$user||!$pass||!$email){
			exit($this->put("提示","请填写完整！"));
		}
		$this->inject($user);
		$this->inject($pass);
		$this->inject($email);
		if(!preg_match("/(.*)@(.*).com/",$email)){
			exit($this->put("提示","邮箱格式错误!"));
		}
		if($this->long($user)>$this->user_long){
			//限制昵称长度
			exit($this->put("提示","账号名过长!"));
		}
		if($this->long($pass)>"10"){
			//限制密码长度:10
			exit($this->put("提示","密码过长!"));
		}
		$date=date("Y-m-d H:i:s");
		$info=array("id"=>"NULL","user"=>$user,"pass"=>$pass,"email"=>$email,"state"=>$this->state,"grade"=>$this->grade,"expiry"=>$this->expiry,"sum"=>$this->sum,"ip"=>$this->ip(),"date"=>$date);
		$back=$this->insert("user",$info);
		if($back=="true"){
			exit($this->put("提示","注册成功，请牢记以下信息！</br>账号:$user</br>密码:$pass"));
		} else{
			$error=$this->error();
			$sql="Duplicate entry '$user' for key 'user'";
			if($error==$sql){
				exit($this->put("提示","注册失败！<br>原因:用户名已被占用"));
			}
			exit($this->put("提示","注册失败！"));
		}
	}
	function login($user,$pass,$type=null){
		//登陆
		$sql="SELECT *  FROM `user` WHERE `user` LIKE '$user' AND `pass` LIKE '$pass'";
		$this->query($sql);
		$us=$this->fetch;
		if($this->expiry($us["id"])=="true"){
			$this->query($sql);
			$us=$this->fetch;
		}
		if($us["user"]==$user){
			if($us["state"]!="true"){
				exit($this->put("警告","您已被拉黑！</br>原因:".$us["state"]));
			}
			if($type!="1"){
				$info="</br>账号:".$us['user']."<br>金额:".$us['sum']."</br>账号状态:".$us['state']."</br>等级:".$us['grade']."</br>到期时间:".$us['expiry']."</br>邮箱:".$us['email']."</br>注册时间:".$us['date'];
				echo $this->put("提示","登陆成功！".$info);
			}
			return "true";
			exit();
		} else{
			exit($this->put("提示","账号或密码错误！"));
		}
	}
	function expiry($id){
		//会员到期
		$sql="SELECT *  FROM `user` WHERE `id`=$id";
		$this->query($sql);
		$info=$this->fetch;
		if($info["expiry"]=="长期"){
			return "false";
		}
		if(!$info){
			return "false";
		}
		$date=date("Y-m-d H:i:s");
		$now=strtotime($date);
		$time=strtotime($info["expiry"]);
		if($now>$time){
			$sql="UPDATE `user` SET `grade`='普通用户', `expiry`='长期' WHERE `user`.`id` = $id;";
			$this->query($sql);
			$tw='<font color="red">'.$info['grade']."</font>已过期！";
			echo $this->put("提示",$tw);
			return "true";
		}
		return "false";
	}
	//账号，密码，关键字，修改值
	function alert($user,$pass,$type,$value){
		//修改
		if($type=="user"||$type=="pass"||$type=="email"){
		} else{
			exit($this->put("提示","参数错误！"));
		}
		$this->inject($value);
		$this->inject($user);
		$this->inject($pass);
		if($type=="user"){
			if($this->long($value)>$this->user_long){
				//限制昵称长度
				exit($this->put("提示","账号名过长!"));
			}
		}
		if($type=="email"){
			if(!preg_match("/(.*)@(.*).com/",$value)){
				exit($this->put("提示","邮箱格式错误!"));
			}
		}
		if($type=="pass"){
			if($this->long($value)>"10"){
				//限制密码长度:10
				exit($this->put("提示","密码过长!"));
			}
		}
		if($this->login($user,$pass,"1")!="true"){
			exit($this->put("提示","账号或密码错误！"));
		}
		$info=$this->fetch;
		$id=$info["id"];
		$sql="UPDATE `user` SET `$type`='$value' WHERE `user`.`id`=$id;";
		$this->query($sql);
		$sql="SELECT *  FROM `user` WHERE `id`=$id";
		$this->query($sql);
		$info=$this->fetch;
		if($info[$type]==$value){
			exit($this->put("提示","修改成功！"));
		} else{
			exit($this->put("提示","修改失败！"));
		}
	}
	function seal($user,$pass,$id,$cause){
		//拉黑
		$this->inject($id);
		$this->power($user,$pass);
		/*
		if($this->login($user,$pass,"1")!="true"){
			exit($this->put("提示","账号或密码错误！"));
		}
		$info=$this->fetch;
		if($info["grade"]!="超级管理员"){
			exit($this->put("警告","您没有权限！"));
		}
		*/
$sql="SELECT *  FROM `user` WHERE `id`=$id";
		$this->query($sql);
		$info=$this->fetch;
		if(!info){
			exit($this->put("提示","未找到该条信息！"));
		}
		if(!$id||!$cause){
			exit($this->put("提示","请填写完整！"));
		}
		$value="拉黑:".$cause;
		$sql="UPDATE `user` SET `state`='$value' WHERE `user`.`id`=$id;";
		$this->query($sql);
		$sql="SELECT *  FROM `user` WHERE `id`=$id";
		$this->query($sql);
		$info=$this->fetch;
		if($info["state"]==$value){
			exit($this->put("提示",$info["user"]."/拉黑成功！"));
		} else{
			exit($this->put("提示","拉黑失败！"));
		}
	}
	
	function seal_rid($user,$pass,$id,$value="true"){
		$this->inject($id);
		$this->inject($value);
		$this->power($user,$pass);
$sql="SELECT *  FROM `user` WHERE `id`=$id";
		$this->query($sql);
		$info=$this->fetch;
		if(!info){
			exit($this->put("提示","未找到该条信息！"));
		}
		if(!$id||!$value){
			exit($this->put("提示","请填写完整！"));
		}
		$sql="UPDATE `user` SET `state`='$value' WHERE `user`.`id`=$id;";
		$this->query($sql);
		$sql="SELECT *  FROM `user` WHERE `id`=$id";
		$this->query($sql);
		$info=$this->fetch;
		if($info["state"]==$value){
			exit($this->put("提示",$info["user"]."/解除成功！"));
		} else{
			exit($this->put("提示","解除失败！"));
		}
     }
	function sum($user,$pass,$way,$num){
		//金额加减
		$this->inject($way);
		$this->inject($num);
		if($this->login($user,$pass,"1")!="true"){
			exit($this->put("提示","账号或密码错误！"));
		}
		$info=$this->fetch;
		$id=$info["id"];
		$sum=$info["sum"];
		if($way=="buy"){
			//充值
			if($info["grade"]!="超级管理员"){
				exit($this->put("警告","您没有权限！"));
			}
			$money=$sum+$num;
			$wa="充值";
		}
		if($way=="sell"){
			//扣除
			if($num>$sum){
				exit($this->put("提示","扣除失败！</br>原因:余额不足"));
			}
			$money=$sum-$num;
			$wa="扣除";
		}
		$sql="UPDATE `user` SET `sum`='$money' WHERE `user`.`id`=$id;";
		$this->query($sql);
		$sql="SELECT *  FROM `user` WHERE `id`=$id";
		$this->query($sql);
		$info=$this->fetch;
		$mon=$info["sum"];
		if($info["sum"]==$money){
			exit($this->put("提示",$wa."成功！</br>当前余额:$mon"));
		} else{
			exit($this->put("提示",$wa."失败！"));
		}
	}
	function userlist(){
		//用户列表
		$sql="SELECT * FROM `user`";
		//$db=$this->query($sql);
		$db=mysql_query($sql);
		$add=array();
		$cont=null;
		while($row=@mysql_fetch_row($db)){
			$uid=$row["0"];
			$array=array("账号"=>$row["1"],"余额"=>$row["7"],"状态"=>$row["4"],"等级"=>$row["5"],"到期时间"=>$row["6"],"邮箱"=>$row["3"],"注册时间"=>$row["9"]);
			$add[$uid]=$array;
			$cont.="账号:".$row["1"]."\t余额:".$row["7"]."\t状态:".$row["4"]."\t等级:".$row["5"]."\t到期时间:".$row["6"]."\t邮箱:".$row["3"]."\t注册时间:".$row["9"]."</br>";
		}
		echo "<pre>";
		$json=json_encode($add);
		echo $this->put("json格式",$json);
		echo $this->put("列表格式",$cont);
		exit();
	}
	function appinfo($user,$pass,$notice,$update,$way="read"){
		//app公告，更新
		//公告，更新
		$info=array();
		$date=date("Y-m-d H:i:s");
		if(!file_exists($this->appfile)){
			file_put_contents($this->appfile,"");
			echo($this->put("提示","appinfo初始化成功！"));
		}
		if($way=="write"){
					$this->power($user,$pass);
			//写入
			$info["公告"]=$notice;
			$info["更新"]=$update;
			$json=json_encode($info);
			if(file_put_contents($this->appfile,$json)){
				exit($this->put("提示:公告/更新","写入成功！</br>编辑于:$date"));
			} else{
				exit($this->put("提示:公告/更新","写入失败！"));
			}
		}
		if($way=="read"){
			//读取
			$json=file_get_contents($this->appfile);
			$array = json_decode($json, true);
			echo($this->put("公告",$array["公告"]));
			echo($this->put("更新",$array["更新"]));
			exit();
		}
	}
	function kami($user,$pass,$type,$value,$num){
		$this->power($user,$pass);
		$ber="QWERTYUIOPLKJHGFDSAZXCVBNM1234567890";
		$info=array();
		$ec="";
		$long=strlen($ber);
		for ($i=0;$i<$num;$i++){
			$card="";
			for ($ii=0;$ii<13;$ii++){
				$card=$card.$ber[mt_rand(0,$long)];
			}
			$ec="类型:".$type."\t数值:".$value."\t卡密:".$card."\n".$ec;
			$info=array("id"=>"NULL","kami"=>$card,"type"=>$type,"value"=>$value);
			if($back=$this->insert("kami",$info)!="true"){
				exit($this->put("提示","卡密生成失败！"));
			}
		}
		echo "<pre>";
		exit($this->put("提示","卡密生成成功！<br>".$ec));
	}
	function kami_list($user,$pass){
		$this->power($user,$pass);
		$sql="SELECT * FROM `kami`";
		//$db=$this->query($sql);
		$db=mysql_query($sql);
		$num=@mysql_num_rows($db);
		$add=array();
		$cont=null;
		echo "<pre>";
		while($row=@mysql_fetch_row($db)){
			$ec.="类型:".$row["2"]."\t数值:".$row["3"]."\t卡密:".$row["1"]."\n";
		}
		exit($this->put("提示","共有".$num."条卡密</br>".$ec));
	}
	function buy($user,$pass,$kami){
		$this->inject($kami);
		if($this->login($user,$pass,"1")!="true"){
			exit($this->put("提示","账号或密码错误！"));
		}
		$info=$this->fetch;
		$id=$info["id"];
		$sum=$info["sum"];
		//获取充值用户信息
		$sql="SELECT *  FROM `kami` WHERE `kami` LIKE '$kami'";
		$this->query($sql);
		$kami=$this->fetch;
		$kami_id=$kami["id"];
		if(!$kami){
			exit($this->put("提示","卡密不存在！"));
		}
		//获取卡密信息
		if($kami["type"]=="金额"){
			$money=$sum+$kami["value"];
			$sql="UPDATE `user` SET `sum`='$money' WHERE `user`.`id`=$id;";
		} else{
			$time=$kami["value"];
			//卡密数值
			$type=$kami["type"];
			//卡密类型
			$expiry=date("Y-m-d",strtotime("+".$time."day"));
			$sql="UPDATE `user` SET `grade`='$type', `expiry`='$expiry' WHERE `user`.`id` = $id;";
		}
		$this->query($sql);
		$sql="SELECT *  FROM `user` WHERE `id`=$id";
		$this->query($sql);
		$info=$this->fetch;
		if($info[$type]==$value){
			$this->record($id,$kami["kami"],"充值成功");
			$this->delete("kami",$kami_id);
			exit($this->put("提示","充值成功！"));
		} else{
			$this->record($id,$kami["kami"],"充值失败");
			exit($this->put("提示","充值失败！"));
		}
	}
	function record($uid,$kami,$start){
		//充值记录
		if(!file_exists($this->recordfile)){
			file_put_contents($this->recordfile,"");
			echo($this->put("提示","record初始化成功！"));
		}
		$sql="SELECT *  FROM `user` WHERE `id`=$uid";
		$this->query($sql);
		$info=$this->fetch;
		$user=$info["user"];
		$sql="SELECT *  FROM `kami` WHERE `kami` LIKE '$kami'";
		$this->query($sql);
		$kami=$this->fetch;
		$kami_id=$kami["id"];
		$date=date("Y-m-d H:i:s");
		$info=array();
		$json=file_get_contents($this->recordfile);
		$info= json_decode($json, true);
		$array=array("充值用户"=>$user,"充值类型"=>$kami["type"],"面值"=>$kami["value"],"充值状态"=>$start,"充值时间"=>$date);
		$info[]=$array;
		echo "<pre>";
		print_r($info);
		$json=json_encode($info);
		file_put_contents($this->recordfile,$json);
	}
	function  record_list($user,$pass){
		$this->power($user,$pass);
		if(!file_exists($this->recordfile)){
			file_put_contents($this->recordfile,"");
			echo($this->put("提示","record初始化成功！"));
		}
		$json=file_get_contents($this->recordfile);
		$array = json_decode($json, true);
		//解析json
		$count=count($array);
		//获取条数
		echo "<pre>";
		foreach($array as $key=>$value){
			$info.="充值用户:".$value["充值用户"]."\t充值类型:".$value["充值类型"]."\t面值:".$value["面值"]."\t充值状态:".$value["充值状态"]."\t充值时间:".$value["充值时间"]."</br>";
		}
		exit($this->put("充值记录","共有".$count."条记录</br>".$info));
	}
	function count($table="user"){
		$sql="SELECT * FROM `$table`";
		$db=mysql_query($sql);
		$num=@mysql_num_rows($db);
		if(!$num){
			$num="0";
		}
		exit($this->put("查询","结果:".$num));
	}
	//--•ᴗ•--------------------自定义函数//
	function power($user,$pass){
		if($this->login($user,$pass,"1")!="true"){
			exit($this->put("提示","登陆失败！"));
		}
		$info=$this->fetch;
		$id=$info["id"];
		$sum=$info["sum"];
		if($info["grade"]!="超级管理员"){
			exit($this->put("警告","您没有权限！"));
		}
	}
	function long($string) {
		//获取字符串长度
		// 将字符串分解为单元
		preg_match_all("/./us", $string, $match);
		// 返回单元个数
		return count($match[0]);
	}
	function inject($sql_str) {
		//防止注入
		$check=preg_match('/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/', $sql_str);
        if($check) {
           echo $this->put("警告","输入非法注入内容!");
            exit ();
        } else {
            return $sql_str;
        }
    }
    function kele($check){
		//秘密通道
	$check=preg_match('我是中国人');
        if($check) {
           echo $this->put("你好！","我也是中国人！");
            exit ();
        } else {
            return $check;
        }
	}
function put($title,$content){
if($title=="警告"){
$title='<font color="red">'.$title."</font>";
}
return '<fieldset>'."<legend>$title</legend>$content</fieldset>";
}
function ip() { 
if (getenv('HTTP_CLIENT_IP')) { 
$ip = getenv('HTTP_CLIENT_IP'); 
} 
elseif (getenv('HTTP_X_FORWARDED_FOR')) { 
$ip = getenv('HTTP_X_FORWARDED_FOR'); 
} 
elseif (getenv('HTTP_X_FORWARDED')) { 
$ip = getenv('HTTP_X_FORWARDED'); 
} 
elseif (getenv('HTTP_FORWARDED_FOR')) { 
$ip = getenv('HTTP_FORWARDED_FOR'); 
} 
elseif (getenv('HTTP_FORWARDED')) { 
$ip = getenv('HTTP_FORWARDED'); 
} 
else { 
$ip = $_SERVER['REMOTE_ADDR']; 
} 
return $ip;
}
}
$db=new way();
/*
调用实际
echo $db->alert("浮沉n","123456","user","浮沉");
echo $db->seal("浮沉","123456","2","提交违法参数");
echo $db->sum("老王","6655","buy","5");
echo $db->userlist();
echo $db->appinfo("俺是公告","GG视频\t版本:1.0\t更新:修复bug等不稳定因素\t全新界面\t下载链接:www.baidu.com","read");
echo $db->kami("浮沉","123456","VIP","2","5");
echo $db->buy("浮沉","123456","GHIBUSBOFY2N");
echo $db->record_list("浮沉","123456");
echo $db->count();
*/
?>
