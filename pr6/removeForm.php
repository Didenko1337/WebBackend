<?php
    require('connection.php');

    function result($status, $val){
        exit(json_encode(array('status' => $status, 'value' => $val), JSON_UNESCAPED_UNICODE));
    }
    
    if(!checkAdmin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) result('error', "Вы не авторизованы");

    $id = $_POST['id'];
    if(!preg_match('/^[0-9]+$/', $id)) result('error', "Введите id");
    
    $dbf = $db->prepare("SELECT * FROM form_data WHERE id = ?");
    $dbf->execute([$id]);
    if($dbf->rowCount() != 0){
        $dbdel = $db->prepare("DELETE FROM form_data WHERE id = ?");
        $dbdel->execute([$id]);
        $dbdel = $db->prepare("DELETE FROM form_data_lang WHERE id_form = ?");
        ($dbdel->execute([$id])) ? result('success', "Форма удалена") : result('error', "Ошибка удаления");
    }
    else{
        result('error', "Форма не найдена");
    }
?>