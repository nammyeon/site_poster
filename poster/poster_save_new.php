<?php
include_once("./_common.php");

// Permission check
if(!$is_member) { 
    alert('글을 쓸 권한이 없습니다.');
    exit;
}

// 기본 데이터 수집
$mb_id = isset($member['mb_id']) ? clean_xss_tags($member['mb_id']) : '';
$mb_nick = isset($member['mb_nick']) ? clean_xss_tags($member['mb_nick']) : '';
$design_type = isset($_POST['design_type']) ? trim(clean_xss_tags($_POST['design_type'])) : '';
$design = isset($_POST['design']) ? trim(clean_xss_tags($_POST['design'])) : '';

// 포스터 타입별 필드 설정
$poster_field_configs = [
    'A' => ['field1', 'field2', 'field3'],
    'B' => ['field1', 'field2', 'field3'], 
    'C' => ['field1', 'field2', 'field3'],
    'D' => ['field1', 'field2', 'field3'],
    'E' => ['field1', 'field2', 'field3'],
    'F' => ['field1', 'field2', 'field3'],
    'G' => ['field1', 'field2', 'field3'],
    'H' => ['field1', 'field2', 'field3'],
    'I' => ['field1', 'field2', 'field3', 'field4', 'field5', 'field6', 'field7', 'field8', 'field9', 'field10'],
    'J' => ['field1', 'field2', 'field3'],
    'K' => ['field1'],
    'L' => ['field1'],
	'M' => ['field1', 'field2'],
	'N' => ['field1'],
    'O' => ['field1', 'field2', 'field3'],
	'P' => ['field1'],
	'Q' => ['field1'],
	'R' => ['field1', 'field2', 'field3'],
    'S' => ['field1', 'field2', 'field3'],
	'T' => ['field1', 'field2', 'field3'],
	'U' => ['field1', 'field2'],
    'V' => ['field1', 'field2', 'field3'],
    'W' => ['field1', 'field2', 'field3'],
    'X' => ['field1', 'field2', 'field3'],
];

// 필수 필드 체크
$required_field_configs = [
    'A' => ['field1', 'field2', 'field3'],
    'B' => ['field1', 'field2', 'field3'],
    'C' => ['field1', 'field2', 'field3'],
    'D' => ['field1', 'field2', 'field3'],
    'E' => ['field1', 'field2'],
    'F' => ['field1', 'field2'],
    'G' => ['field1', 'field2'],
    'H' => ['field1', 'field2'],
    'I' => ['field1', 'field2', 'field10'],
    'J' => ['field1', 'field2'],
    'K' => ['field1'],
	'L' => ['field1'],
	'M' => ['field1', 'field2'],
	'N' => ['field1'],
	'O' => ['field1', 'field2', 'field3'],
	'P' => ['field1'],
	'Q' => ['field1'],
	'R' => ['field1', 'field2', 'field3'],
    'S' => ['field1', 'field2', 'field3'],
	'T' => ['field1', 'field2', 'field3'],
	'U' => ['field1', 'field2'],
    'V' => ['field1', 'field2', 'field3'],
    'W' => ['field1', 'field2', 'field3'],
    'X' => ['field1', 'field2', 'field3'],
];

$msg = array();
if (empty($design)) $msg[] = '<strong>디자인</strong>을 입력하세요.';

// 타입별 필수 필드 체크
if (isset($required_field_configs[$design_type])) {
    foreach ($required_field_configs[$design_type] as $field) {
        if (empty($_POST[$field])) {
            $field_names = [
                'field1' => '첫 번째 필드',
                'field2' => '두 번째 필드',
                'field3' => '세 번째 필드',
                'field10' => '약국명'
            ];
            $msg[] = '<strong>' . ($field_names[$field] ?? $field) . '</strong>을 입력하세요.';
        }
    }
}

$msg = implode('<br>', $msg);
if ($msg) {
    alert($msg);
    exit;
}

// 데이터 정제 및 수집
$processed_data = [];
if (isset($poster_field_configs[$design_type])) {
    foreach ($poster_field_configs[$design_type] as $field) {
        if (isset($_POST[$field])) {
            $processed_data[$field] = get_text(htmlspecialchars($_POST[$field], ENT_QUOTES));
        } else {
            $processed_data[$field] = '';
        }
    }
}

// 타입별 제목 및 내용 생성
if ($design_type === 'I') {
    $wr_subject = ($processed_data['field10'] ?? '') . " / Type: " . $design_type . " / Num: " . $design . " / " . ($processed_data['field2'] ?? '');
    $content_summary = "design_type: " . $design_type . " / design: " . $design;
    foreach ($processed_data as $key => $value) {
        $content_summary .= " / {$key}: " . $value;
    }
} else {
    // 일반 포스터 타입
    $main_title = $processed_data['field1'] ?? '';
    $wr_subject = $main_title . " / Type: " . $design_type . " / Num: " . $design;
    $content_summary = "design_type: " . $design_type . " / design: " . $design;
    foreach ($processed_data as $key => $value) {
        $content_summary .= " / {$key}: " . $value;
    }
}

// SEO 제목 생성 함수 (기본 함수가 없는 경우)
if (!function_exists('generate_seo_title')) {
    function generate_seo_title($title) {
        return strip_tags($title);
    }
}

// 타입별 데이터베이스 필드 매핑
if ($design_type === 'I') {
    // Type I 전용 매핑
    $sql = "INSERT INTO wd_write_poster_save 
            SET wr_num = 0,
                wr_reply = '',
                wr_comment = 0,
                wr_option = 'html1',
                wr_subject = '" . sql_real_escape_string($wr_subject) . "',
                wr_content = '" . sql_real_escape_string($content_summary) . "',
                wr_seo_title = '" . sql_real_escape_string(generate_seo_title($wr_subject)) . "',
                wr_link1 = '" . sql_real_escape_string($processed_data['field8'] ?? '') . "',
                wr_link2 = '" . sql_real_escape_string($processed_data['field9'] ?? '') . "',
                wr_hit = 0,
                wr_good = 0,
                wr_nogood = 0,
                mb_id = '" . sql_real_escape_string($mb_id) . "',
                wr_password = '',
                wr_name = '" . sql_real_escape_string($mb_nick) . "',
                wr_email = '',
                wr_homepage = '" . sql_real_escape_string($processed_data['field10'] ?? '') . "',
                wr_datetime = '" . G5_TIME_YMDHIS . "',
                wr_last = '" . G5_TIME_YMDHIS . "',
                wr_ip = '" . sql_real_escape_string($_SERVER['REMOTE_ADDR']) . "',
                wr_1 = '" . sql_real_escape_string($design) . "',
                wr_2 = '" . sql_real_escape_string($processed_data['field1'] ?? '') . "',
                wr_3 = '" . sql_real_escape_string($processed_data['field2'] ?? '') . "',
                wr_4 = '" . sql_real_escape_string($processed_data['field3'] ?? '') . "',
                wr_5 = '" . sql_real_escape_string($processed_data['field4'] ?? '') . "',
                wr_6 = '" . sql_real_escape_string($processed_data['field5'] ?? '') . "',
                wr_7 = '" . sql_real_escape_string($design_type) . "',
                wr_8 = '" . sql_real_escape_string($processed_data['field6'] ?? '') . "',
                wr_9 = '" . sql_real_escape_string($processed_data['field7'] ?? '') . "',
                wr_10 = '',
                wr_11 = '',
                wr_12 = ''";
} else {
    // 일반 포스터 타입
    $sql = "INSERT INTO wd_write_poster_save 
            SET wr_num = 0,
                wr_reply = '',
                wr_comment = 0,
                wr_option = 'html1',
                wr_subject = '" . sql_real_escape_string($wr_subject) . "',
                wr_content = '" . sql_real_escape_string($content_summary) . "',
                wr_seo_title = '" . sql_real_escape_string(generate_seo_title($wr_subject)) . "',
                wr_link1 = '',
                wr_link2 = '',
                wr_hit = 0,
                wr_good = 0,
                wr_nogood = 0,
                mb_id = '" . sql_real_escape_string($mb_id) . "',
                wr_password = '',
                wr_name = '" . sql_real_escape_string($mb_nick) . "',
                wr_email = '',
                wr_homepage = '',
                wr_datetime = '" . G5_TIME_YMDHIS . "',
                wr_last = '" . G5_TIME_YMDHIS . "',
                wr_ip = '" . sql_real_escape_string($_SERVER['REMOTE_ADDR']) . "',
                wr_1 = '" . sql_real_escape_string($design) . "',
                wr_2 = '" . sql_real_escape_string($processed_data['field1'] ?? '') . "',
                wr_3 = '" . sql_real_escape_string($processed_data['field2'] ?? '') . "',
                wr_4 = '" . sql_real_escape_string($processed_data['field3'] ?? '') . "',
                wr_5 = '',
                wr_6 = '',
                wr_7 = '" . sql_real_escape_string($design_type) . "',
                wr_8 = '',
                wr_9 = '',
                wr_10 = '',
                wr_11 = '',
                wr_12 = ''";
}

$result = sql_query($sql);

if ($result) {
    $wr_id = sql_insert_id();

	// wr_num 업데이트 (그누보드 기본 방식과 동일)
	$sql_num = "SELECT IFNULL(MIN(wr_num) - 1, -1) as next_num FROM wd_write_poster_save";
	$row_num = sql_fetch($sql_num);
	$next_num = $row_num['next_num'];
	sql_query("UPDATE wd_write_poster_save SET wr_num = '{$next_num}' WHERE wr_id = '{$wr_id}'");

    // 부모 아이디 업데이트
    sql_query("UPDATE wd_write_poster_save SET wr_parent = '$wr_id' WHERE wr_id = '$wr_id'");

    // 새글 삽입
    sql_query("INSERT INTO {$g5['board_new_table']} (bo_table, wr_id, wr_parent, bn_datetime, mb_id) VALUES ('poster_save', '{$wr_id}', '{$wr_id}', '" . G5_TIME_YMDHIS . "', '" . sql_real_escape_string($mb_id) . "')");

    // 게시글 수 증가
    sql_query("UPDATE {$g5['board_table']} SET bo_count_write = bo_count_write + 1 WHERE bo_table = 'poster_save'");

    alert("포스터가 성공적으로 저장되었습니다.", './poster_history.php');
} else {
    error_log("포스터 저장 실패: " . sql_error_info());
    alert("저장 중 오류가 발생했습니다. 다시 시도해 주세요.");
    exit;
}

// 기본 alert 함수
if (!function_exists('alert')) {
    function alert($msg, $url = '') {
        $msg = addslashes($msg);
        if ($url) {
            echo "<script>alert('$msg'); location.href='$url';</script>";
        } else {
            echo "<script>alert('$msg'); history.back();</script>";
        }
        exit;
    }
}
?>