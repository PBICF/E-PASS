<?php
session_start();
if (!isset($_SESSION['family_logged_in']) || $_SESSION['family_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}
require_once 'database.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
try {
    $db = Database::getInstance();

    switch ($action) {
        case 'relationships':
            $relationships = $db->from('RELMR')
                ->select('RELCODE, RELNAME')
                ->orderBy('RELNAME')
                ->get();
            echo json_encode(['relationships' => $relationships]);
            break;

        case 'inquire':
            $empno = $_POST['empno'] ?? '';
            if (empty($empno) || !is_numeric($empno)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid Employee Number']);
                exit;
            }

            $empno = (int) $empno;
            
            // Fetch Family
            $family = $db->from('FAMILY')
                ->select("FAMILY.*, RELMR.RELNAME as RELATION, TO_CHAR(DB, 'DD/MM/YYYY') AS DB")
                ->leftJoin('RELMR', 'RELMR.RELCODE = FAMILY.FRELATION')
                ->where('EMPNO', $empno)
                ->orderBy('FSLNO')
                ->get();

            echo json_encode(['family' => $family, 'code' => 200]);
            break;

        case 'update':
            $empno = (int) ($_POST['empno'] ?? 0);
            $fslno = (int) ($_POST['fslno'] ?? 0);
            $name = $_POST['name'] ?? '';
            $frelation = $_POST['frelation'] ?? '';
            $db_val = $_POST['db'] ?? '';
            $fallowed = $_POST['fallowed'] ?? 'N';

            if (!$empno || !$fslno || !$name || !$frelation) {
                http_response_code(400);
                echo json_encode(['error' => 'Required fields missing']);
                exit;
            }

            // Get old data for audit
            $old_row = $db->from('FAMILY')
                ->select("NAME, FRELATION, TO_CHAR(DB, 'DD/MM/YYYY') as DB, FALLOWED")
                ->where('EMPNO', $empno)
                ->where('FSLNO', $fslno)
                ->row();

            $db->from('FAMILY')
                ->where('EMPNO', $empno)
                ->where('FSLNO', $fslno)
                ->set('NAME', $name)
                ->set('FRELATION', $frelation)
                ->setRaw('DB', "TO_DATE('$db_val', 'DD/MM/YYYY')")
                ->set('FALLOWED', $fallowed)
                ->update();

            // Audit Log
            if ($old_row) {
                $audit_sql = "INSERT INTO FAMILY_AUDIT (
                    EMPNO, FSLNO, ACTION, 
                    OLD_NAME, NEW_NAME, 
                    OLD_REL, NEW_REL, 
                    OLD_DB, NEW_DB, 
                    OLD_ALLOWED, NEW_ALLOWED, 
                    CHANGED_BY
                ) VALUES (?, ?, 'UPDATE', ?, ?, ?, ?, TO_DATE(?, 'DD/MM/YYYY'), TO_DATE(?, 'DD/MM/YYYY'), ?, ?, ?)";
                
                $db->query($audit_sql, [
                    $empno, $fslno, 
                    $old_row->name, $name,
                    $old_row->frelation, $frelation,
                    $old_row->db, $db_val,
                    $old_row->fallowed, $fallowed,
                    $_SESSION['username'] ?? 'SYSTEM'
                ]);
            }

            echo json_encode(['success' => 'Record updated successfully', 'code' => 200]);
            break;

        case 'add':
            $empno = (int) ($_POST['empno'] ?? 0);
            $name = $_POST['name'] ?? '';
            $frelation = $_POST['frelation'] ?? '';
            $db_val = $_POST['db'] ?? '';
            $fallowed = $_POST['fallowed'] ?? 'Y';

            if (!$empno || !$name || !$frelation) {
                http_response_code(400);
                echo json_encode(['error' => 'Name and Relationship are required']);
                exit;
            }

            // Get Next FSLNO
            $max_row = $db->from('FAMILY')
                ->select('MAX(FSLNO) as max_sl')
                ->where('EMPNO', $empno)
                ->row();
            
            $next_sl = ($max_row->max_sl ?? 0) + 1;

            // Use raw query for insert to handle TO_DATE
            $sql = "INSERT INTO FAMILY (EMPNO, FSLNO, NAME, FRELATION, DB, FALLOWED) 
                    VALUES (?, ?, ?, ?, TO_DATE(?, 'DD/MM/YYYY'), ?)";
            
            $db->query($sql, [$empno, $next_sl, $name, $frelation, $db_val, $fallowed]);

            // Audit Log
            $audit_sql = "INSERT INTO FAMILY_AUDIT (
                EMPNO, FSLNO, ACTION, 
                NEW_NAME, NEW_REL, NEW_DB, NEW_ALLOWED, 
                CHANGED_BY
            ) VALUES (?, ?, 'INSERT', ?, ?, TO_DATE(?, 'DD/MM/YYYY'), ?, ?)";

            $db->query($audit_sql, [
                $empno, $next_sl, 
                $name, $frelation, $db_val, $fallowed,
                $_SESSION['username'] ?? 'SYSTEM'
            ]);

            echo json_encode(['success' => 'Member added successfully', 'code' => 200]);
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Action not found']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}


