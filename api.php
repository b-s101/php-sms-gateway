<?php
//Change the following four entries according to your environment
$mysql_srv='localhost'
$mysql_db='smsd'
$mysql_user='smsd-usr'
$mysql_pass='smsd_usr_pass'

if ((!isset($_GET['user'])) || (!isset($_GET['pw'])) || (!isset($_GET['text'])) || (!isset($_GET['dest']))) {
	header("Refresh: 3; URL=http://somewhere");
	echo 'You missed a few things. RTFM and try again <br> Redirecting in three seconds...';
	exit;
}

$_SERVER['PHP_AUTH_USER']=$_GET['user'];
$_SERVER['PHP_AUTH_PW']=$_GET['pw'];

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="PHP-SMS-Gateway"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentication Required by SMS API';
    exit;
} else {
	include_once 'auth.php';
	$auth=http_authenticate($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
//	echo $auth . '<br>';
		if ((http_authenticate($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']))==TRUE) {
			include_once 'sms_inject.php';
//			echo $_SERVER['PHP_AUTH_USER'] . '<br>';
//			echo $_SERVER['PHP_AUTH_PW'] . '<br>';
			$user=$_GET['user'];
			$res=mysql_connect($mysql_srv,$mysql_user,$mysql_pass);
			mysql_select_db($mysql_db,$res);

			$sms=new sms_inject($res);
				$msg=htmlspecialchars($_GET['text']);
				$destnums=explode(',',htmlspecialchars($_GET['dest']));
				foreach ($destnums as $destnum) {
					$sms->send_sms($msg,$destnum,'',$user);

				}

	}else{
		echo 'authentication failed <br>';
		}
}
?>