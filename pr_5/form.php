<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Форма регистрации</title>
</head>
<body>
<div class="container">
    <form action="" method="post" class="form">
        <h1>Форма регистрации</h1>
        <div class="message"><?php if(isset($messages['success'])) echo $messages['success']; ?></div>
        <div class="message message_info"><?php if(isset($messages['info'])) echo $messages['info']; ?></div>
        <div class="form-group">
            <input name="fullname" class=" <?php echo ($errors['fullname'] != NULL) ? 'red' : ''; ?>" value="<?php echo $values['fullname']; ?>" type="text" placeholder="Ваши ФИО" required>
            <div class="messageError"><?php echo $messages['fullname']?></div>
        </div>
        <div class="form-group">
            <input name="phone" class=" <?php echo ($errors['phone'] != NULL) ? 'red' : ''; ?>" value="<?php echo $values['phone']; ?>" type="tel" placeholder="Ваш номер телефона" pattern="\+?[0-9]{1,3}\(?[0-9]{3}\)?[0-9]{3}-?[0-9]{2}-?[0-9]{2}" required>
            <div class="messageError"><?php echo $messages['phone']?></div>
        </div>
        <div class="form-group">
            <input name="email" class=" <?php echo ($errors['email'] != NULL) ? 'red' : ''; ?>" value="<?php echo $values['email']; ?>" type="email" placeholder="Ваш email" required>
            <div class="messageError"><?php echo $messages['email']?></div>
        </div>
        <div class="form-group">
            <input name="birthday" class=" <?php echo ($errors['birthday'] != NULL) ? 'red' : ''; ?>" value="<?php echo $values['birthday']; ?>" type="date" required>
            <div class="messageError"><?php echo $messages['birthday']?></div>
        </div>
        <div class="form-group">
            <label>Пол:</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="gender" value="male" <?php if($values['gender'] == 'male') echo 'checked'; ?> required>
                    <span class="<?php echo ($errors['gender'] != NULL) ? 'red' : ''; ?>">Мужской</span>
                </label>
                <label>
                    <input type="radio" name="gender" value="female" <?php if($values['gender'] == 'female') echo 'checked'; ?>>
                    <span class="<?php echo ($errors['gender'] != NULL) ? 'red' : ''; ?>">Женский</span>
                </label>
            </div>
            <div class="messageError"><?php echo $messages['gender']?></div>
        </div>
        <div class="form-group">
            <label>Любимый язык программирования:</label>
            <select class=" <?php echo ($errors['language'] != NULL) ? 'red' : ''; ?>" name="language[]" id="language" multiple="multiple">
                <option value="Pascal" <?php echo (in_array('Pascal', $Languages)) ? 'selected' : ''; ?>>Pascal</option>
                <option value="C" <?php echo (in_array('C', $Languages)) ? 'selected' : ''; ?>>C</option>
                <option value="C++" <?php echo (in_array('C++', $Languages)) ? 'selected' : ''; ?>>C++</option>
                <option value="JavaScript" <?php echo (in_array('JavaScript', $Languages)) ? 'selected' : ''; ?>>JavaScript</option>
                <option value="PHP" <?php echo (in_array('PHP', $Languages)) ? 'selected' : ''; ?>>PHP</option>
                <option value="Python" <?php echo (in_array('Python', $Languages)) ? 'selected' : ''; ?>>Python</option>
                <option value="Java" <?php echo (in_array('Java', $Languages)) ? 'selected' : ''; ?>>Java</option>
                <option value="Haskell" <?php echo (in_array('Haskell', $Languages)) ? 'selected' : ''; ?>>Haskell</option>
                <option value="Clojure" <?php echo (in_array('Clojure', $Languages)) ? 'selected' : ''; ?>>Clojure</option>
                <option value="Scala" <?php echo (in_array('Scala', $Languages)) ? 'selected' : ''; ?>>Scala</option>
            </select>
            <div class="messageError"><?php echo $messages['language']?></div>
        </div>
        <div class="form-group">
            <textarea id="biography" name="biography" class=" <?php echo ($errors['biography'] != NULL) ? 'red' : ''; ?>" placeholder="Расскажите о себе" rows="2" cols="50" required><?php echo $values['biography']; ?></textarea>
            <div class="messageError"><?php echo $messages['biography']?></div>
        </div>
        <div class="form-group checkbox-group">
            <input type="checkbox" id="accept" name="accept" <?php echo ($values['accept'] != NULL) ? 'checked' : ''; ?> required>
            <label for="accept">С контрактом ознакомлен(а)</label>
            <div class="messageError"><?php echo $messages['accept']?></div>
        </div>
        <div class="form-group">
            <?php
                if($user) echo '<button class="button" type="submit">Изменить</button>';
                else echo '<button class="button" type="submit">Отправить</button>';
                if($user) echo '<button type="submit" class="logout_form" name="logout_form">Выйти</button>'; 
                else echo '<a href="login.php" class="login_form" name="logout_form">Войти</a>';
            ?>
        </div>
    </form>
</div>
</body>
</html>