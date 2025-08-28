<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("UPDATE appointments SET viewed = 1 WHERE id = ?");
    $stmt->execute([$id]);
    echo 'Marked as viewed';
}
