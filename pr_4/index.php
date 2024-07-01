<?php

header("Content-Type: text/html, charset=UTF-8");
$error = false;
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $birthday = (isset($_POST['birthday']) ? date('Y-m-d', strtotime($_POST['birthday'])) : '');
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $language = isset($_POST['language']) ? $_POST['language'] : [];
    $biography = isset($_POST['biography']) ? $_POST['biography'] : '';
    $accept = isset($_POST['accept']) ? $_POST['accept'] : '';
    
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
        if($cook == 'language')
        {
            global $language;
            $setval = ($language != '') ? implode(",", $language) : '';
        }
        setcookie($cook.'_value', $setval, time() + 30*24*60*60);
        return $res;
    }
    
    if(!check_pole('fullname', 'Это поле пустое', empty($fullname)))
        check_pole('fullname', 'Неправильный формат: Имя Фамилия (Отчество), только кириллица', !preg_match('/^([а-яё]+-?[а-яё]+)( [а-яё]+-?[а-яё]+){1,2}$/Diu', $fullname));
    if(!check_pole('phone', 'Это поле пустое', empty($phone)))
    {
        check_pole('phone', 'Неправильный формат, должно быть 11 символов', strlen($phone) != 11);
        check_pole('phone', 'Поле должно содержать только цифры', $phone != preg_replace('/\D/', '', $phone));
    }
    if(!check_pole('email', 'Это поле пустое', empty($email)))
        check_pole('email', 'Неправильный формат: example@mail.ru', !preg_match('/^\w+([.-]?\w+)@\w+([.-]?\w+)(.\w{2,3})+$/', $email));
    if(!check_pole('birthday', 'Это поле пустое', empty($birthday)))
        check_pole('birthday', 'Неправильная дата', strtotime('now') < strtotime($birthday));
    check_pole('gender', "Не выбран пол", empty($gender) || !preg_match('/^(male|female)$/', $gender));
    if(!check_pole('biography', 'Это поле пустое', empty($biography)))
        check_pole('biography', 'Слишком длинное поле, максимум символов - 65535', strlen($biography) > 65535);
    check_pole('accept', 'Не ознакомлены с контрактом', empty($accept));

    include('db.php');
    try {
        $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD,
            [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (PDOException $e) {
        echo 'Подключение не удалось: ' . $e->getMessage();
        exit;
    }
    $inQuery = implode(',', array_fill(0, count($language), '?'));

    if(!check_pole('language', 'Не выбран язык', empty($language)))
    {
        try
        {
            $dbLangs = $db->prepare("SELECT id, name FROM Languages WHERE name IN ($inQuery)");
            foreach ($language as $key => $value)
                $dbLangs->bindValue(($key+1), $value);
            $dbLangs->execute();
            $Languages = $dbLangs->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            print('Error : '.$e->getMessage());
            exit();
        }
        check_pole('language', 'Неверно выбраны языки', $dbLangs->rowCount() != count($language));
    }
    
    if (!$error)
    {
        setcookie('fullname_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('phone_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('email_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('birthday_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('gender_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('language_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('biography_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('accept_error', '', time() - 30 * 24 * 60 * 60);
        try
        {
            $stmt = $db->prepare("INSERT INTO Users (fullname, phone, email, birthday, gender, biography) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fullname, $phone, $email, $birthday, $gender, $biography]);
            $user_id = $db->lastInsertId();
            $stmt1 = $db->prepare("INSERT INTO UserLanguages (user_id, lang_id) VALUES (?, ?)");
            foreach($Languages as $row)
                $stmt1->execute([$user_id, $row['id']]);
        }
        catch(PDOException $e)
        {
            print('Error : ' . $e->getMessage());
            exit();
        }
        setcookie('fullname_value', $fullname, time() + 24 * 60 * 60 * 365);
        setcookie('phone_value', $phone, time() + 24 * 60 * 60 * 365);
        setcookie('email_value', $email, time() + 24 * 60 * 60 * 365);
        setcookie('birthday_value', $birthday, time() + 24 * 60 * 60 * 365);
        setcookie('gender_value', $gender, time() + 24 * 60 * 60 * 365);
        setcookie('language_value', implode(",", $language), time() + 24 * 60 * 60 * 365);
        setcookie('biography_value', $biography, time() + 24 * 60 * 60 * 365);
        setcookie('accept_value', $accept, time() + 24 * 60 * 60 * 365);

        setcookie('save', '1');
    }
    header('Location: index.php');
}
else
{
    $fullname = !empty($_COOKIE['fullname_error']) ? $_COOKIE['fullname_error'] : '';
    $phone = !empty($_COOKIE['phone_error']) ? $_COOKIE['phone_error'] : '';
    $email = !empty($_COOKIE['email_error']) ? $_COOKIE['email_error'] : '';
    $birthday = !empty($_COOKIE['birthday_error']) ? $_COOKIE['birthday_error'] : '';
    $gender = !empty($_COOKIE['gender_error']) ? $_COOKIE['gender_error'] : '';
    $language = !empty($_COOKIE['language_error']) ? $_COOKIE['language_error'] : '';
    $biography = !empty($_COOKIE['biography_error']) ? $_COOKIE['biography_error'] : '';
    $accept = !empty($_COOKIE['accept_error']) ? $_COOKIE['accept_error'] : '';

    $errors = array();
    $messages = array();
    $values = array();

    function check_pole($str, $pole)
    {
        global $errors, $messages, $values;
        $errors[$str] = !empty($pole);
        $messages[$str] = "<div class=\"error\">$pole</div>";
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
       
    check_pole('fullname', $fullname);
    check_pole('phone', $phone);
    check_pole('email', $email);
    check_pole('birthday', $birthday);
    check_pole('gender', $gender);
    check_pole('language', $language);
    check_pole('biography', $biography);
    check_pole('accept', $accept);

    $Languages = explode(',', $values['language']);

    include('form.php');
}
?>