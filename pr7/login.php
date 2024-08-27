<?php
  header('Content-Type: text/html; charset=UTF-8');
  session_start();
  if (isset($_SESSION['login'])) {
    header('Location: ./');
    exit();
  }

  $error = '';

  if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  }
  else{
    require('connection.php');
    $csrf_tokens = (isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '');

    if(isset($_POST['auth']) && $_SESSION['csrf_token_login'] != $csrf_tokens){
      $error = 'Не соответствие CSRF токена';
    }
    else{
      $login = checkInput($_POST['login']);
      $password = md5(checkInput($_POST['password']));
      try {
        $stmt = $db->prepare("SELECT id FROM users WHERE login = ? and password = ?");
        $stmt->execute([$login, $password]);
        $its = $stmt->rowCount();
        if($its){
          $uid = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['id'];
          $_SESSION['login'] = $_POST['login'];
          $_SESSION['user_id'] = $uid;
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
    <title>Задание 7</title>
</head>
<body>
  <div class="pform pformAuth">
    <form action="" method="post">
      <?php
          $csrf_token = bin2hex(random_bytes(32));
          $_SESSION['csrf_token_login'] = $csrf_token;
      ?>
      <input type="hidden" name='csrf_token' value='<?php echo $csrf_token; ?>'>
      
      <div class="message" style="color: red;"><?php echo $error; ?></div>
      <h3>Авторизация</h3>
        <div>
          <input class="w100" type="text" name="login" placeholder="Логин">
        </div>
        <div>
          <input class="w100" type="text" name="password" placeholder="Пароль">
        </div>
        <button type="submit" name="auth">Войти</button>
    </form>
  </div>
</body>
</html>