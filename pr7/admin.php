<?php
  require('connection.php');
  
  if (!checkAdmin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
    header('HTTP/1.1 401 Unanthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация</h1>');
    exit();
  }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styleAdmin.css">
    <script src="jquery-3.4.1.min.js"></script>
    <title>Задание 7 (админка)</title>
</head>
<body class="admin">
  <header>
    <div><a href="#data">Информация</a></div>
    <div><a href="#analize">Статистика</a></div>
  </header>

  <table id="data">
    <thead>
      <tr>
        <th>id</th>
        <th>ФИО</th>
        <th>Телефон</th>
        <th>Почта</th>
        <th>День рождения</th>
        <th>Пол</th>
        <th>Биография</th>
        <th>Язык(и) программирования</th>
        <th></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php
        $form_data = $db->query("SELECT * FROM form_data ORDER BY id DESC");
        while($row = $form_data->fetch(PDO::FETCH_ASSOC)){
          echo '<tr data-id='.checkInput($row['id']).'>
                  <td>'.checkInput($row['id']).'</td>
                  <td>'.checkInput($row['fio']).'</td>
                  <td>'.checkInput($row['phone']).'</td>
                  <td>'.checkInput($row['email']).'</td>
                  <td>'.date("d.m.Y", checkInput(strtotime($row['birthday']))).'</td>
                  <td>'.((checkInput($row['gender']) == "m") ? "Мужской" : "Женский").'</td>
                  <td class="wb">'.checkInput($row['biography']).'</td>
                  <td>';
          $dbl = $db->prepare("SELECT * FROM form_data_lang fd
                                LEFT JOIN languages l ON l.id = fd.id_lang
                                WHERE id_form = ?");
          $dbl->execute([$row['id']]);
          while($row1 = $dbl->fetch(PDO::FETCH_ASSOC)){
            echo $row1['name'].'<br>';
          }
          echo '</td>
                <td><a href="./index.php?uid='.$row['user_id'].'" target="_blank">Редактировать</a></td>
                <td><button class="remove">Удалить</button></td>
              </tr>';
        }
      ?>


    </tbody>
  </table>

  <table class="analize" id="analize">
    <thead>
      <tr>
        <th>Язык(и) программирования</th>
        <th>Кол-во пользователей</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $qu = $db->query("SELECT l.id, l.name, COUNT(id_form) as count FROM languages l 
        LEFT JOIN form_data_lang fd ON fd.id_lang = l.id
        GROUP by l.id");
        while($row = $qu->fetch(PDO::FETCH_ASSOC)){
          echo '<tr>
                  <td>'.$row['name'].'</td>
                  <td>'.$row['count'].'</td>
                </tr>';
        }
      ?>
    </tbody>
  </table>

  <script src="./core.js"></script>
</body>
</html>
