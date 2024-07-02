<?php
    header('Content-Type: text/html; charset=UTF-8');
    session_start();

    if (!empty($_SESSION['username']))
    {
        header('Location: form.php');
        exit();
    }

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        require('db.php');
        try {
            $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD,
                [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException $e) {
            echo 'Подключение не удалось: ' . $e->getMessage();
            exit;
        }
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        try
        {
            $stmt = $db->prepare("SELECT user_id FROM Users WHERE username = ? and password = ?");
            $stmt->execute([$username, $password]);
            $its = $stmt->rowCount();
            if($its)
            {
                $uid = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['user_id'];
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['user_id'] = $uid;
                header('Location: form.php');
            }
            else
                $error = 'Неверный логин или пароль. Попробуйте еще.';
        }
        catch(PDOException $e)
        {
            print('Error : ' . $e->getMessage());
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <title>Авторизация</title>
</head>
<body>
    <form action="" method="post" class="form">
        <div class="message" style="color: red;"><?php echo $error; ?></div>
        <h2>Авторизация</h2>
        <div> <input class="input"  type="text" name="username" placeholder="Логин"> </div>
        <div> <input class="input"  type="text" name="password" placeholder="Пароль"> </div>
        <button class="button" type="submit">Войти</button>
    </form>
</body>
</html>