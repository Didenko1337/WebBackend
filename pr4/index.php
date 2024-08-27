<?php

header("Content-Type: text/html, charset=UTF-8");
$error = false;
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $fio = isset($_POST['fio']) ? $_POST['fio'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $birthday = (isset($_POST['birthday']) ? date('Y-m-d', strtotime($_POST['birthday'])) : '');
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $like_lang = isset($_POST['like_lang']) ? $_POST['like_lang'] : [];
    $biography = isset($_POST['biography']) ? $_POST['biography'] : '';
    $oznakomlen = isset($_POST['oznakomlen']) ? $_POST['oznakomlen'] : '';
    
    function check_pole($cook, $str, $flag)
    {
        global $error;
        $res = false;
        $setval = isset($_POST[$cook]) ? $_POST[$cook] : '';
        if($flag)
        {
            setcookie($cook.'_error', $str, time() + 24*60*60);
            $error = true;
            $res = true;
        }
        if($cook == 'like_lang')
        {
            global $like_lang;
            $setval = ($like_lang != '') ? implode(",", $like_lang) : '';
        }
        setcookie($cook.'_value', $setval, time() + 30*24*60*60);
        return $res;
    }
    
    if(!check_pole('fio', 'Это поле пустое', empty($fio)))
        check_pole('fio', 'Неправильный формат: ФИО только кириллица', !preg_match('/^([а-яё]+-?[а-яё]+)( [а-яё]+-?[а-яё]+){1,2}$/Diu', $fio));
    if(!check_pole('phone', 'Это поле пустое', empty($phone)))
    {
        check_pole('phone', 'Неправильный формат, должно быть 11 символов', strlen($phone) != 11);
        check_pole('phone', 'Поле должно содержать только цифры', $phone != preg_replace('/\D/', '', $phone));
    }
    if(!check_pole('email', 'Это поле пустое', empty($email)))
        check_pole('email', 'Неправильный формат: example@mail.ru', !preg_match('/^\w+([.-]?\w+)@\w+([.-]?\w+)(.\w{2,3})+$/', $email));
    if(!check_pole('birthday', 'Это поле пустое', empty($birthday)))
        check_pole('birthday', 'Неправильная дата', strtotime('now') < strtotime($birthday));
    check_pole('gender', "Не выбран пол", empty($gender) || !preg_match('/^(m|f)$/', $gender));
    if(!check_pole('biography', 'Это поле пустое', empty($biography)))
        check_pole('biography', 'Слишком длинное поле', strlen($biography) > 65535);
    check_pole('oznakomlen', 'Не ознакомлены с контрактом', empty($oznakomlen));

    $db;
    function conn(){
      global $db;
      include('connection.php');
    }
    $inQuery = implode(',', array_fill(0, count($like_lang), '?'));

    if(!check_pole('like_lang', 'Не выбран язык', empty($like_lang)))
    {
        conn();
        try
        {
            $dbLangs = $db->prepare("SELECT id, name FROM languages WHERE name IN ($inQuery)");
            foreach ($like_lang as $key => $value)
                $dbLangs->bindValue(($key+1), $value);
            $dbLangs->execute();
            $languages = $dbLangs->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            print('Error : '.$e->getMessage());
            exit();
        }
        check_pole('like_lang', 'Неверно выбраны языки', $dbLangs->rowCount() != count($like_lang));
    }
    
    if (!$error)
    {
        setcookie('fio_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('phone_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('email_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('birthday_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('gender_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('like_lang_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('biography_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('oznakomlen_error', '', time() - 30 * 24 * 60 * 60);
        try
        {
            $stmt = $db->prepare("INSERT INTO form_data (fio, phone, email, birthday, gender, biography) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fio, $phone, $email, $birthday, $gender, $biography]);
            $user_id = $db->lastInsertId();
            $stmt1 = $db->prepare("INSERT INTO form_data_lang (id_form, id_lang) VALUES (?, ?)");
            foreach($languages as $row)
                $stmt1->execute([$user_id, $row['id']]);
        }
        catch(PDOException $e)
        {
            print('Error : ' . $e->getMessage());
            exit();
        }
        setcookie('fio_value', $fio, time() + 24 * 60 * 60 * 365);
        setcookie('phone_value', $phone, time() + 24 * 60 * 60 * 365);
        setcookie('email_value', $email, time() + 24 * 60 * 60 * 365);
        setcookie('birthday_value', $birthday, time() + 24 * 60 * 60 * 365);
        setcookie('gender_value', $gender, time() + 24 * 60 * 60 * 365);
        setcookie('like_lang_value', implode(",", $like_lang), time() + 24 * 60 * 60 * 365);
        setcookie('biography_value', $biography, time() + 24 * 60 * 60 * 365);
        setcookie('oznakomlen_value', $oznakomlen, time() + 24 * 60 * 60 * 365);

        setcookie('save', '1');
    }
    header('Location: index.php');
}
else
{
    $fio = !empty($_COOKIE['fio_error']) ? $_COOKIE['fio_error'] : '';
    $phone = !empty($_COOKIE['phone_error']) ? $_COOKIE['phone_error'] : '';
    $email = !empty($_COOKIE['email_error']) ? $_COOKIE['email_error'] : '';
    $birthday = !empty($_COOKIE['birthday_error']) ? $_COOKIE['birthday_error'] : '';
    $gender = !empty($_COOKIE['gender_error']) ? $_COOKIE['gender_error'] : '';
    $like_lang = !empty($_COOKIE['like_lang_error']) ? $_COOKIE['like_lang_error'] : '';
    $biography = !empty($_COOKIE['biography_error']) ? $_COOKIE['biography_error'] : '';
    $oznakomlen = !empty($_COOKIE['oznakomlen_error']) ? $_COOKIE['oznakomlen_error'] : '';

    $errors = array();
    $messages = array();
    $values = array();

    function check_pole($str, $pole)
    {
        global $errors, $messages, $values;
        $errors[$str] = !empty($pole);
        $messages[$str] = "<div class=\"messageError\">$pole</div>";
        $values[$str] = empty($_COOKIE[$str.'_value']) ? '' : $_COOKIE[$str.'_value'];
        setcookie($str.'_error', '', time() - 30 * 24 * 60 * 60);
        return;
    }

    if (!empty($_COOKIE['save']))
    {
        setcookie('save', '', 100000);
        $messages['success'] = '<div class="message">Данные сохранены</div>';
    }
    else
        $messages['success'] = '';
       
    check_pole('fio', $fio);
    check_pole('phone', $phone);
    check_pole('email', $email);
    check_pole('birthday', $birthday);
    check_pole('gender', $gender);
    check_pole('like_lang', $like_lang);
    check_pole('biography', $biography);
    check_pole('oznakomlen', $oznakomlen);

    $like_langs = explode(',', $values['like_lang']);

    include('form.php');
}
?>