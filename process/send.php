<?php
    include("check.php");

    if(isset($_POST["message"]) && isset($_POST["id"])) {

        // Normalization
        $user_id = $_POST["id"];
        $message = $_POST["message"];
        $image = "";

        if($_FILES['image']['error'] <= 0) {
            $image = $username."_MESSAGE_".rand(999, 999999).$_FILES['image']['name'];
            $imagetemp = $_FILES['image']['tmp_name'];
            $imagePath = "../uploads/";
            if (is_uploaded_file($imagetemp)) {
                if (move_uploaded_file($imagetemp, $imagePath . $image)) {
                    echo "OK";
                } else {
                    die(header("HTTP/1.0 401 Erro ao guardar imagem"));
                }
            } else {
                die(header("HTTP/1.0 401 Erro ao carregar imagem"));
            }
        } elseif ($user_id == "" || $message == "") {
            die(header("HTTP/1.0 401 Escreva uma mensagem"));
        }

        // Check if conversation exists
        $checkConversation = $conn->prepare("SELECT id FROM `conversas` WHERE (id_user = ? AND id_other_user = ?)");
        $checkConversation->bind_param("ii", $uid, $user_id);
        $checkConversation->execute();
        $count = $checkConversation->get_result()->num_rows;
        
        if ($count < 1) {
            // Create conversation user side
            $createChat = $conn->prepare("INSERT INTO `conversas` (`id_user`, `id_other_user`, `Unread`, `create`) VALUES (?, ?, 'n', now())");
            $createChat->bind_param("ii", $uid, $user_id);
            $createChat->execute();

            // Create conversation other user side
            $createChat2 = $conn->prepare("INSERT INTO `conversas` (`id_user`, `id_other_user`, `Unread`, `create`) VALUES (?, ?, 'y', now())");
            $createChat2->bind_param("ii", $user_id, $uid);
            $createChat2->execute();
        } else {
            $update = $conn->prepare("UPDATE `conversas` SET Unread = 'y' WHERE (id_user = ? AND id_other_user = ?)");
            $update->bind_param("ii", $uid, $user_id);
            $update->execute();
        }
        
        // Queries for create and collection
        $stmt = $conn->prepare("INSERT INTO Chat (`Sender`, `Reciever`, `Message`, `Image`, `create`) VALUES (?, ?, ?, ?, now())");
        $stmt->bind_param("iiss", $uid, $user_id, $message, $image);
        $stmt->execute();

        if (!$stmt || !$update) {
            die(header("HTTP/1.0 401 Ocorreu um erro ao enviar a sua mensagem"));
        }
    } else {
        die(header("HTTP/1.0 401 Faltam parametros"));
    }
?>