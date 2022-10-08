<?php
include 'check.php';

$stmt = $conn->prepare("SELECT * FROM conversas WHERE (id_user = ?) ORDER BY modification DESC");
$stmt->bind_param("i",$uid);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->num_rows;

if($count < 1){
	echo '<div class="empty"><p>Pesquise um utilizador e comece uma conversa!</p></div>';
}else{
	while($inbox = $result->fetch_assoc()){
		$stmt = $conn->prepare("SELECT id,username,picture FROM user WHERE (id = ?) LIMIT 1");
		$stmt->bind_param("i",$inbox['id_other_user']);
		$stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();

		if($user){
			?>
			<div class="chat <?php if($inbox['unread'] == "y"){echo "new";} ?>" 
				onclick="chat('<?= $user['id'] ?>');">
				<img src="profilePics/<?= $user['picture'] ?>">
				<p><?= $user['username'] ?></p>
			</div>
			<?php
		}
	}
}
?>