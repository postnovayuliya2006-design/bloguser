<?php
require_once "config/db.php";
require_once "templates/header.php";

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$limit = 5;

$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("
SELECT posts.*, users.name AS author 
FROM posts 
JOIN users ON posts.user_id = users.id 
ORDER BY created_at DESC
LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPosts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();

$totalPages = ceil($totalPosts / $limit);
?>

<?php if($posts): ?>
    <?php foreach($posts as $post): ?>
        <article class="post-card">

            <h2>
                <a href="post.php?id=<?=$post['id']?>">
                    <?=htmlspecialchars($post['title'])?>
                </a>
            </h2>

            <div class="post-meta">
                Автор: <?=htmlspecialchars($post['author'])?> |
                <?=$post['created_at']?>
            </div>

            <p>
                <?=htmlspecialchars(substr($post['content'],0,200))?>...
            </p>

            <?php if($post['image']): ?>
                <img src="uploads/<?=htmlspecialchars($post['image'])?>" alt="image">
            <?php endif; ?>

        </article>
    <?php endforeach; ?>

<?php else: ?>
    <p>Постов пока нет.</p>
<?php endif; ?>

<?php if($totalPages > 1): ?>

<div class="pagination">

<?php if($page > 1): ?>
<a href="?page=<?=$page-1?>">← Назад</a>
<?php endif; ?>

<?php for($i = 1; $i <= $totalPages; $i++): ?>
<a href="?page=<?=$i?>" class="<?=$i==$page ? 'active' : ''?>">
<?=$i?>
</a>
<?php endfor; ?>

<?php if($page < $totalPages): ?>
<a href="?page=<?=$page+1?>">Вперёд →</a>
<?php endif; ?>

</div>

<?php endif; ?>

<?php require_once "templates/footer.php"; ?>