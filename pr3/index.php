<?php
  header('Content-Type: text/html; charset=UTF-8');
  $db = '';

  function conn(){
    global $db;
    include('connection.php');
  }
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
  $fio = (isset($_POST['fio']) ? $_POST['fio'] : '');
  $phone = (isset($_POST['phone']) ? $_POST['phone'] : '');
  $email = (isset($_POST['email']) ? $_POST['email'] : '');
  $birthday = (isset($_POST['birthday']) ? date('Y-m-d', strtotime($_POST['birthday'])) : '');
  $gender = (isset($_POST['gender']) ? $_POST['gender'] : '');
  $like_lang = (isset($_POST['like_lang']) ? $_POST['like_lang'] : '');
  $biography = (isset($_POST['biography']) ? $_POST['biography'] : '');
  $oznakomlen = (isset($_POST['oznakomlen']) ? $_POST['oznakomlen'] : '');

  $phone = preg_replace('/\D/', '', $phone);

  $Languages = ($like_lang != '') ? implode(", ", $like_lang) : [];

  val_empty($fio, "имя");
  val_empty($phone, "телефон");
  val_empty($email, "email");
  val_empty($birthday, "дата");
  val_empty($gender, "пол", 1);
  val_empty($like_lang, "языки", 1);
  val_empty($biography, "биографию");
  val_empty($oznakomlen, "ознакомлен");

  if(empty($fio))
  {
    print('Пустое поле фио');
  }
  if(strlen($fio) > 255)
  {
    $errors = 'Длина поля "ФИО" > 255 символов';
  }
  if(count(explode(" ", $fio)) < 2 || !preg_match('/^([а-яa-zё]+-?[а-яa-zё]+)( [а-яa-zё]+-?[а-яa-zё]+){1,2}$/Diu', $fio))
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
  elseif($gender != "m" && $gender != "f")
  {
    $errors = 'Укажите пол';
  }
  elseif(count($like_lang) == 0)
  {
    $errors = 'Укажите языки';
  }
  if ($errors != '') {
    errp($errors);
  }
  
  conn();

  $inQuery = implode(',', array_fill(0, count($like_lang), '?'));
  try 
  {
    $dbLangs = $db->prepare("SELECT id, name FROM languages WHERE name IN ($inQuery)");
    foreach ($like_lang as $key => $value) 
    {
      $dbLangs->bindValue(($key+1), $value);
    }
    $dbLangs->execute();
    $languages = $dbLangs->fetchAll(PDO::FETCH_ASSOC);
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
    $stmt = $db->prepare("INSERT INTO form_data (fio, phone, email, birthday, gender, biography) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fio, $phone, $email, $birthday, $gender, $biography]);
    $fid = $db->lastInsertId();
    $stmt1 = $db->prepare("INSERT INTO form_data_lang (id_form, id_lang) VALUES (?, ?)");
    foreach($languages as $row)
    {
        $stmt1->execute([$fid, $row['id']]);
    }
  }
  catch(PDOException $e)
  {
    print('Error : ' . $e->getMessage());
    exit();
  }
  header('Location: success.php');
  ?>



