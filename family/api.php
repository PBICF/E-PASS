<?php
require_once 'database.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
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

        $db->from('FAMILY')
            ->where('EMPNO', $empno)
            ->where('FSLNO', $fslno)
            ->set('NAME', $name)
            ->set('FRELATION', $frelation)
            ->setRaw('DB', "TO_DATE('$db_val', 'DD/MM/YYYY')")
            ->set('FALLOWED', $fallowed)
            ->update();

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

        echo json_encode(['success' => 'Member added successfully', 'code' => 200]);
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Action not found']);
        break;
}

