<?php
    $db = new PDO('mysql:host=127.127.126.53;port=3306;dbname=project', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    function checkAdmin($login, $password){
        global $db;
        if(isset($login) && isset($password)){
            $qu = $db->prepare("SELECT id FROM users WHERE role = 'admin' and login = ? and password = ?");
            $qu->execute([$login, md5($password)]);
            return $qu->rowCount();
        }
    }
?>