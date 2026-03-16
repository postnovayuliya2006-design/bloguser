<?php
require_once "config/db.php";
require_once "templates/header.php";

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) { 
    echo "<p>Пост не найден</p>"; 
    require_once "templates/footer.php"; 
    exit; 
}
$post_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT posts.*, users.name AS author FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id=?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$post){ 
    echo "<p>Пост не найден</p>"; 
    require_once "templates/footer.php"; 
    exit; 
}

$stmt = $pdo->prepare("SELECT comments.*, users.name AS user_name FROM comments JOIN users ON comments.user_id = users.id WHERE post_id=? ORDER BY created_at ASC");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$like_count = $pdo->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id=?");
$like_count->execute([$post_id]);
$like_count = $like_count->fetchColumn();

$user_liked = false;
if(isset($_SESSION['user_id'])){
    $stmt = $pdo->prepare("SELECT 1 FROM post_likes WHERE post_id=? AND user_id=?");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    $user_liked = $stmt->fetchColumn() ? true : false;
}
?>

<link rel="stylesheet" href="/style.css">

<article class="post-card">
<h1><?=htmlspecialchars($post['title'])?></h1>
<div class="post-meta">Автор: <?=htmlspecialchars($post['author'])?> | <?=$post['created_at']?></div>
<?php if($post['image']): ?>
<img src="uploads/<?=htmlspecialchars($post['image'])?>" alt="image">
<?php endif; ?>
<p><?=nl2br(htmlspecialchars($post['content']))?></p>

<?php if(isset($_SESSION['user_id'])): ?>
<button class="like-post" data-id="<?=$post['id']?>">
    ❤️ <span class="like-count"><?=$like_count?></span> <?= $user_liked ? '(Вы лайкнули)' : '' ?>
</button>
<?php else: ?>
<p>Чтобы поставить лайк, войдите в аккаунт.</p>
<?php endif; ?>
</article>

<section id="comments">
<h2>Комментарии</h2>
<div id="comment-list">
<?php foreach($comments as $c): ?>
<div class="comment" data-id="<?=$c['id']?>">
<p><strong><?=htmlspecialchars($c['user_name'])?></strong> | <?=$c['created_at']?></p>
<p><?=nl2br(htmlspecialchars($c['content']))?></p>
</div>
<?php endforeach; ?>
</div>

<?php if(isset($_SESSION['user_id'])): ?>
<form id="comment-form">
    <textarea name="content" placeholder="Напишите свой комментарий..." required></textarea>
    <input type="hidden" name="post_id" value="<?=$post_id?>">
    <button type="submit">Отправить</button>
</form>
<?php else: ?>
<p>Чтобы оставить комментарий, войдите в аккаунт.</p>
<?php endif; ?>
</section>

<?php require_once "templates/footer.php"; ?>