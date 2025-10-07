<?php
 require 'settings.php';
try {
    $pdo = new PDO("mysql:host=localhost;dbname=" . CONF_DB_NAME, CONF_DB_USER, CONF_DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional test line:
    // echo "Database connection successful.";

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

 
 
if ($_GET['url'] === 'getCourses') {
    $stmt = $pdo->query("SELECT id, level_name as name FROM course_levels");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 1, 'data' => $courses]);
    exit;
}

if ($_GET['url'] === 'getYears') {
    $stmt = $pdo->query("SELECT id, name FROM course_year");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 1, 'data' => $courses]);
    exit;
}

if ($_GET['url'] === 'getExamboards') {
    $stmt = $pdo->query("SELECT id, name FROM course_examboards");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 1, 'data' => $courses]);
    exit;
}

if ($_GET['url'] === 'getTiers') {
    $stmt = $pdo->query("SELECT id, name FROM course_tier");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 1, 'data' => $courses]);
    exit;
}


if ($_GET['url'] === 'getSubjects') {
    // Expect levelId param
    $levelId = $_GET['levelId'] ?? '';
    if (!$levelId) {
        echo json_encode(['status' => 0, 'error' => 'Missing levelId']);
        exit;
    }
    $stmt = $pdo->prepare("SELECT id, subject as name FROM course_subjects WHERE level_id = ?");
    $stmt->execute([$levelId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 1, 'data' => $rows]);
    exit;
}
