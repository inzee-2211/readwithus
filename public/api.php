<?php
require 'settings.php';
try {
    $pdo = new PDO("mysql:host=localhost;dbname=" . CONF_DB_NAME, CONF_DB_USER, CONF_DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get Courses (Levels)
if ($_GET['url'] === 'getCourses') {
    $stmt = $pdo->query("SELECT id, level_name as name FROM course_levels");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 1, 'data' => $courses]);
    exit;
}

// Get Years (Non-GCSE flow)
if ($_GET['url'] === 'getYears') {
    $subjectId = $_GET['subjectId'] ?? null; 
    $levelId   = $_GET['levelId'] ?? null;

    if ($subjectId) {
        $stmt = $pdo->prepare("SELECT id, name FROM course_year WHERE subject_id = ?");
        $stmt->execute([$subjectId]);
    } elseif ($levelId) {
        $stmt = $pdo->prepare("SELECT id, name FROM course_year WHERE level_id = ?");
        $stmt->execute([$levelId]);
    } else {
        // fallback → saare years
        $stmt = $pdo->query("SELECT id, name FROM course_year");
    }

    $years = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 1, 'data' => $years]);
    exit;
}

// Get Exam Boards
if ($_GET['url'] === 'getExamboards') {
    $subjectId = $_GET['subjectId'] ?? '';
    if (!$subjectId) {
        echo json_encode(['status' => 0, 'error' => 'Missing subjectId']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, name FROM course_examboards WHERE subject_id = ?");
    $stmt->execute([$subjectId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 1, 'data' => $rows]);
    exit;
}

// Get Tiers
if ($_GET['url'] === 'getTiers') {
    $examboardId = $_GET['examboardId'] ?? '';
    if (!$examboardId) {
        echo json_encode(['status' => 0, 'error' => 'Missing examboardId']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, name FROM course_tier WHERE examboard_id = ?");
    $stmt->execute([$examboardId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 1, 'data' => $rows]); 
    exit;
}

// Get Subjects
if ($_GET['url'] === 'getSubjects') {
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

