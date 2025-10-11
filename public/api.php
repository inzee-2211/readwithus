<?php
require 'settings.php';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=" . CONF_DB_NAME, CONF_DB_USER, CONF_DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['status' => 0, 'msg' => "Database connection failed: " . $e->getMessage()]));
}

// Get the URL parameter
$url = $_GET['url'] ?? '';

// Debug logging
error_log("API Request: " . $url . ", GET: " . json_encode($_GET));

// Get Courses (Levels)
if ($url === 'getCourses') {
    try {
        $stmt = $pdo->query("SELECT id, level_name as name FROM course_levels ORDER BY level_name");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 1, 'data' => $courses]);
    } catch (Exception $e) {
        echo json_encode(['status' => 0, 'msg' => $e->getMessage()]);
    }
    exit;
}

// Get Years (Non-GCSE flow)
if ($url === 'getYears') {
    try {
        $subjectId = $_GET['subjectId'] ?? null; 
        $levelId   = $_GET['levelId'] ?? null;

        if ($subjectId) {
            $stmt = $pdo->prepare("SELECT id, name FROM course_year WHERE subject_id = ? ORDER BY name");
            $stmt->execute([$subjectId]);
        } elseif ($levelId) {
            $stmt = $pdo->prepare("SELECT id, name FROM course_year WHERE level_id = ? ORDER BY name");
            $stmt->execute([$levelId]);
        } else {
            $stmt = $pdo->query("SELECT id, name FROM course_year ORDER BY name");
        }

        $years = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 1, 'data' => $years]);
    } catch (Exception $e) {
        echo json_encode(['status' => 0, 'msg' => $e->getMessage()]);
    }
    exit;
}

// Get Exam Boards
if ($url === 'getExamboards') {
    try {
        $subjectId = $_GET['subjectId'] ?? '';
        if (!$subjectId) {
            echo json_encode(['status' => 0, 'error' => 'Missing subjectId']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT id, name FROM course_examboards WHERE subject_id = ? ORDER BY name");
        $stmt->execute([$subjectId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 1, 'data' => $rows]);
    } catch (Exception $e) {
        echo json_encode(['status' => 0, 'msg' => $e->getMessage()]);
    }
    exit;
}

// Get Tiers
if ($url === 'getTiers') {
    try {
        $examboardId = $_GET['examboardId'] ?? '';
        if (!$examboardId) {
            echo json_encode(['status' => 0, 'error' => 'Missing examboardId']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT id, name FROM course_tier WHERE examboard_id = ? ORDER BY name");
        $stmt->execute([$examboardId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 1, 'data' => $rows]); 
    } catch (Exception $e) {
        echo json_encode(['status' => 0, 'msg' => $e->getMessage()]);
    }
    exit;
}

// Get Subjects
if ($url === 'getSubjects') {
    try {
        $levelId = $_GET['levelId'] ?? '';
        if (!$levelId) {
            echo json_encode(['status' => 0, 'error' => 'Missing levelId']);
            exit;
        }
        $stmt = $pdo->prepare("SELECT id, subject as name FROM course_subjects WHERE level_id = ? ORDER BY subject");
        $stmt->execute([$levelId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 1, 'data' => $rows]);
    } catch (Exception $e) {
        echo json_encode(['status' => 0, 'msg' => $e->getMessage()]);
    }
    exit;
}

if ($url === 'getQuizzesBySubtopic') {
    try {
        $subtopicId = (int)($_GET['subtopicId'] ?? 0);
        if ($subtopicId < 1) { echo json_encode(['status'=>0,'error'=>'Missing subtopicId']); exit; }

        // fetch subtopic text
        $txt = $pdo->prepare("SELECT topic FROM course_topics WHERE id = ?");
        $txt->execute([$subtopicId]);
        $subtopicRow = $txt->fetch(PDO::FETCH_ASSOC);
        if (!$subtopicRow) { echo json_encode(['status'=>1,'data'=>[]]); exit; }

        $stmt = $pdo->prepare("
            SELECT id, question_title AS name
            FROM tbl_quaestion_bank
            WHERE subtopic = ?
            ORDER BY id ASC
        ");
        $stmt->execute([$subtopicRow['topic']]);
        echo json_encode(['status'=>1,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (Exception $e) {
        echo json_encode(['status'=>0,'msg'=>$e->getMessage()]);
    }
    exit;
}
// NEW: Get Topics for a Subject (top level topics only)
if ($url === 'getTopics') {
    try {
        $subjectId = (int)($_GET['subjectId'] ?? 0);
        if ($subjectId < 1) { echo json_encode(['status'=>0,'error'=>'Missing subjectId']); exit; }

        // parent_id IS NULL or 0 = top-level topic
        $stmt = $pdo->prepare("
            SELECT id, topic AS name
            FROM course_topics
            WHERE subject_id = ? AND (parent_id IS NULL OR parent_id = 0)
            ORDER BY topic ASC
        ");
        $stmt->execute([$subjectId]);
        echo json_encode(['status'=>1, 'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (Exception $e) {
        echo json_encode(['status'=>0,'msg'=>$e->getMessage()]);
    }
    exit;
}

// NEW: Get Subtopics for a Topic
if ($url === 'getSubtopics') {
    try {
        $topicId = (int)($_GET['topicId'] ?? 0);
        if ($topicId < 1) { echo json_encode(['status'=>0,'error'=>'Missing topicId']); exit; }

        $stmt = $pdo->prepare("
            SELECT id, topic AS name
            FROM course_topics
            WHERE parent_id = ?
            ORDER BY topic ASC
        ");
        $stmt->execute([$topicId]);
        echo json_encode(['status'=>1, 'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (Exception $e) {
        echo json_encode(['status'=>0,'msg'=>$e->getMessage()]);
    }
    exit;
}


// Get Quizzes - FIXED VERSION
if ($url === 'getQuizzes') {
    try {
        $subjectId   = (int)($_GET['subjectId']   ?? 0);
        $examboardId = (int)($_GET['examboardId'] ?? 0);
        $tierId      = (int)($_GET['tierId']      ?? 0);
        $yearId      = (int)($_GET['yearId']      ?? 0);

        error_log("getQuizzes called with subjectId: $subjectId, examboardId: $examboardId, tierId: $tierId, yearId: $yearId");

        if ($subjectId < 1) {
            echo json_encode(['status' => 0, 'msg' => 'Missing subjectId']);
            exit;
        }

        // 1) Get topics for this subject
        $stmt = $pdo->prepare("SELECT id FROM course_topics WHERE subject_id = ?");
        $stmt->execute([$subjectId]);
        $topicRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("Found " . count($topicRows) . " topics for subject $subjectId");

        if (empty($topicRows)) {
            echo json_encode(['status' => 1, 'data' => []]);
            exit;
        }

        // 2) Get subtopics under those topics (these are your "quizzes")
        $topicIds = array_map(fn($r) => (int)$r['id'], $topicRows);
        $placeholders = implode(',', array_fill(0, count($topicIds), '?'));
        
        $sql = "SELECT id, topic AS name 
                FROM course_topics 
                WHERE parent_id IN ($placeholders) 
                ORDER BY topic ASC";
        
        error_log("Subtopic SQL: $sql with topicIds: " . json_encode($topicIds));
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($topicIds);
        $subs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("Found " . count($subs) . " subtopics");

        // Format the output
        $out = array_map(function($r) {
            return ['id' => (int)$r['id'], 'name' => $r['name']];
        }, $subs);

        echo json_encode(['status' => 1, 'data' => $out]);
        
    } catch (Exception $e) {
        error_log("Error in getQuizzes: " . $e->getMessage());
        echo json_encode(['status' => 0, 'msg' => $e->getMessage()]);
    }
    exit;
}

// Unknown endpoint
echo json_encode(['status' => 0, 'msg' => 'Unknown URL: ' . $url]);
exit;