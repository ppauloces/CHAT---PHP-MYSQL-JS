<?php
include("connect.php");

	function timing($time){
		$time = time() - $time;
		$time = ($time<1) ? 1 : $time;
		$tokens = array(
			31536000 => 'ano',
			2592000 => 'mês',
			604800 => 'semana',
			86400 => 'dia',
			3600 => 'hora',
			60 => 'minuto',
			1 => 'segundo'
		);

		foreach ($tokens as $unit => $text) {
			if($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			if($text == "segundo"){
				return "agora mesmo";
			}
			return $numberOfUnits.''.$text.(($numberOfUnits>1)?'s':'');
		}
	}

if (isset($_COOKIE['ID']) && isset($_COOKIE['TOKEN']) && isset($_COOKIE['SECURE'])) {
	$id = $_COOKIE['ID'];
	$token = $_COOKIE['TOKEN'];
	$secure = $_COOKIE['SECURE'];

	$stmt = $conn->prepare("SELECT id, username, picture, online, creation FROM user WHERE (id = ? AND token LIKE ? AND secure = ?) LIMIT 1");
	$stmt->bind_param("isi",$id,$token,$secure);
	$stmt->execute();
	$me = $stmt->get_result()->fetch_assoc();

	if(!$me){
		die("<script>location.href='auth.html'</script>");
	}else{
		$uid = $me['id'];
		$username = $me['username'];
		$user_picture = $me['picture'];
		$user_online = strtotime($me['online']);
		$user_creation = date('d/m/Y',strtotime($me['creation']));

		$stmt = $conn->prepare("UPDATE user set online = now() WHERE id = ?");
		$stmt->bind_param("i",$uid);
		$stmt->execute();
	}
}else{
	die("<script>location.href='auth.html'</script>");
}
?>