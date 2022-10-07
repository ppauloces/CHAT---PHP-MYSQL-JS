<?php 
include("connect.php");

if(isset($_POST["email"]) && isset($_POST["password"])) {
        // Normalization
	$email = $_POST["email"];
	$password = $_POST["password"];

        // Check if values are okay
	if ($email == "" || $password == "") {
		echo "<script>
		Swal.fire({
			title: 'Oops!',
			text: 'Preencha os campos',
			icon: 'error',
			confirmButtonText: 'Tentar novamente'
			})
			</script>";
			die(); 
		}

        // Query
		$stmt = $conn->prepare("SELECT id, password, token, secure FROM user WHERE (email LIKE ? OR username LIKE ?) LIMIT 1");
		$stmt->bind_param("ss", $email, $email);
		$stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();

        // Check password
		if ($user && password_verify($password, $user['password'])) {
			setcookie("ID", $user['id'], time() + (10 * 365 * 24 * 60 * 60));
			setcookie("TOKEN", $user['token'], time() + (10 * 365 * 24 * 60 * 60));
			setcookie("SECURE", $user['secure'], time() + (10 * 365 * 24 * 60 * 60));
			echo "<script>setTimeout(function () { window.location.href = './'; }, 3000);</script>";
		} else {
			echo "<script>
			Swal.fire({
				title: 'Oops!',
				text: 'Senha incorreta',
				icon: 'error',
				confirmButtonText: 'Tentar novamente'
				})
				</script>";
				die(); 
			}
		} else {
			die(header("HTTP/1.0 401 Formulário de autenticação inválido"));
		}

	?>