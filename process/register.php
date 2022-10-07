<?php 
include("connect.php");

if(isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["Rpassword"])) {

        // Normalization
	$username = $_POST["username"];
	$email = $_POST["email"];
	$password = $_POST["password"];
	$RepPassword = $_POST["Rpassword"];

        // Check if values are okay
	if ($username == "" || $email == "" || $password == "" || $RepPassword == "") {
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

        // Check if username already exists
		$checkUsername = $conn->prepare("SELECT Id FROM User WHERE Username = ?");
		$checkUsername->bind_param("s", $username);
		$checkUsername->execute();
		$count = $checkUsername->get_result()->num_rows;
		if ($count > 0) {
			echo "<script>
			Swal.fire({
				title: 'Oops!',
				text: 'Já existe um usuário com esse mesmo nome',
				icon: 'error',
				confirmButtonText: 'Tentar novamente'
				})
				</script>";
				die(); 
			}

        // Check if email already exists
			$checkEmail = $conn->prepare("SELECT Id FROM User WHERE Email = ?");
			$checkEmail->bind_param("s", $email);
			$checkEmail->execute();
			$count = $checkEmail->get_result()->num_rows;
			if ($count > 0) {
				echo "<script>
				Swal.fire({
					title: 'Oops!',
					text: 'Esse email já existe.',
					icon: 'error',
					confirmButtonText: 'Tentar novamente'
					})
					</script>";
					die(); 
				}

        // Verify password repeat
				if ($password != $RepPassword) {
					echo "<script>
					Swal.fire({
						title: 'Oops!',
						text: 'Senhas diferentes',
						icon: 'error',
						confirmButtonText: 'Tentar novamente'
						})
						</script>";
						die(); 
					}

        // Ecrypt password
					$password = password_hash($password, PASSWORD_DEFAULT);

        // Create secure code and token
					$token = bin2hex(openssl_random_pseudo_bytes(20));
					$secure = rand(1000000000, 9999999999);

        // Queries for creation and collection
					$stmt = $conn->prepare("INSERT INTO user (`username`, `email`, `password`, `online`, `token`, `secure`, `creation`) 
						VALUES (?, ?, ?, now(), ?, ?, now())");
					$stmt->bind_param("ssssi", $username, $email, $password, $token, $secure);
					$stmt->execute();

					$getUser = $conn->prepare("SELECT id, token, secure FROM user WHERE email = ?");
					$getUser->bind_param("s", $email);
					$getUser->execute();
					$user = $getUser->get_result()->fetch_assoc();

					if ($stmt && $user) {
						setcookie("ID", $user['id'], time() + (10 * 365 * 24 * 60 * 60));
						setcookie("TOKEN", $user['token'], time() + (10 * 365 * 24 * 60 * 60));
						setcookie("SECURE", $user['secure'], time() + (10 * 365 * 24 * 60 * 60));
						return true;
					} else {
						echo "<script>
						Swal.fire({
							title: 'Oops!',
							text: 'Ocorreu um erro com a base de dados',
							icon: 'error',
							confirmButtonText: 'Tentar novamente'
							})
							</script>";
							die(); 
						}
					} else {
						echo "<script>
						Swal.fire({
							title: 'Oops!',
							text: 'Formulário de autenticação inválido',
							icon: 'error',
							confirmButtonText: 'Tentar novamente'
							})
							</script>";
							die(); 
						}
					?>