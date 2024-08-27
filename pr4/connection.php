<?php
    $db = new PDO('mysql:host=127.127.126.53;port=3306;dbname=project', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>