<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма регистрации</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Форма регистрации</h1>
        <form action="index.php" method="post">
            <div class="form-group">
                <input type="text" id="fullname" name="fullname" placeholder="Ваши ФИО" required>
            </div>
            <div class="form-group">
                <input type="tel" id="phone" name="phone" placeholder="Ваш номер телефона" pattern="\+?[0-9]{1,3}\(?[0-9]{3}\)?[0-9]{3}-?[0-9]{2}-?[0-9]{2}" required>
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Ваш email" required>
            </div>
            <div class="form-group">
                <input type="date" id="birthday" name="birthday" required>
            </div>
            <div class="form-group">
                <label>Пол:</label>
                <div class="radio-group">
                    <input type="radio" id="male" name="gender" value="male" required>
                    <label for="male">Мужчина</label>
                    <input type="radio" id="female" name="gender" value="female">
                    <label for="female">Женщина</label>
                </div>
            </div>
            <div class="form-group">
                <label>Любимый язык программирования:</label>
                <select id="language" name="language[]" multiple ='multiple'>
                    <option value="Pascal">Pascal</option>
                    <option value="C">C</option>
                    <option value="C++">C++</option>
                    <option value="JavaScript">JavaScript</option>
                    <option value="PHP">PHP</option>
                    <option value="Python">Python</option>
                    <option value="Java">Java</option>
                    <option value="Haskell">Haskell</option>
                    <option value="Clojure">Clojure</option>
                    <option value="Scala">Scala</option>
                </select>
            </div>
            <div class="form-group">
                <textarea id="biography" name="biography" placeholder="Расскажите о себе" rows="2" cols="50" required></textarea>
            </div>
            <div class="form-group checkbox-group">
                <input type="checkbox" id="accept" name="accept" required>
                <label for="accept">С контрактом ознакомлен(а)</label>
            </div>
            <div class="form-group">
                <button type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</body>
</html>
