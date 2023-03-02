<?php

$board_array = array();
$pdo = null;
$stmt = null;
$error_messages = array();

// DB接続
try {
    $pdo = new PDO('mysql:host=localhost;dbname=tut_board', "root"); 
} catch (PDOException $e) {
    echo $e->getMessage();
}

//フォームを打ち込んだ時
if(!empty($_POST["submitButton"])) {

    //掲示板タイトルのチェック
    if(empty($_POST["title"])) {
        echo "掲示板タイトルを入力してください";
        $error_messages["title"] = "掲示板タイトルを入力してください";
    }

    if(empty($error_messages)) {
        $postDate = date("Y-m-d H:i:s");

        //uuid生成
        $pattern = "xxxx_yxxx_yxxx";
        $character_array = str_split($pattern);
        $uuid = "";

        foreach($character_array as $character) {
            switch($character) {
                case "x":
                    $uuid .= dechex(random_int(0, 15));
                    break;

                case "y":
                    $uuid .= dechex(random_int(8, 11));
                    break;

                default:
                    $uuid .= $character;
            }
        }


        $board_id = "board" . $uuid;

        try {
            $pdo->query("CREATE TABLE $board_id (id INT(11) AUTO_INCREMENT PRIMARY KEY, username VARCHAR(100), comment TEXT, postDate DATETIME);");
            // $stmt = $pdo->prepare("CREATE TABLE $title (title VARCHAR(100), postDate DATETIME);");
            $stmt = $pdo->prepare("INSERT INTO `board_list` (`id`, `title`, `postDate`) VALUES (:id, :title, :postDate);");
            $stmt->bindParam(':id', $uuid, PDO::PARAM_STR);
            $stmt->bindParam(':title', $_POST["title"], PDO::PARAM_STR);
            $stmt->bindParam(':postDate', $postDate, PDO::PARAM_STR);
    
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


}
// INSERT INTO `class-table` (`id`, `class_name`, `grade`, `star`) VALUES ('1', '線形代数Ⅰ', '1', '3');

//DBからコメントデータを取得する
$sql = "SELECT `id`, `title`, `postDate` FROM `board_list` ORDER BY postDate DESC;";
$board_array = $pdo->query($sql);

//DBの接続を閉じる
$pdo = null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TUT Board</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class="title">TUT Board</h1><!-- 見出し -->

    <form method="POST">
        <div>
            <label for="">掲示板タイトル：</label>
            <input type="text" name="title">
            <input type="submit" value="投稿する" name="submitButton">
        </div>
    </form>
    
    <div class="boardWrapper">
        <h2 >掲示板一覧</h2>
        <ul class="board-list">
            <?php foreach($board_array as $board): ?>
                <li>
                    <a href="board.php?board_id=<?php echo "board".$board["id"]?> & board_title=<?php echo $board["title"]?>">
                        <h2><?php echo $board["title"]; ?></h2>
                    </a>
                    <p><?php echo $board["postDate"]; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>