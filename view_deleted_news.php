<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

$stmt = $pdo->prepare("
    SELECT news.*, categories.name as category_name, users.name as user_name 
    FROM news 
    JOIN categories ON news.category_id = categories.id 
    JOIN users ON news.user_id = users.id 
    WHERE news.deleted = 1 
    ORDER BY news.id DESC
");
$stmt->execute();
$deleted_news = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الأخبار المحذوفة - نظام إدارة الأخبار</title>
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
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #dc3545;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .no-data {
            text-align: center;
            color: #888;
            padding: 20px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .btn-restore {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-restore:hover {
            background-color: #218838;
        }
        .btn-permanent-delete {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            margin-left: 5px;
        }
        .btn-permanent-delete:hover {
            background-color: #c82333;
        }
        .news-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .no-image {
            color: #999;
            font-size: 12px;
        }
        .deleted-badge {
            background-color: #dc3545;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        .actions {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2> الأخبار المحذوفة <span class="deleted-badge">محذوف</span></h2>
        
        <?php if(count($deleted_news) > 0): ?>
            <p> عدد الأخبار المحذوفة: <?php echo count($deleted_news); ?></p>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>العنوان</th>
                        <th>الفئة</th>
                        <th>المستخدم</th>
                        <th>تاريخ الحذف</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($deleted_news as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td>
                                <?php if(!empty($item['image']) && file_exists('uploads/' . $item['image'])): ?>
                                    <img src="uploads/<?php echo $item['image']; ?>" class="news-image" alt="صورة الخبر">
                                <?php else: ?>
                                    <span class="no-image">لا توجد صورة</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['user_name']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($item['updated_at'])); ?></td>
                            <td class="actions">
                                <a href="restore_news.php?id=<?php echo $item['id']; ?>" class="btn-restore" onclick="return confirm('هل أنت متأكد من استعادة هذا الخبر؟')">↩️ استعادة</a>
                                <a href="permanent_delete_news.php?id=<?php echo $item['id']; ?>" class="btn-permanent-delete" onclick="return confirm('تحذير! هذا سيحذف الخبر نهائياً. هل أنت متأكد؟')">🗑️ حذف نهائي</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">لا توجد أخبار محذوفة</div>
        <?php endif; ?>
        
        <br>
        <a href="dashboard.php" class="back-link">← العودة إلى لوحة التحكم</a>
    </div>
</body>
</html>