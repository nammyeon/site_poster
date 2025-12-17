<?php
include_once('./_common.php');

// 관리자 권한 체크 (최고 관리자만 삭제 가능)
if (!$member['mb_id'] || !$is_admin || $member['mb_level'] < 10) {
    echo 'unauthorized';
    exit;
}

// POST 데이터 받기
$bo_table = isset($_POST['bo_table']) ? clean_xss_tags($_POST['bo_table']) : '';
$wr_id = isset($_POST['wr_id']) ? (int)$_POST['wr_id'] : 0;

// 유효성 검사
if (!$bo_table || !$wr_id) {
    echo 'invalid_data';
    exit;
}

// 허용된 테이블인지 확인
if ($bo_table !== 'poster_save') {
    echo 'invalid_table';
    exit;
}

// 테이블 이름 생성
$write_table = $g5['write_prefix'] . $bo_table;

// 테이블 존재 여부 확인
$table_check = sql_query("SHOW TABLES LIKE '{$write_table}'");
if (!sql_num_rows($table_check)) {
    echo 'table_not_found';
    exit;
}

// 게시글 존재 여부 및 정보 확인
$check_sql = "SELECT wr_id, wr_subject, wr_name FROM {$write_table} WHERE wr_id = {$wr_id}";
$check_result = sql_fetch($check_sql);
if (!$check_result) {
    echo 'post_not_found';
    exit;
}

// 트랜잭션 시작
sql_query("START TRANSACTION");

try {
    // 첨부파일이 있다면 삭제 (파일 시스템에서)
    $file_sql = "SELECT * FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = {$wr_id}";
    $file_result = sql_query($file_sql);
    
    while ($file_row = sql_fetch_array($file_result)) {
        $file_path = G5_DATA_PATH . '/file/' . $bo_table . '/' . $file_row['bf_file'];
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
    }
    
    // 첨부파일 테이블에서 삭제
    $delete_file_sql = "DELETE FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = {$wr_id}";
    sql_query($delete_file_sql);
    
    // 게시글 삭제
    $delete_sql = "DELETE FROM {$write_table} WHERE wr_id = {$wr_id}";
    $delete_result = sql_query($delete_sql);
    
    if (!$delete_result) {
        throw new Exception('게시글 삭제 실패');
    }
    
    // 댓글이 있다면 삭제 (댓글 테이블이 있는 경우)
    $comment_table = $g5['write_prefix'] . $bo_table . '_comment';
    $comment_check = sql_query("SHOW TABLES LIKE '{$comment_table}'");
    if (sql_num_rows($comment_check)) {
        $delete_comment_sql = "DELETE FROM {$comment_table} WHERE wr_parent = {$wr_id}";
        sql_query($delete_comment_sql);
    }
    
    // 트랜잭션 커밋
    sql_query("COMMIT");
    
    echo 'success';
    
} catch (Exception $e) {
    // 트랜잭션 롤백
    sql_query("ROLLBACK");
    echo 'delete_failed: ' . $e->getMessage();
}
?>