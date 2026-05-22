<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

$success = '';
$error = '';

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $category_id = $_POST['category_id'];
    $details = trim($_POST['details']);
    $user_id = $_SESSION['user_id'];
    
    if(empty($title)) {
        $error = 'يرجى إدخال عنوان الخبر';
    } elseif(empty($category_id)) {
        $error = 'يرجى اختيار الفئة';
    } elseif(empty($details)) {
        $error = 'يرجى إدخال تفاصيل الخبر';
    } else {
        $image_name = '';
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $file_name = $_FILES['image']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $file_size = $_FILES['image']['size'];
            $file_tmp = $_FILES['image']['tmp_name'];
            
            if(!in_array($file_ext, $allowed)) {
                $error = 'الصورة غير مسموحة. الأنواع المسموحة: jpg, jpeg, png, gif, webp';
            } elseif($file_size > 5000000) { // 5MB
                $error = 'حجم الصورة كبير جداً. الحد الأقصى 5MB';
            } else {
                $image_name = time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = 'uploads/' . $image_name;
                
                if(!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }
                
                if(move_uploaded_file($file_tmp, $upload_path)) {
                } else {
                    $error = 'فشل في رفع الصورة';
                }
            }
        }
        
        if(empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO news (title, category_id, details, image, user_id) VALUES (?, ?, ?, ?, ?)");
            if($stmt->execute([$title, $category_id, $details, $image_name, $user_id])) {
                $success = 'تم إضافة الخبر بنجاح';
                $title = $details = '';
            } else {
                $error = 'حدث خطأ، يرجى المحاولة مرة أخرى';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة خبر - نظام إدارة الأخبار</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
            min-height: 150px;
        }
        input[type="file"] {
            padding: 10px;
            border: 1px dashed #ddd;
            border-radius: 5px;
            width: 100%;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .image-preview {
            margin-top: 10px;
            display: none;
        }
        .image-preview img {
            max-width: 200px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📝 إضافة خبر جديد</h2>
        
        <?php if($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>عنوان الخبر *</label>
                <input type="text" name="title" required value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label>الفئة *</label>
                <select name="category_id" required>
                    <option value="">-- اختر الفئة --</option>
                    <?php foreach($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>تفاصيل الخبر *</label>
                <textarea name="details" required><?php echo isset($details) ? htmlspecialchars($details) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>صورة الخبر</label>
                <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
                <small>الأنواع المسموحة: jpg, jpeg, png, gif, webp (الحد الأقصى 5MB)</small>
                <div class="image-preview" id="imagePreview">
                    <img id="previewImg" src="#" alt="معاينة الصورة">
                </div>
            </div>
            
            <button type="submit"> حفظ الخبر</button>
        </form>
        
        <a href="dashboard.php" class="back-link">← العودة إلى لوحة التحكم</a>
    </div>
    
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const img = document.getElementById('previewImg');
            
            if(input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>