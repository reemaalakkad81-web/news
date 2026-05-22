<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

if(isset($_GET['id'])) {
    $news_id = $_GET['id'];
    
    $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
    $stmt->execute([$news_id]);
    $news = $stmt->fetch();
    
    if($news && !empty($news['image']) && file_exists('uploads/' . $news['image'])) {
        unlink('uploads/' . $news['image']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    $stmt->execute([$news_id]);
}

header('Location: view_deleted_news.php');
exit();
?>