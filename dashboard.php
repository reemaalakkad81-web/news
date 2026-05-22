<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم - نظام إدارة الأخبار</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .user-info {
            color: #555;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 10px;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .menu {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .menu h3 {
            margin-top: 0;
            color: #333;
        }
        .menu-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .menu-links a {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .menu-links a:hover {
            background-color: #0056b3;
        }
        .content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>نظام إدارة الأخبار</h1>
            <div>
                <span class="user-info">مرحباً، <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php" class="logout-btn">تسجيل خروج</a>
            </div>
        </div>
        
        <div class="menu">
            <h3>القائمة الرئيسية</h3>
            <div class="menu-links">
                <a href="add_category.php"> إضافة فئة</a>
                <a href="view_categories.php"> عرض الفئات</a>
                <a href="add_news.php"> إضافة خبر</a>
                <a href="view_news.php"> عرض جميع الأخبار</a>
                <a href="view_deleted_news.php"> عرض الأخبار المحذوفة</a>
            </div>
        </div>
        
        <div class="content">
            <h3>مرحباً بك في لوحة التحكم</h3>
            <p>اختر إحدى الوظائف من القائمة أعلاه للبدء في إدارة الأخبار والفئات.</p>
        </div>
    </div>
</body>
</html>