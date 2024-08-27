<?php
  header('Content-Type: text/html; charset=UTF-8');
  session_start();
  if(strpos($_SERVER['REQUEST_URI'], 'index.php') === false){
    header('Location: index.php');
    exit();
  }

  $login_user = !empty($_SESSION['login']);
  $login_admin = !empty($_SERVER['PHP_AUTH_USER']);
  $user_ids = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
  $get_user_ids = isset($_GET['uid']) ? strip_tags($_GET['uid']) : '';

  if($login_admin && preg_match('/^[0-9]+$/', $get_user_ids)){
    $login_user = true;
    $user_ids = $get_user_ids;
  }
  
  function delete_cookie($cook, $vals = 0){
    setcookie($cook.'_error', '', 100000);
    if($vals) setcookie($cook.'_value', '', 100000);
  }

  $db;
  function conn(){
    global $db;
    include('connection.php');
  }

  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if(($login_admin && !empty($get_user_ids)) || !$login_admin){
      $cookie_admin = (!empty($_COOKIE['admin_value']) ? $_COOKIE['admin_value'] : '');
      if($cookie_admin == '1'){
        delete_cookie('fio', 1);
        delete_cookie('phone', 1);
        delete_cookie('email', 1);
        delete_cookie('birthday', 1);
        delete_cookie('gender', 1);
        delete_cookie('like_lang', 1);
        delete_cookie('biography', 1);
        delete_cookie('oznakomlen', 1);
        delete_cookie('admin', 1);
      }
    }

    $fio = (!empty($_COOKIE['fio_error']) ? $_COOKIE['fio_error'] : '');
    $phone = (!empty($_COOKIE['phone_error']) ? $_COOKIE['phone_error'] : '');
    $email = (!empty($_COOKIE['email_error']) ? $_COOKIE['email_error'] : '');
    $birthday = (!empty($_COOKIE['birthday_error']) ? $_COOKIE['birthday_error'] : '');
    $gender = (!empty($_COOKIE['gender_error']) ? $_COOKIE['gender_error'] : '');
    $like_lang = (!empty($_COOKIE['like_lang_error']) ? $_COOKIE['like_lang_error'] : '');
    $biography = (!empty($_COOKIE['biography_error']) ? $_COOKIE['biography_error'] : '');
    $oznakomlen = (!empty($_COOKIE['oznakomlen_error']) ? $_COOKIE['oznakomlen_error'] : '');

    $errors = array();
    $messages = array();
    $values = array();
    $error = true;
    
    function setValue($enName, $param){
      global $values;
      $values[$enName] = empty($param) ? '' : strip_tags($param);
    }

    function check_input_value($enName, $val){
      global $errors, $messages, $error, $values;
      if($error) 
        $error = empty($_COOKIE[$enName.'_error']);

      $errors[$enName] = !empty($_COOKIE[$enName.'_error']);
      $messages[$enName] = "<div class='messageError'>$val</div>";
      $values[$enName] = empty($_COOKIE[$enName.'_value']) ? '' : strip_tags($_COOKIE[$enName.'_value']);
      delete_cookie($enName);
      return;
    }

    if (!empty($_COOKIE['save'])) {
      setcookie('login', '', 100000);
      setcookie('password', '', 100000);
      setcookie('save', '', 100000);
      $messages['success'] = 'Спасибо, результаты сохранены.';
      if (!empty($_COOKIE['password'])) {
        $messages['info'] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
          и паролем <strong>%s</strong> для изменения данных.',
          strip_tags($_COOKIE['login']),
          strip_tags($_COOKIE['password']));
      }
    }
    
    check_input_value('fio', $fio);
    check_input_value('phone', $phone);
    check_input_value('email', $email);
    check_input_value('birthday', $birthday);
    check_input_value('gender', $gender);
    check_input_value('like_lang', $like_lang);
    check_input_value('biography', $biography);
    check_input_value('oznakomlen', $oznakomlen);
    
    $like_lang_array = explode(',', $values['like_lang']);
    
    if ($error && $login_user) {
      conn();
      try {
        $dbFD = $db->prepare("SELECT * FROM form_data WHERE user_id = ?");
        $dbFD->execute([$user_ids]);
        $fet = $dbFD->fetchAll(PDO::FETCH_ASSOC)[0];
        $form_id = $fet['id'];
        $_SESSION['form_id'] = $form_id;
        $dbL = $db->prepare("SELECT l.name FROM form_data_lang f
                              LEFT JOIN languages l ON l.id = f.id_lang
                              WHERE f.id_form = ?");
        $dbL->execute([$form_id]);
        $like_lang_array = [];
        foreach($dbL->fetchAll(PDO::FETCH_ASSOC) as $item){
          $like_lang_array[] = $item['name'];
        }
        setValue('fio', $fet['fio']);
        setValue('phone', $fet['phone']);
        setValue('email', $fet['email']);
        setValue('birthday', date("d.m.Y", strtotime($fet['birthday'])));
        setValue('gender', $fet['gender']);
        setValue('like_lang', $like_lang);
        setValue('biography', $fet['biography']);
        setValue('oznakomlen', $fet['oznakomlen']);
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
    }
    
    include('form.php');
  }
  else {
    $fio = (!empty($_POST['fio']) ? $_POST['fio'] : '');
    $phone = (!empty($_POST['phone']) ? $_POST['phone'] : '');
    $email = (!empty($_POST['email']) ? $_POST['email'] : '');
    $birthday = (!empty($_POST['birthday']) ? $_POST['birthday'] : '');
    if ($birthday) {
      $birthday = date('Y-m-d', strtotime($birthday));
    }
    $gender = (!empty($_POST['gender']) ? $_POST['gender'] : '');
    $like_lang = (!empty($_POST['like_lang']) ? $_POST['like_lang'] : '');
    $biography = (!empty($_POST['biography']) ? $_POST['biography'] : '');
    $oznakomlen = (!empty($_POST['oznakomlen']) ? $_POST['oznakomlen'] : '');

    if(isset($_POST['logout_form'])){
      if($login_admin && empty($_SESSION['login'])){
        header('Location: admin.php');
      }
      else{
        delete_cookie('fio', 1);
        delete_cookie('phone', 1);
        delete_cookie('email', 1);
        delete_cookie('birthday', 1);
        delete_cookie('gender', 1);
        delete_cookie('like_lang', 1);
        delete_cookie('biography', 1);
        delete_cookie('oznakomlen', 1);
        session_destroy();
        if($get_user_ids != NULL){
          header("Location: index.php?uid=$user_ids");
        }
        else{
          header('Location: index.php');
        }
      }
      exit();
    }

    $phone1 = preg_replace('/\D/', '', $phone);

    function check_input_value($cook, $comment, $usl){
      global $error;
      $res = false;
      $setValue = $_POST[$cook];
      if ($usl) {
        setcookie($cook.'_error', $comment, time() + 24 * 60 * 60);
        $error = true;
        $res = true;
      }
      
      if($cook == 'like_lang'){
        global $like_lang;
        $setValue = ($like_lang != '') ? implode(",", $like_lang) : '';
      }
      
      setcookie($cook.'_value', $setValue, time() + 30 * 24 * 60 * 60);
      return $res;
    }
    
    if(!check_input_value('fio', 'Заполните поле', empty($fio))){
      if(!check_input_value('fio', 'Длина поля > 255 символов', strlen($fio) > 255)){
        check_input_value('fio', 'Поле не соответствует требованиям: <i>Фамилия Имя (Отчество)</i>, кириллицей', !preg_match('/^([а-яёА-ЯЁ]+-?[а-яёА-ЯЁ]+)( [а-яёА-ЯЁ]+-?[а-яёА-ЯЁ]+){1,2}$/Diu', $fio));
      }
    }
    if(!check_input_value('phone', 'Заполните поле', empty($phone))){
      if(!check_input_value('phone', 'Длина поля некорректна', strlen($phone) != 11)){
        check_input_value('phone', 'Поле должен содержать только цифры', ($phone != $phone1));
      }
    }
    if(!check_input_value('email', 'Заполните поле', empty($email))){
      if(!check_input_value('email', 'Длина поля > 255 символов', strlen($email) > 255)){
        check_input_value('email', 'Поле не соответствует требованию example@mail.ru', !preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email));
      }
    }
    if(!check_input_value('birthday', "Выберите дату рождения", empty($birthday))){
      check_input_value('birthday', "Неверно введена дата рождения, дата больше настоящей", (strtotime("now") < strtotime($birthday)));
    }
    check_input_value('gender', "Выберите пол", (empty($gender) || !preg_match('/^(m|f)$/', $gender)));
    if(!check_input_value('like_lang', "Выберите хотя бы один язык", empty($like_lang))){
      conn();
      try {
        $inQuery = implode(',', array_fill(0, count($like_lang), '?'));
        $dbLangs = $db->prepare("SELECT id, name FROM languages WHERE name IN ($inQuery)");
        foreach ($like_lang as $key => $value) {
          $dbLangs->bindValue(($key+1), $value);
        }
        $dbLangs->execute();
        $languages = $dbLangs->fetchAll(PDO::FETCH_ASSOC);
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
      
      check_input_value('like_lang', 'Неверно выбраны языки', $dbLangs->rowCount() != count($like_lang));
    }
    if(!check_input_value('biography', 'Заполните поле', empty($biography))){
      check_input_value('biography', 'Длина текста > 65 535 символов', strlen($biography) > 65535);
    }
    check_input_value('oznakomlen', "Ознакомьтесь с контрактом", empty($oznakomlen));
    
    if ($error){
      session_destroy();
      if($get_user_ids != NULL){
        header("Location: index.php?uid=$user_ids");
      }
      else{
        header('Location: index.php');
      }
      exit();
    }
    else {
      delete_cookie('fio');
      delete_cookie('phone');
      delete_cookie('email');
      delete_cookie('birthday');
      delete_cookie('gender');
      delete_cookie('like_lang');
      delete_cookie('biography');
      delete_cookie('oznakomlen');
    }
    
    // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
    if ($login_user) {
      $stmt = $db->prepare("UPDATE form_data SET fio = ?, phone = ?, email = ?, birthday = ?, gender = ?, biography = ? WHERE user_id = ?");
      $stmt->execute([$fio, $phone, $email, $birthday, $gender, $biography, $user_ids]);

      $stmt = $db->prepare("DELETE FROM form_data_lang WHERE id_form = ?");
      $stmt->execute([$_SESSION['form_id']]);

      $stmt1 = $db->prepare("INSERT INTO form_data_lang (id_form, id_lang) VALUES (?, ?)");
      foreach($languages as $row){
          $stmt1->execute([$_SESSION['form_id'], $row['id']]);
      }
      if($login_admin) 
        setcookie('admin_value', '1', time() + 30 * 24 * 60 * 60);
    }
    else {
      $login = substr(uniqid(), 0, 4).rand(10, 100);
      $password = rand(100, 1000).substr(uniqid(), 4, 10);
      setcookie('login', $login);
      setcookie('password', $password);

      $mpassword = md5($password);
      try {
        $stmt = $db->prepare("INSERT INTO users (login, password) VALUES (?, ?)");
        $stmt->execute([$login, $mpassword]);
        $user_id = $db->lastInsertId();

        $stmt = $db->prepare("INSERT INTO form_data (user_id, fio, phone, email, birthday, gender, biography) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $fio, $phone, $email, $birthday, $gender, $biography]);
        $fid = $db->lastInsertId();

        $stmt1 = $db->prepare("INSERT INTO form_data_lang (id_form, id_lang) VALUES (?, ?)");
        foreach($languages as $row){
            $stmt1->execute([$fid, $row['id']]);
        }
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
      setcookie('fio_value', $fio, time() + 24 * 60 * 60 * 365);
      setcookie('phone_value', $phone, time() + 24 * 60 * 60 * 365);
      setcookie('email_value', $email, time() + 24 * 60 * 60 * 365);
      setcookie('birthday_value', $birthday, time() + 24 * 60 * 60 * 365);
      setcookie('gender_value', $gender, time() + 24 * 60 * 60 * 365);
      setcookie('like_value', $like, time() + 24 * 60 * 60 * 365);
      setcookie('biography_value', $biography, time() + 24 * 60 * 60 * 365);
      setcookie('oznakomlen_value', $oznakomlen, time() + 24 * 60 * 60 * 365);
    }

    setcookie('save', '1');

    session_destroy();
    if($get_user_ids != NULL){
      header("Location: index.php?uid=$user_ids");
    }
    else{
      header('Location: index.php');
    }
  }
?>