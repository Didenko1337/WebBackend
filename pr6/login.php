<?php
  header('Content-Type: text/html; charset=UTF-8');
  session_start();
  if (!empty($_SESSION['login'])) {
    header('Location: ./');
    exit();
  }

  $error = '';
  if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    require('connection.php');
    $login = $_POST['login'];
    $password = md5($_POST['password']);
    try {
      $user = $db->prepare("SELECT id FROM users WHERE login = ? and password = ?");
      $user->execute([$login, $password]);
      if($user->rowCount()){
        $user_ids = $user->fetchAll(PDO::FETCH_ASSOC)[0]['id'];
        $_SESSION['login'] = $_POST['login'];
        $_SESSION['user_id'] = $user_ids;
        header('Location: ./');
      }
      else{
        $error = 'Неверный логин или пароль';
      }
    }
    catch(PDOException $e){
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
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="jquery-3.4.1.min.js"></script>
    <title>Задание 6</title>
</head>
<body>
  <div class="pform pformAuth">
    <form action="" method="post">
      <div class="message" style="color: red;"><?php echo $error; ?></div>
      <h3>Авторизация</h3>
        <div>
          <input class="w100" type="text" name="login" placeholder="Логин">
        </div>
        <div>
          <input class="w100" type="text" name="password" placeholder="Пароль">
        </div>
        <button type="submit">Войти</button>
    </form>
  </div>
</body>
</html>