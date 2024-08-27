<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-4.0.0-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Задание 7</title>
</head>
<body>
<div class="pform">
    <form action="" method="post">
        <?php
            $csrf_token = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrf_token;
        ?>
        <input type="hidden" name='csrf_token' value='<?php echo $csrf_token; ?>'>

        <h3>Форма регистрации</h3>
        <div class="message"><?php if(isset($messages['success'])) echo $messages['success']; ?></div>
        <div class="message message_info"><?php if(isset($messages['info'])) echo $messages['info']; ?></div>
        <div>
            <input class="w100 <?php if($errors['fio'] != NULL) echo 'border_error'; ?>" value="<?php echo $values['fio']; ?>" type="text" name="fio" placeholder="ФИО">
            <div><?php echo $messages['fio']?></div>
        </div>
        <div>
            <input class="w100 <?php if($errors['phone'] != NULL) echo 'border_error'; ?>" value="<?php echo $values['phone']; ?>" type="tel" name="phone" placeholder="Телефон">
            <div><?php echo $messages['phone']?></div>
        </div>
        <div>
            <input class="w100 <?php if($errors['email'] != NULL) echo 'border_error'; ?>" value="<?php echo $values['email']; ?>" type="email" name="email" placeholder="email">
            <div><?php echo $messages['email']?></div>
        </div>
        <div>
            <input class="w100 <?php if($errors['birthday'] != NULL) echo 'border_error'; ?>" value="<?php if(strtotime($values['birthday']) > 100000) echo $values['birthday']; ?>" type="date" name="birthday">
            <div><?php echo $messages['birthday']?></div>
        </div>
        <div class="gender_left">
            <div>Пол:</div>
            <label>
                <input type="radio" name="gender" value="m" <?php if($values['gender'] == 'm' || $values['gender'] == '') echo 'checked'; ?>>
                <span>Мужской</span>
            </label>
            <br>
            <label>
                <input type="radio" name="gender" value="f" <?php if($values['gender'] == 'f') echo 'checked'; ?>>
                <span>Женский</span>
            </label>
            <div><?php echo $messages['gender']?></div>
        </div>
        <div>
        <label>Любимый язык программирования:</label>
            <select class="w100 <?php if($errors['like_lang'] != NULL) echo 'border_error'; ?>" name="like_lang[]" id="like_lang" multiple="multiple">
                <option value="Pascal" <?php if(in_array('Pascal', $like_lang_array)) echo 'selected'; ?>>Pascal</option>
                <option value="C" <?php if(in_array('C', $like_lang_array)) echo 'selected'; ?>>C</option>
                <option value="C++" <?php if(in_array('C++', $like_lang_array)) echo 'selected'; ?>>C++</option>
                <option value="JavaScript" <?php if(in_array('JavaScript', $like_lang_array)) echo 'selected'; ?>>JavaScript</option>
                <option value="PHP" <?php if(in_array('PHP', $like_lang_array)) echo 'selected'; ?>>PHP</option>
                <option value="Python" <?php if(in_array('Python', $like_lang_array)) echo 'selected'; ?>>Python</option>
                <option value="Java" <?php if(in_array('Java', $like_lang_array)) echo 'selected'; ?>>Java</option>
                <option value="Haskel" <?php if(in_array('Haskel', $like_lang_array)) echo 'selected'; ?>>Haskel</option>
                <option value="Clojure" <?php if(in_array('Clojure', $like_lang_array)) echo 'selected'; ?>>Clojure</option>
                <option value="Prolog" <?php if(in_array('Prolog', $like_lang_array)) echo 'selected'; ?>>Prolog</option>
                <option value="Scala" <?php if(in_array('Scala', $like_lang_array)) echo 'selected'; ?>>Scala</option>
            </select>
            <div><?php echo $messages['like_lang']?></div>
        </div>
        <div>
            <textarea name="biography" placeholder="Биография" class="<?php if($errors['biography'] != NULL) echo 'border_error'; ?>"><?php echo $values['biography']; ?></textarea>
            <div><?php echo $messages['biography']?></div>
        </div>
        <div class="oznak_element">
            <input type="checkbox" name="oznakomlen" id="oznakomlen" <?php echo ($values['oznakomlen'] != NULL) ? 'checked' : ''; ?>>
            <label for="oznakomlen" class="<?php if($errors['oznakomlen'] != NULL) echo 'color_error'; ?>">С контрактом ознакомлен (а)</label>
            <div><?php echo $messages['oznakomlen']?></div>
        </div>
        <?php
            if($userLog){
                echo '<button type="submit" class="editBut" name="editBut">Изменить</button><br>
                <button type="submit" class="logout_form" name="logout_form">Выйти</button>';

            }
            else{
                echo '<button type="submit">Отправить</button><br>
                <a href="login.php" class="login_form" name="logout_form">Войти</a>';
            }
        ?>
    </form>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function(){
        setTimeout(function(){
            let el = document.querySelector('input[name=gender][checked]');
            if(el != null)
                el.click();
        }, 200)
    });
</script>
</body>
</html>