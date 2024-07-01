<?php
  header('Content-Type: text/html; charset=UTF-8');
  
  if ($_SERVER['REQUEST_METHOD'] == 'GET') 
  {
    include('form.php');
    exit();
  }

  function errp($error)
  {
    print("<div class='messageError'>$error</div>");
    exit();
  }

  function val_empty($val, $name, $o = 0)
  {
    if(empty($val))
    {
      if($o == 0)
      {
        errp("Заполните поле $name.<br/>");
      }
      if($o == 1)
      {
        errp("Выберите $name.<br/>");
      }
      exit();
    }
  }

  $errors = '';
  $fullname = (isset($_POST['fullname']) ? $_POST['fullname'] : '');
  $phone = (isset($_POST['phone']) ? $_POST['phone'] : '');
  $email = (isset($_POST['email']) ? $_POST['email'] : '');
  $birthday = (isset($_POST['birthday']) ? date('Y-m-d', strtotime($_POST['birthday'])) : '');
  $gender = (isset($_POST['gender']) ? $_POST['gender'] : '');
  $language = (isset($_POST['language']) ? $_POST['language'] : '');
  $biography = (isset($_POST['biography']) ? $_POST['biography'] : '');
  $accept = (isset($_POST['accept']) ? $_POST['accept'] : '');

  $phone = preg_replace('/\D/', '', $phone);

  $Languages = ($language != '') ? implode(", ", $language) : [];

  val_empty($fullname, "имя");
  val_empty($phone, "телефон");
  val_empty($email, "email");
  val_empty($birthday, "дата");
  val_empty($gender, "пол", 1);
  val_empty($language, "языки", 1);
  val_empty($biography, "биографию");
  val_empty($accept, "ознакомлен");

  if(empty($fullname))
  {
    print('Пустое поле фио');
  }
  if(strlen($fullname) > 150)
  {
    $errors = 'Длина поля "ФИО" > 150 символов';
  }
  if(count(explode(" ", $fullname)) < 2 || !preg_match('/^([а-яa-zё]+-?[а-яa-zё]+)( [а-яa-zё]+-?[а-яa-zё]+){1,2}$/Diu', $fullname))
  {
    $errors = 'Неверный формат ФИО';
  }
  elseif(strlen($phone) != 11 || !preg_match('/^\d{11}$/', $phone))
  {
    $errors = 'Неверное значение поля "Телефон"';
  }
  elseif(!preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email))
  {
    $errors = 'Неверное значение поля "email"';
  }
  elseif($gender != "male" && $gender != "female")
  {
    $errors = 'Укажите пол';
  }
  elseif(count($language) == 0)
  {
    $errors = 'Укажите языки';
  }
  if ($errors != '') {
    errp($errors);
  }

  include('db.php');
  try 
  {
      $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD,
          [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  } catch (PDOException $e) 
  {
      echo 'Подключение не удалось: ' . $e->getMessage();
      exit;
  }

  $inQuery = implode(',', array_fill(0, count($language), '?'));
  try 
  {
    $dbLangs = $db->prepare("SELECT id, name FROM Languages WHERE name IN ($inQuery)");
    foreach ($language as $key => $value) 
    {
      $dbLangs->bindValue(($key+1), $value);
    }
    $dbLangs->execute();
    $Languages = $dbLangs->fetchAll(PDO::FETCH_ASSOC);
  }
  catch(PDOException $e)
  {
    print('Error : ' . $e->getMessage());
    exit();
  } 

  if ($errors != '') 
  {
    errp($errors);
  }

  try 
  {
    $stmt = $db->prepare("INSERT INTO Users (fullname, phone, email, birthday, gender, biography) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fullname, $phone, $email, $birthday, $gender, $biography]);
    $user_id = $db->lastInsertId();
    $stmt1 = $db->prepare("INSERT INTO UserLanguages (user_id, lang_id) VALUES (?, ?)");
    foreach($Languages as $row)
    {
        $stmt1->execute([$user_id, $row['id']]);
    }
  }
  catch(PDOException $e)
  {
    print('Error : ' . $e->getMessage());
    exit();
  }
  header('Location: success.php');
  ?>