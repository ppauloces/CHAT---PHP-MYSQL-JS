<?php
    include("check.php");

    if (isset($_GET["id"])){
        $user_id = $_GET["id"];

        // Query
        $stmt = $conn->prepare("SELECT `enviado`, `mensagem`, `Image` FROM Chat WHERE (enviado = ? AND recebido = ?) OR (recebido = ? AND enviado = ?) ORDER BY id_chat");
        $stmt->bind_param("iiii", $user_id, $uid, $user_id, $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->num_rows;

        $getUser = $conn->prepare("SELECT id, username, picture FROM user WHERE (id LIKE ?) LIMIT 1");
        $getUser->bind_param("i", $user_id);
        $getUser->execute();
        $user = $getUser->get_result()->fetch_assoc();

        if ($count < 1) {
            echo '<p class="info">Envie a sua primeira mensagem para '.$user["username"].'</p>';
        } else {
            while ($mensagem = $result->fetch_assoc()) {
                if($mensagem["enviado"] == $uid && $mensagem["Image"] != "") {
                    ?>
                    <div class="row sent">
                        <img src="uploads/<?php echo $mensagem["Image"] ?>" />
                    </div>
                    <?php
                } elseif($mensagem["enviado"] == $uid) {
                    ?>
                    <div class="row sent">
                        <p><?php echo $mensagem["mensagem"] ?></p>
                    </div>
                    <?php
                } elseif($mensagem["Image"] != "") {
                    ?>
                    <div class="row recieved">
                        <img src="uploads/<?php echo $mensagem["Image"] ?>" />
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="row recieved">
                        <p><?php echo $mensagem["mensagem"] ?></p>
                    </div>
                    <?php
                }
            }
    
            // Update conversation has opened
            $stmt = $conn->prepare("UPDATE conversas SET `unread` = 'n' WHERE (id_user = ? AND id_other_user = ?)");
            $stmt->bind_param("ii", $uid, $user_id);
            $stmt->execute();
        }

    } else {
        die(header("HTTP/1.0 401 Faltam parametros"));
    }
?>