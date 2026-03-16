<?php
require_once "config/db.php";

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$error = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(!$name || !$email || !$password){
        $error = "Все поля обязательны";
    } else {
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email=?");
        $stmt->execute([$email]);
        if($stmt->fetchColumn()){
            $error = "Такой email уже зарегистрирован";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)");
            $stmt->execute([$name,$email,$hash,'user']);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['name'] = $name;
            $_SESSION['role'] = 'user';

            header("Location: /index.php");
            exit;
        }
    }
}

require_once "templates/header.php";
?>

<link rel="stylesheet" href="/style.css">

<?php if($error): ?>
<p style="color:red;"><?=htmlspecialchars($error)?></p>
<?php endif; ?>

<form method="post" class="auth-form">
    <h2>Регистрация</h2>
    <input type="text" name="name" placeholder="Имя" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Зарегистрироваться</button>
    <p class="auth-link">
        Уже зарегистрированы? <a href="login.php">Войти</a>
    </p>
</form>

<?php require_once "templates/footer.php"; ?>