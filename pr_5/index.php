<?php

$db;
include('db.php');
    try {
        $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD,
            [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (PDOException $e) {
        echo 'Подключение не удалось: ' . $e->getMessage();
        exit;
    }
header("Content-Type: text/html; charset=UTF-8");
session_start();

$error = false;
$user = !empty($_SESSION['username']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $birthday = isset($_POST['birthday']) ? $_POST['birthday'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $language = isset($_POST['language']) ? $_POST['language'] : [];
    $biography = isset($_POST['biography']) ? $_POST['biography'] : '';
    $accept = isset($_POST['accept']) ? $_POST['accept'] : '';
    
    if (isset($_POST['logout_form'])) {
        setcookie('fio_value', '', time() - 30 * 24 * 60 * 60);
        setcookie('number_value', '', time() - 30 * 24 * 60 * 60);
        setcookie('email_value', '', time() - 30 * 24 * 60 * 60);
        setcookie('date_value', '', time() - 30 * 24 * 60 * 60);
        setcookie('radio_value', '', time() - 30 * 24 * 60 * 60);
        setcookie('language_value', '', time() - 30 * 24 * 60 * 60);
        setcookie('bio_value', '', time() - 30 * 24 * 60 * 60);
        setcookie('check_value', '', time() - 30 * 24 * 60 * 60);
        session_destroy();
        header('Location: ./');
        exit();
    }
    function proverka($cook, $str, $flag)
    {
        global $error;
        $res = false;
        $setval = isset($_POST[$cook]) ? $_POST[$cook] : '';
        if ($flag) {
            setcookie($cook . '_error', $str, time() + 24 * 60 * 60);
            $error = true;
            $res = true;
        }
        if ($cook == 'language') {
            global $language;
            $setval = ($language != '') ? implode(",", $language) : '';
        }
        setcookie($cook . '_value', $setval, time() + 30 * 24 * 60 * 60);
        return $res;
    }

    if (!proverka('fullname', 'Это поле пустое', empty($fullname)))
        proverka('fullname', 'Неправильный формат: введите ФИО на русском', !preg_match('/^([а-яё]+-?[а-яё]+)( [а-яё]+-?[а-яё]+){1,2}$/Diu', $fullname));
    if (!proverka('phone', 'Это поле пустое', empty($phone))) {
        proverka('phone', 'Неправильный формат, должно быть 11 символов', strlen($phone) != 11);
        proverka('phone', 'Поле должно содержать только цифры', $phone != preg_replace('/\D/', '', $phone));
    }
    if (!proverka('email', 'Это поле пустое', empty($email)))
        proverka('email', 'Неправильный формат: example@mail.ru', !preg_match('/^\w+([.-]?\w+)@\w+([.-]?\w+)(.\w{2,3})+$/', $email));
    if (!proverka('birthday', 'Это поле пустое', empty($birthday)))
        proverka('birthday', 'Неправильная дата', strtotime('now') < strtotime($birthday));
    proverka('gender', "Не выбран пол", empty($gender) || !preg_match('/^(male|female)$/', $gender));
    if (!proverka('biography', 'Это поле пустое', empty($biography)))
        proverka('biography', 'Слишком длинное поле', strlen($biography) > 65535);
    proverka('accept', 'Не ознакомлены с контрактом', empty($accept));

    if (!proverka('language', 'Не выбран язык', empty($language))) {
        try {
            $inQuery = implode(',', array_fill(0, count($language), '?'));
            $dbLangs = $db->prepare("SELECT id, name FROM Languages WHERE name IN ($inQuery)");
            foreach ($language as $key => $value)
                $dbLangs->bindValue(($key + 1), $value);
            $dbLangs->execute();
            $Languages = $dbLangs->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            print ('Error : ' . $e->getMessage());
            exit();
        }
        proverka('language', 'Неверно выбраны языки', $dbLangs->rowCount() != count($language));
    }

    if (!$error) {
        setcookie('fullname_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('phone_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('email_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('birthday_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('gender_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('language_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('biography_error', '', time() - 30 * 24 * 60 * 60);
        setcookie('accept_error', '', time() - 30 * 24 * 60 * 60);

        if ($user) {
            $stmt = $db->prepare("UPDATE Users SET fullname = ?, phone = ?, email = ?, birthday = ?, gender = ?, biography = ? WHERE user_id = ?");
            $stmt->execute([$fullname, $phone, $email, $birthday, $gender, $biography, $_SESSION['user_id']]);


            $stmt = $db->prepare("DELETE FROM UserLanguages WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);

            $stmt1 = $db->prepare("INSERT INTO UserLanguages (user_id, lang_id) VALUES (?, ?)");
            foreach ($Languages as $row)
                $stmt1->execute([$_SESSION['user_id'], $row['id']]);
        } else {
            $username = uniqid();
            $password = uniqid();
            setcookie('username', $username);
            setcookie('password', $password);
            $mpassword = md5($password);
            try {
                $stmt = $db->prepare("INSERT INTO Authorization (username, password) VALUES (?, ?)");
                $stmt->execute([$username, $mpassword]);
                $user_id = $db->lastInsertId();
        
                $stmt = $db->prepare("INSERT INTO Users (user_id, fullname, phone, email, birthday, gender, biography, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $fullname, $phone, $email, $birthday, $gender, $biography, $username, $mpassword]);
                $form_id = $db->lastInsertId();
        
                $stmt1 = $db->prepare("INSERT INTO UserLanguages (user_id, lang_id) VALUES (?, ?)");
                foreach ($Languages as $row)
                    $stmt1->execute([$form_id, $row['id']]);
            } catch (PDOException $e) {
                if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    echo 'Ошибка: Дублирование ключа.';
                } else {
                    echo 'Ошибка: ' . $e->getMessage();
                }
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
        }
        setcookie('save', '1');
    }
    header('Location: index.php');
} else {
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
    $error = true;

    function set_val($str, $pole)
    {
        global $values;
        $values[$str] = empty($pole) ? '' : strip_tags($pole);
    }

    function proverka($str, $pole)
    {
        global $errors, $messages, $values, $error;
        if ($error)
            $error = empty($_COOKIE[$str . '_error']);
        $errors[$str] = !empty($_COOKIE[$str . '_error']);
        $messages[$str] = "<div class=\"errorMessage\">$pole</div>";
        $values[$str] = empty($_COOKIE[$str . '_value']) ? '' : strip_tags($_COOKIE[$str . '_value']);
        setcookie($str . '_error', '', time() - 30 * 24 * 60 * 60);
        return;
    }

    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        setcookie('username', '', 100000);
        setcookie('password', '', 100000);
        $messages['success'] = 'Данные сохранены.';
        if (!empty($_COOKIE['password']))
            $messages['info'] = sprintf('Вы можете <a href="username.php">войти</a> с логином <strong>%s</strong><br>
            и паролем <strong>%s</strong> для изменения данных.', strip_tags($_COOKIE['username']), strip_tags($_COOKIE['password']));
    }

    proverka('fullname', $fullname);
    proverka('phone', $phone);
    proverka('email', $email);
    proverka('birthday', $birthday);
    proverka('gender', $gender);
    proverka('language', $language);
    proverka('biography', $biography);
    proverka('accept', $accept);

    $Languages = explode(',', $values['language']);

    if ($error && !empty($_SESSION['username'])) {
        try {
            $dbLangs = $db->prepare("SELECT * FROM Users WHERE user_id = ?");
            $dbLangs->execute([$_SESSION['user_id']]);
            $user_details = $dbLangs->fetchAll(PDO::FETCH_ASSOC)[0];

            $form_id = $user_details['id'];
            $_SESSION['form_id'] = $form_id;

            $dbL = $db->prepare("SELECT l.name FROM UserLanguages f
                                JOIN Languages l ON l.id = f.lang_id
                                WHERE f.user_id = ?");

            $dbL->execute([$form_id]);

            $Languages = [];
            foreach ($dbL->fetchAll(PDO::FETCH_ASSOC) as $item)
                $Languages[] = $item['name'];

            set_val('fullname', $user_details['fullname']);
            set_val('phone', $user_details['phone']);
            set_val('email', $user_details['email']);
            set_val('birthday', birthday("Y-m-d", $user_details['birthday']));
            set_val('gender', $user_details['gender']);
            set_val('language', $language);
            set_val('biography', $user_details['biography']);
            set_val('accept', "1");
        } catch (PDOException $e) {
            print ('Error : ' . $e->getMessage());
            exit();
        }
    }

    include ('form.php');
}
?>