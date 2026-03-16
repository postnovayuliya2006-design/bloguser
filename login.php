<?php
require_once "config/db.php";

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$error = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user['password'])){

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        if($user['role'] === "admin"){
            header("Location: /admin/index.php");
        } else {
            header("Location: /index.php");
        }
        exit;

    } else {
        $error = "Неверный email или пароль";
    }
}

require_once "templates/header.php";
?>

<link rel="stylesheet" href="/style.css">

<?php if($error): ?>
<p style="color:red;"><?=$error?></p>
<?php endif; ?>

<form method="post">

<h2 class="tex">Вход</h2>

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Пароль" required>

<button type="submit">Войти</button>

<p class="auth-link">
Ещё не зарегистрированы?
<a href="register.php">Зарегистрироваться</a>
</p>

</form>

<?php require_once "templates/footer.php"; ?>