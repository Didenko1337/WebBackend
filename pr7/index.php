<?php
  header('Content-Type: text/html; charset=UTF-8');
  if(strpos($_SERVER['REQUEST_URI'], 'index.php') === false){
    header('Location: index.php');
    exit();
  }

  include('connection.php');

  $adminLog = checkAdmin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
  $userLog = isset($_SESSION['login']);
  $getUid = isset($_GET['uid']) ? $_GET['uid'] : '';
  $uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

  if($adminLog)
    if(preg_match('/^[0-9]+$/', $getUid)){
      $uid = $getUid;
      $userLog = true;
    }
  
  function delete_cookie($cook, $vals = 0){
    setcookie($cook.'_error', '', 100000);
    if($vals == 1){
      setcookie($cook.'_value', '', 100000);
    }
    if($vals == 2){
      setcookie($cook, '', 100000);
    }
  }

  function set_cookie($cook, $val, $dop_time = 1){
    setcookie($cook, $val, time() + 24 * 60 * 60 * $dop_time);
  }

  function delete_cookie_all($p = 0){
    delete_cookie('fio', $p);
    delete_cookie('phone', $p);
    delete_cookie('email', $p);
    delete_cookie('birthday', $p);
    delete_cookie('gender', $p);
    delete_cookie('like_lang', $p);
    delete_cookie('biography', $p);
    delete_cookie('oznakomlen', $p);
  }

  function user_exit(){
    delete_cookie_all(1);
    session_destroy();
    header('Location: index.php');
    exit();
  }

  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if(($adminLog && isset($getUid)) || !$adminLog){
      $cookAdmin = (isset($_COOKIE['admin_value']) ? $_COOKIE['admin_value'] : '');
      if($cookAdmin == '1'){
        delete_cookie_all(1);
        delete_cookie('admin', 1);
      }
    }

    $csrf_error = (isset($_COOKIE['csrf_error']) ? $_COOKIE['csrf_error'] : '');
    $fio = (isset($_COOKIE['fio_error']) ? $_COOKIE['fio_error'] : '');
    $phone = (isset($_COOKIE['phone_error']) ? $_COOKIE['phone_error'] : '');
    $email = (isset($_COOKIE['email_error']) ? $_COOKIE['email_error'] : '');
    $birthday = (isset($_COOKIE['birthday_error']) ? $_COOKIE['birthday_error'] : '');
    $gender = (isset($_COOKIE['gender_error']) ? $_COOKIE['gender_error'] : '');
    $like_lang = (isset($_COOKIE['like_lang_error']) ? $_COOKIE['like_lang_error'] : '');
    $biography = (isset($_COOKIE['biography_error']) ? $_COOKIE['biography_error'] : '');
    $oznakomlen = (isset($_COOKIE['oznakomlen_error']) ? $_COOKIE['oznakomlen_error'] : '');

    $messages = array();
    $values = array();
    $errors = array();
    $error = true;
    
    function setValue($enName, $param){
      global $values;
      $values[$enName] = empty($param) ? '' : $param;
    }

    function check_input_value($enName, $val){
      global $errors, $messages, $error, $values;
      if($error) 
        $error = empty($_COOKIE[$enName.'_error']);

      $messages[$enName] = "<div class='messageError'>$val</div>";
      $values[$enName] = empty($_COOKIE[$enName.'_value']) ? '' : $_COOKIE[$enName.'_value'];
      $errors[$enName] = isset($_COOKIE[$enName.'_error']);
      delete_cookie($enName);
      return;
    }

    if (isset($_COOKIE['csrf_error'])) {
      $messages['error'] = 'Не соответствие CSRF токена';
      delete_cookie('csrf');
    }
    if (isset($_COOKIE['save'])) {
      delete_cookie('login', 2);
      delete_cookie('password', 2);
      delete_cookie('save', 2);
      $messages['success'] = (!$userLog) ? 'Спасибо, данные сохранены' : 'Данные изменены';
      if (isset($_COOKIE['password'])) {
        $messages['info'] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
          и паролем <strong>%s</strong> для изменения данных.',
          $_COOKIE['login'],
          $_COOKIE['password']);
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
    
    if ($error && $userLog) {
      try {
        $dbFD = $db->prepare("SELECT * FROM form_data WHERE user_id = ?");
        $dbFD->execute([$uid]);
        if($dbFD->rowCount() > 0){
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
        else{
          unset($_SESSION['user_id']);
          $userLog = false;
          unset($uid);
          $messages['error'] = 'Пользователь был удален';
          user_exit();
        }
      }
      catch(PDOException $e){
        print('Error1 : ' . $e->getMessage());
        exit();
      }
    }
    
    include('form.php');
  }
  else {
    $csrf_tokens = (isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '');
    $fio = (isset($_POST['fio']) ? $_POST['fio'] : '');
    $phone = (isset($_POST['phone']) ? $_POST['phone'] : '');
    $email = (isset($_POST['email']) ? $_POST['email'] : '');
    $birthday = (isset($_POST['birthday']) ? $_POST['birthday'] : '');
    if ($birthday) {
      $birthday = date('Y-m-d', strtotime($birthday));
    }
    $gender = (isset($_POST['gender']) ? $_POST['gender'] : '');
    $like_lang = (isset($_POST['like_lang']) ? $_POST['like_lang'] : '');
    $biography = (isset($_POST['biography']) ? $_POST['biography'] : '');
    $oznakomlen = (isset($_POST['oznakomlen']) ? $_POST['oznakomlen'] : '');

    if($_SESSION['csrf_token'] != $csrf_tokens){
      set_cookie('csrf_error', '1');
      if($getUid != NULL){
        header('Location: index.php?uid='.$uid);
      }
      else{
        header('Location: index.php');
      }
      exit();
    }
    
    if(isset($_POST['logout_form'])){
      if($adminLog && empty($_SESSION['login']) && !isset($_POST['editBut'])){
        header('Location: admin.php');
      }
      else{
        user_exit();
      }
      exit();
    }

    $phone1 = preg_replace('/\D/', '', $phone);

    function check_input_value($cook, $comment, $usl){
      global $error;
      $res = false;
      $setValue = $_POST[$cook];
      if ($usl) {
        set_cookie($cook.'_error', $comment);
        $error = true;
        $res = true;
      }
      
      if($cook == 'like_lang'){
        global $like_lang;
        $setValue = ($like_lang != '') ? implode(",", $like_lang) : '';
      }
      
      set_cookie($cook.'_value', $setValue, 60);
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
        print('Error2 : ' . $e->getMessage());
        exit();
      }
      
      check_input_value('like_lang', 'Неверно выбраны языки', $dbLangs->rowCount() != count($like_lang));
    }
    if(!check_input_value('biography', 'Заполните поле', empty($biography))){
      check_input_value('biography', 'Длина текста > 65 535 символов', strlen($biography) > 65535);
    }
    check_input_value('oznakomlen', "Ознакомьтесь с контрактом", empty($oznakomlen));
    
    if ($error) {
      echo $error;
      if($adminLog && empty($_SESSION['login']) && !isset($_POST['editBut'])){
        header('Location: admin.php');
      }
      else{
        if($getUid != NULL){
          header("Location: index.php?uid=$uid");
        }
        else{
          header('Location: index.php');
        }
        // user_exit();
      }
      exit();
    }
    else {
      delete_cookie_all();
    }
    
    if ($userLog) {
      $stmt = $db->prepare("UPDATE form_data SET fio = ?, phone = ?, email = ?, birthday = ?, gender = ?, biography = ? WHERE user_id = ?");
      $stmt->execute([$fio, $phone, $email, $birthday, $gender, $biography, $uid]);

      $stmt = $db->prepare("DELETE FROM form_data_lang WHERE id_form = ?");
      $stmt->execute([$_SESSION['form_id']]);

      $stmt1 = $db->prepare("INSERT INTO form_data_lang (id_form, id_lang) VALUES (?, ?)");
      foreach($languages as $row){
          $stmt1->execute([$_SESSION['form_id'], $row['id']]);
      }
      if($adminLog) 
        set_cookie('admin_value', '1', 60);
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
        print('Error3 : ' . $e->getMessage());
        exit();
      }
      set_cookie('fio_value', $fio, 365);
      set_cookie('phone_value', $phone, 365);
      set_cookie('email_value', $email, 365);
      set_cookie('birthday_value', $birthday, 365);
      set_cookie('gender_value', $gender, 365);
      set_cookie('like_value', $like, 365);
      set_cookie('biography_value', $biography, 365);
      set_cookie('oznakomlen_value', $oznakomlen, 365);
    }
    setcookie('save', '1');
    if($adminLog && empty($_SESSION['login']) && !isset($_POST['editBut'])){
      header('Location: admin.php');
    }
    else{
      if($getUid != NULL){
        header("Location: index.php?uid=$uid");
      }
      else{
        header('Location: index.php');
      }
      // user_exit();
    }
    exit();
  }
?>