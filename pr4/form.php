<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Задание 4</title>
</head>
<body>
<div class="pform">
    <form action="" method="post" class="form">
        <h1>Форма регистрации</h1>
        <div class="message"><?php if(isset($messages['success'])) echo $messages['success']; ?></div>
        <div class="form-group">
            <input name="fio" class="w100" <?php echo ($errors['fio'] != NULL) ? 'border_error' : ''; ?>" value="<?php echo $values['fio']; ?>" type="text" placeholder="ФИО" required>
            <div class="messageError"><?php echo $messages['fio']?></div>
        </div>
        <div class="form-group">
            <input name="phone" class="w100" <?php echo ($errors['phone'] != NULL) ? 'border_error' : ''; ?>" value="<?php echo $values['phone']; ?>" type="tel" placeholder="Телефон" pattern="\+?[0-9]{1,3}\(?[0-9]{3}\)?[0-9]{3}-?[0-9]{2}-?[0-9]{2}" required>
            <div class="messageError"><?php echo $messages['phone']?></div>
        </div>
        <div class="form-group">
            <input name="email" class="w100" <?php echo ($errors['email'] != NULL) ? 'border_error' : ''; ?>" value="<?php echo $values['email']; ?>" type="email" placeholder="email" required>
            <div class="messageError"><?php echo $messages['email']?></div>
        </div>
        <div class="form-group">
            <input name="birthday" class="w100" <?php echo ($errors['birthday'] != NULL) ? 'border_error' : ''; ?>" value="<?php echo $values['birthday']; ?>" type="date" required>
            <div class="messageError"><?php echo $messages['birthday']?></div>
        </div>
        <div class="gender_left">
            <label>Пол:</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="gender" value="m" <?php if($values['gender'] == 'm') echo 'checked'; ?> required>
                    <span class="<?php echo ($errors['gender'] != NULL) ? 'border_error' : ''; ?>">Мужской</span>
                </label>
                <label>
                    <input type="radio" name="gender" value="f" <?php if($values['gender'] == 'f') echo 'checked'; ?>>
                    <span class="<?php echo ($errors['gender'] != NULL) ? 'border_error' : ''; ?>">Женский</span>
                </label>
            </div>
            <div class="messageError"><?php echo $messages['gender']?></div>
        </div>
        <label>Любимый язык программирования:</label>
        <div class="w100">
            <select class=" <?php echo ($errors['like_lang'] != NULL) ? 'border_error' : ''; ?>" name="like_lang[]" id="like_lang" multiple="multiple">
                <option value="Pascal" <?php echo (in_array('Pascal', $like_langs)) ? 'selected' : ''; ?>>Pascal</option>
                <option value="C" <?php echo (in_array('C', $like_langs)) ? 'selected' : ''; ?>>C</option>
                <option value="C++" <?php echo (in_array('C++', $like_langs)) ? 'selected' : ''; ?>>C++</option>
                <option value="JavaScript" <?php echo (in_array('JavaScript', $like_langs)) ? 'selected' : ''; ?>>JavaScript</option>
                <option value="PHP" <?php echo (in_array('PHP', $like_langs)) ? 'selected' : ''; ?>>PHP</option>
                <option value="Python" <?php echo (in_array('Python', $like_langs)) ? 'selected' : ''; ?>>Python</option>
                <option value="Java" <?php echo (in_array('Java', $like_langs)) ? 'selected' : ''; ?>>Java</option>
                <option value="Haskell" <?php echo (in_array('Haskell', $like_langs)) ? 'selected' : ''; ?>>Haskell</option>
                <option value="Clojure" <?php echo (in_array('Clojure', $like_langs)) ? 'selected' : ''; ?>>Clojure</option>
                <option value="Scala" <?php echo (in_array('Scala', $like_langs)) ? 'selected' : ''; ?>>Scala</option>
            </select>
            <div class="messageError"><?php echo $messages['like_lang']?></div>
        </div>
        <div class="w100">
            <textarea id="biography" name="biography" class=" <?php echo ($errors['biography'] != NULL) ? 'border_error' : ''; ?>" placeholder="Биография" rows="2" cols="50" required><?php echo $values['biography']; ?></textarea>
            <div class="messageError"><?php echo $messages['biography']?></div>
        </div>
        <div class="oznakomlen_element">
            <input type="checkbox" id="oznakomlen" name="oznakomlen" <?php echo ($values['oznakomlen'] != NULL) ? 'checked' : ''; ?> required>
            <label for="oznakomlen">С контрактом ознакомлен(а)</label>
            <div class="messageError"><?php echo $messages['oznakomlen']?></div>
        </div>
        <div class="w100">
            <button type="submit">Отправить</button>
        </div>
    </form>
</div>
</body>
</html>