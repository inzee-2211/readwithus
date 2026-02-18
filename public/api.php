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

// ---------- helpers ----------
function rowsOrEmpty(PDOStatement $stmt) {
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return ['status' => 1, 'data' => $rows ?: []];
}

// ---------- SUBJECTS (by level) ----------
if ($url === 'getSubjects') {
    try {
        $levelId = (int)($_GET['levelId'] ?? 0);
        if ($levelId < 1) { echo json_encode(['status'=>0,'error'=>'Missing levelId']); exit; }

        $sql = "
            SELECT DISTINCT s.id, s.subject AS name
            FROM tbl_quiz_setup q
            JOIN course_subjects s ON s.id = q.subject_id
            WHERE q.level_id = ?
            ORDER BY s.subject ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$levelId]);
        echo json_encode(rowsOrEmpty($stmt));
    } catch (Exception $e) {
        echo json_encode(['status'=>0,'msg'=>$e->getMessage()]);
    }
    exit;
}

// ---------- EXAM BOARDS (by level+subject) ----------
if ($url === 'getExamboards') {
    try {
        $levelId   = (int)($_GET['levelId'] ?? 0);     // GC(S)E will send this too
        $subjectId = (int)($_GET['subjectId'] ?? 0);
        if ($subjectId < 1) { echo json_encode(['status'=>0,'error'=>'Missing subjectId']); exit; }

        $params = [$subjectId];
        $where  = "q.subject_id = ?";
        if ($levelId > 0) { $where .= " AND q.level_id = ?"; $params[] = $levelId; }

        $sql = "
            SELECT DISTINCT e.id, e.name
            FROM tbl_quiz_setup q
            JOIN course_examboards e ON e.id = q.examboard_id
            WHERE $where
            ORDER BY e.name ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(rowsOrEmpty($stmt));
    } catch (Exception $e) {
        echo json_encode(['status'=>0,'msg'=>$e->getMessage()]);
    }
    exit;
}

// ---------- TIERS (by examboard) ----------
if ($url === 'getTiers') {
    try {
        $examboardId = (int)($_GET['examboardId'] ?? 0);
        if ($examboardId < 1) { echo json_encode(['status'=>0,'error'=>'Missing examboardId']); exit; }

        $sql = "
            SELECT DISTINCT t.id, t.name
            FROM tbl_quiz_setup q
            JOIN course_tier t ON t.id = q.tier_id
            WHERE q.examboard_id = ?
            ORDER BY t.name ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$examboardId]);
        echo json_encode(rowsOrEmpty($stmt));
    } catch (Exception $e) {
        echo json_encode(['status'=>0,'msg'=>$e->getMessage()]);
    }
    exit;
}

// ---------- YEARS (works for both GCSE and non-GCSE) ----------
if ($url === 'getYears') {
    try {
        $subjectId   = (int)($_GET['subjectId'] ?? 0);
        $levelId     = (int)($_GET['levelId'] ?? 0);
        $examboardId = (int)($_GET['examboardId'] ?? 0); // may be 0 for non-GCSE
        $tierId      = (int)($_GET['tierId'] ?? 0);      // may be 0 for non-GCSE

        $where  = [];
        $params = [];
        if ($subjectId > 0)   { $where[] = "q.subject_id = ?";   $params[] = $subjectId; }
        if ($levelId > 0)     { $where[] = "q.level_id = ?";     $params[] = $levelId; }
        if ($examboardId > 0) { $where[] = "q.examboard_id = ?"; $params[] = $examboardId; }
        if ($tierId > 0)      { $where[] = "q.tier_id = ?";      $params[] = $tierId; }

        $whereSql = $where ? ("WHERE " . implode(' AND ', $where)) : "";

        $sql = "
            SELECT DISTINCT y.id, y.name
            FROM tbl_quiz_setup q
            JOIN course_year y ON y.id = q.year_id
            $whereSql
            ORDER BY y.name ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(rowsOrEmpty($stmt));
    } catch (Exception $e) {
        echo json_encode(['status'=>0,'msg'=>$e->getMessage()]);
    }
    exit;
}

// ---------- RESOLVE a setup row id ----------
// if ($url === 'resolveSetup') {
//     try {
//         $levelId     = (int)($_GET['levelId'] ?? 0);
//         $subjectId   = (int)($_GET['subjectId'] ?? 0);
//         $examboardId = (int)($_GET['examboardId'] ?? 0); // 0 for non-GCSE
//         $tierId      = (int)($_GET['tierId'] ?? 0);      // 0 for non-GCSE
//         $yearId      = (int)($_GET['yearId'] ?? 0);

//         if ($levelId < 1 || $subjectId < 1 || $yearId < 1) {
//             echo json_encode(['status'=>0,'msg'=>'Missing required selections']); exit;
//         }

//         $where  = ["level_id = ?", "subject_id = ?", "year_id = ?"];
//         $params = [$levelId, $subjectId, $yearId];

//         // Only constrain examboard/tier when provided (GCSE)
//         if ($examboardId > 0) { $where[] = "examboard_id = ?"; $params[] = $examboardId; }
//         if ($tierId > 0)      { $where[] = "tier_id = ?";      $params[] = $tierId; }

//         $sql = "SELECT id FROM tbl_quiz_setup WHERE " . implode(' AND ', $where) . " LIMIT 1";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute($params);
//         $row = $stmt->fetch(PDO::FETCH_ASSOC);

//         echo json_encode(['status'=>1, 'data'=>['setup_id' => (int)($row['id'] ?? 0)]]);
//     } catch (Exception $e) {
//         echo json_encode(['status'=>0,'msg'=>$e->getMessage()]);
//     }
//     exit;
// }
// ---------- RESOLVE all setup row ids for a given path ----------
// if ($url === 'resolveSetup') {
//     try {
//         $levelId     = (int)($_GET['levelId'] ?? 0);
//         $subjectId   = (int)($_GET['subjectId'] ?? 0);
//         $examboardId = (int)($_GET['examboardId'] ?? 0); // 0 for non-GCSE
//         $tierId      = (int)($_GET['tierId'] ?? 0);      // 0 for non-GCSE
//         $yearId      = (int)($_GET['yearId'] ?? 0);

//         if ($levelId < 1 || $subjectId < 1 || $yearId < 1) {
//             echo json_encode(['status' => 0, 'msg' => 'Missing required selections']);
//             exit;
//         }

//         $where  = ["level_id = ?", "subject_id = ?", "year_id = ?"];
//         $params = [$levelId, $subjectId, $yearId];

//         // Only constrain examboard/tier when provided (GCSE)
//         if ($examboardId > 0) {
//             $where[]  = "examboard_id = ?";
//             $params[] = $examboardId;
//         }
//         if ($tierId > 0) {
//             $where[]  = "tier_id = ?";
//             $params[] = $tierId;
//         }

//         // NOTE: no LIMIT 1 – we want ALL matching setup rows
//         $sql = "SELECT id FROM tbl_quiz_setup WHERE " . implode(' AND ', $where) . " ORDER BY id ASC";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute($params);
//         $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

//         if (empty($rows)) {
//             echo json_encode(['status' => 0, 'msg' => 'No topics found']);
//             exit;
//         }

//         $ids = array_map('intval', array_column($rows, 'id'));

//         echo json_encode([
//             'status' => 1,
//             'data'   => ['setup_ids' => $ids],
//         ]);
//     } catch (Exception $e) {
//         echo json_encode(['status' => 0, 'msg' => $e->getMessage()]);
//     }
//     exit;
// }
// ---------- RESOLVE all setup row ids for a given path ----------
if ($url === 'resolveSetup') {
    try {
        $levelId     = (int)($_GET['levelId'] ?? 0);
        $subjectId   = (int)($_GET['subjectId'] ?? 0);
        $examboardId = (int)($_GET['examboardId'] ?? 0); // 0 for non-GCSE
        $tierId      = (int)($_GET['tierId'] ?? 0);      // 0 for non-GCSE
        $yearId      = (int)($_GET['yearId'] ?? 0);      // 0 when not provided

        if ($levelId < 1 || $subjectId < 1) {
            echo json_encode(['status' => 0, 'msg' => 'Missing levelId/subjectId']);
            exit;
        }

        // 🔍 Look up level name to know if this is GCSE
        $stmtLevel = $pdo->prepare("SELECT level_name FROM course_levels WHERE id = ?");
        $stmtLevel->execute([$levelId]);
        $levelName = $stmtLevel->fetchColumn();
        $levelSlug = strtoupper(trim((string)$levelName));

        // GCSE has NO years; all other levels do
        $requiresYear = ($levelSlug !== 'GCSE');

        if ($requiresYear && $yearId < 1) {
            echo json_encode(['status' => 0, 'msg' => 'Missing yearId for non-GCSE level']);
            exit;
        }

        // Base WHERE: always level + subject
        $where  = ["level_id = ?", "subject_id = ?"];
        $params = [$levelId, $subjectId];

        // Only include year filter for non-GCSE levels
        if ($requiresYear) {
            $where[]  = "year_id = ?";
            $params[] = $yearId;
        }

        // Only constrain examboard/tier when provided (GCSE path)
        if ($examboardId > 0) {
            $where[]  = "examboard_id = ?";
            $params[] = $examboardId;
        }
        if ($tierId > 0) {
            $where[]  = "tier_id = ?";
            $params[] = $tierId;
        }

        // NOTE: no LIMIT 1 – we want ALL matching setup rows
        $sql = "SELECT id FROM tbl_quiz_setup WHERE " . implode(' AND ', $where) . " ORDER BY id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            echo json_encode(['status' => 0, 'msg' => 'No topics found']);
            exit;
        }

        $ids = array_map('intval', array_column($rows, 'id'));

        echo json_encode([
            'status' => 1,
            'data'   => ['setup_ids' => $ids],
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 0, 'msg' => $e->getMessage()]);
    }
    exit;
}





// if ($url === 'getQuizzesBySubtopic') {
//     try {
//         $subtopicId = (int)($_GET['subtopicId'] ?? 0);
//         if ($subtopicId < 1) { echo json_encode(['status'=>0,'error'=>'Missing subtopicId']); exit; }

//         // fetch subtopic text
//         $txt = $pdo->prepare("SELECT topic FROM course_topics WHERE id = ?");
//         $txt->execute([$subtopicId]);
//         $subtopicRow = $txt->fetch(PDO::FETCH_ASSOC);
//         if (!$subtopicRow) { echo json_encode(['status'=>1,'data'=>[]]); exit; }

//         $stmt = $pdo->prepare("
//             SELECT id, question_title AS name
//             FROM tbl_quaestion_bank
//             WHERE subtopic = ?
//             ORDER BY id ASC
//         ");
//         $stmt->execute([$subtopicRow['topic']]);
//         echo json_encode(['status'=>1,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
//     } catch (Exception $e) {
//         echo json_encode(['status'=>0,'msg'=>$e->getMessage()]);
//     }
//     exit;
// }
if ($url === 'getQuizzesBySubtopic') {
    try {
        $subtopicId = (int)($_GET['subtopicId'] ?? 0);
        if ($subtopicId < 1) {
            echo json_encode(['status'=>0,'error'=>'Missing subtopicId']);
            exit;
        }

        // ✅ NEW: get subtopic_name from tbl_quiz_management (NOT course_topics)
        $txt = $pdo->prepare("SELECT subtopic_name FROM tbl_quiz_management WHERE id = ?");
        $txt->execute([$subtopicId]);
        $subtopicName = $txt->fetchColumn();

        if (!$subtopicName) {
            echo json_encode(['status'=>1,'data'=>[]]);
            exit;
        }

        // ✅ Fetch question-bank quizzes using the subtopic name
        // NOTE: confirm your table name: tbl_quaestion_bank looks misspelled
        $stmt = $pdo->prepare("
            SELECT id, question_title AS name
            FROM tbl_quaestion_bank
            WHERE subtopic = ?
            ORDER BY id ASC
        ");
        $stmt->execute([$subtopicName]);

        echo json_encode(['status'=>1,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (Exception $e) {
        echo json_encode(['status'=>0,'msg'=>$e->getMessage()]);
    }
    exit;
}

// NEW: Get Topics for a Subject (top level topics only)
// TOPICS (actually the "setup" rows filtered by the chosen path)
if ($url === 'getTopics') {
    try {
        $levelId     = (int)($_GET['levelId'] ?? 0);
        $subjectId   = (int)($_GET['subjectId'] ?? 0);
        $examboardId = (int)($_GET['examboardId'] ?? 0); // optional
        $tierId      = (int)($_GET['tierId'] ?? 0);      // optional
        $yearId      = (int)($_GET['yearId'] ?? 0);      // may be 0 for GCSE

        if ($levelId < 1 || $subjectId < 1) {
            echo json_encode(['status' => 0, 'msg' => 'Missing levelId/subjectId']);
            exit;
        }

        // 🔍 detect level name to see if this is GCSE
        $stmtLevel = $pdo->prepare("SELECT level_name FROM course_levels WHERE id = ?");
        $stmtLevel->execute([$levelId]);
        $levelName = $stmtLevel->fetchColumn();
        $levelSlug = strtoupper(trim((string)$levelName));

        // GCSE: year is OPTIONAL; other levels: year is REQUIRED
        $requiresYear = ($levelSlug !== 'GCSE');

        if ($requiresYear && $yearId < 1) {
            echo json_encode(['status' => 0, 'msg' => 'Missing yearId for non-GCSE level']);
            exit;
        }

        // Base filters
        $where  = ["level_id = ?", "subject_id = ?"];
        $params = [$levelId, $subjectId];

        // Only add year filter for non-GCSE
        if ($requiresYear) {
            $where[]  = "year_id = ?";
            $params[] = $yearId;
        }

        // Optional filters
        if ($examboardId > 0) {
            $where[]  = "examboard_id = ?";
            $params[] = $examboardId;
        }
        if ($tierId > 0) {
            $where[]  = "tier_id = ?";
            $params[] = $tierId;
        }

        $sql = "
            SELECT id, topic_name AS name
            FROM tbl_quiz_setup
            WHERE " . implode(' AND ', $where) . "
            ORDER BY topic_name ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 1, 'data' => $rows ?: []]);
    } catch (Exception $e) {
        echo json_encode(['status' => 0, 'msg' => $e->getMessage()]);
    }
    exit;
}
// ---------- SUBTOPICS (by quiz_setup_id) ----------
if ($url === 'getSubtopics') {
    try {
        $setupId = (int)($_GET['setupId'] ?? 0);
        error_log("getSubtopics called with setupId=$setupId");

        if ($setupId < 1) {
            echo json_encode(['status' => 0, 'msg' => 'Missing setupId']);
            exit;
        }

        $sql = "
            SELECT id, subtopic_name AS name
            FROM tbl_quiz_management
            WHERE quiz_setup_id = ?
            ORDER BY position ASC, subtopic_name ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$setupId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log('getSubtopics('.$setupId.') found '.count($rows).' rows');

        echo json_encode([
            'status' => 1,
            'data'   => $rows ?: [],
        ]);
    } catch (Exception $e) {
        error_log("getSubtopics error: " . $e->getMessage());
        echo json_encode(['status' => 0, 'msg' => $e->getMessage()]);
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