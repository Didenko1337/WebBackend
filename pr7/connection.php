<?php
    $db = new PDO('mysql:host=127.127.126.53;port=3306;dbname=project', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    ini_set('display_errors', 'off');
    ini_set('session.cookie_samesite', 'Lax');

    session_start();

    function checkInput($param){
        return htmlspecialchars(strip_tags(trim($param)), ENT_QUOTES);
    }

    function checkInput_decode($param){
        return htmlspecialchars_decode($param, ENT_QUOTES);
    }

    function checkAdmin($login, $password){
        global $db;
        if(isset($login) && isset($password)){
            $qu = $db->prepare("SELECT id FROM users WHERE role = 'admin' and login = ? and password = ?");
            $qu->execute([$login, md5($password)]);
            return $qu->rowCount();
        }
    }
?>