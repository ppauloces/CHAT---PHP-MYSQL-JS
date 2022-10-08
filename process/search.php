<?php 
	include("check.php");

	if(isset($_GET['term'])){
		$username = mysqli_real_escape_string($conn, $_GET['term']);

		$stmt = $conn->prepare("SELECT id, username, picture FROM user WHERE (username LIKE '%$username%' AND id != '$uid') ORDER BY username DESC LIMIT 20");
		$stmt->execute();
		$result = $stmt->get_result();
		$count = $result->num_rows;

		if($count < 1){
			echo "<p class='noResults'>Sem resultados</p>";
		}else{
			while ($user = $result->fetch_assoc()) {
				?>
				<div class="row" onclick="$('#searchContainer').hide();chat('<?= $user['id'] ?>');">
					<img src="profilePics/<?= $user['picture'] ?>">
					<p><?= $user['username'] ?></p>
				</div>
				<?php
			}
		}
	}
?>