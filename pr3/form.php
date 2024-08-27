<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <title>Задание 3</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="pform">
        <form action="index.php" method="post">
        <h3>Форма регистрации</h3>
            <div class="w100">
                <input type="text" id="fio" name="fio" placeholder="ФИО" required>
            </div>
            <div class="w100">
                <input type="tel" id="phone" name="phone" placeholder="Телефон" pattern="\+?[0-9]{1,3}\(?[0-9]{3}\)?[0-9]{3}-?[0-9]{2}-?[0-9]{2}" required>
            </div>
            <div class="w100">
                <input type="email" id="email" name="email" placeholder="email" required>
            </div>
            <div class="w100">
                <input type="date" id="birthday" name="birthday" required>
            </div>
            <div class="gender_left">
                <div>Пол:</div>
                <label>
                    <input type="radio" name="gender" value="m">
                    <span>Мужской</span>
                </label>
                <br>
                <label>
                    <input type="radio" name="gender" value="f">
                    <span>Женский</span>
                </label>
            </div>
            <div class="w100">
                <label>Любимый язык программирования:</label>
                <select id="like_lang" name="like_lang[]" multiple ='multiple'>
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
            <div class="w100">
                <textarea id="biography" name="biography" placeholder="Биография" rows="2" cols="50" required></textarea>
            </div>
            <div class="oznakomlen_element">
                <input type="checkbox" id="oznakomlen" name="oznakomlen" required>
                <label for="oznakomlen">С контрактом ознакомлен(а)</label>
            </div>
            <div class="w100">
                <button type="submit">Отправить</button>
            </div>
        </form>
    </div>
</body>
</html>
