<?php
include_once('./_common.php');

// 관리자 권한 체크
if (!$member['mb_id'] || !$is_admin) {
    echo 'unauthorized';
    exit;
}

// POST 데이터 받기
$bo_table = isset($_POST['bo_table']) ? clean_xss_tags($_POST['bo_table']) : '';
$wr_id = isset($_POST['wr_id']) ? (int)$_POST['wr_id'] : 0;
$status = isset($_POST['status']) ? clean_xss_tags($_POST['status']) : '';

// 유효성 검사
if (!$bo_table || !$wr_id || !$status) {
    echo 'invalid_data';
    exit;
}

// 허용된 상태값인지 확인
$allowed_status = ['접수', '작업중', '작업완료', '송출중'];
if (!in_array($status, $allowed_status)) {
    echo 'invalid_status';
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

// 게시글 존재 여부 확인
$check_sql = "SELECT wr_id FROM {$write_table} WHERE wr_id = {$wr_id}";
$check_result = sql_fetch($check_sql);
if (!$check_result) {
    echo 'post_not_found';
    exit;
}

// 상태 업데이트
$update_sql = "UPDATE {$write_table} SET wr_10 = '{$status}' WHERE wr_id = {$wr_id}";
$result = sql_query($update_sql);

if ($result) {
    echo 'success';
} else {
    echo 'update_failed';
}
?>