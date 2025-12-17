<?php
include_once("./_common.php");

// 데이터 수집 및 정제
$mb_id = isset($member['mb_id']) ? clean_xss_tags($member['mb_id']) : '';
$mb_nick = isset($member['mb_nick']) ? clean_xss_tags($member['mb_nick']) : '';
$design_type = isset($_POST['design_type']) ? trim(clean_xss_tags($_POST['design_type'])) : '';
$design = isset($_POST['design']) ? trim(clean_xss_tags($_POST['design'])) : '';
$name = isset($_POST['name']) ? get_text(htmlspecialchars($_POST['name'], ENT_QUOTES)) : '';
$raw_hours = isset($_POST['hours']) ? $_POST['hours'] : '';
$hours = get_text(htmlspecialchars($raw_hours, ENT_QUOTES)); // HTML 특수문자 처리 및 텍스트 정제
$contact = isset($_POST['contact']) ? trim(clean_xss_tags($_POST['contact'])) : '';
$name_color = isset($_POST['nameColor']) ? trim(clean_xss_tags($_POST['nameColor'])) : ''; // First color (A)
$hours_color = isset($_POST['hoursColor']) ? trim(clean_xss_tags($_POST['hoursColor'])) : ''; // Second color (C)
// No longer using name_size, so remove it
//$name_size = isset($_POST['nameSize']) ? trim(clean_xss_tags($_POST['nameSize'])) : '';

// 필수 입력값 체크
$msg = array();
if (empty($design)) $msg[] = '<strong>디자인</strong>을 입력하세요.';
if (empty($name)) $msg[] = '<strong>이름</strong>을 입력하세요.';
if (empty($hours)) $msg[] = '<strong>시간</strong>을 입력하세요.';
if (empty($contact)) $msg[] = '<strong>연락처</strong>을 입력하세요.';

$msg = implode('<br>', $msg);
if ($msg) {
    alert($msg);
    exit;
}

$wr_subject = "Poster Save - " . $name . " / Type: " . $design_type . " / Num: " . $design;
$content_summary = "<table style=\"border-collapse: collapse; margin-bottom: 20px; font-family: sans-serif;\">";
$content_summary .= "  <caption style=\"font-size: 1.2em; font-weight: bold; margin-bottom: 10px; color: #555;\">포스터 저장 내역</caption>";
$content_summary .= "  <thead style=\"background-color: #f5f5f5; font-weight: bold; color: #333;\">";
$content_summary .= "    <tr>";
$content_summary .= "      <th style=\"border: 1px solid #ddd; padding: 10px; text-align: left;\" colspan=\"2\">디자인 정보</th>";
$content_summary .= "    </tr>";
$content_summary .= "  </thead>";
$content_summary .= "  <tbody style=\"\">";
$content_summary .= "    <tr style=\"\">";
$content_summary .= "      <th style=\"border: 1px solid #ddd; padding: 10px; text-align: left; background-color: #f5f5f5; font-weight: bold; color: #333;\">디자인 타입</th>";
$content_summary .= "      <td style=\"border: 1px solid #ddd; padding: 10px; text-align: left;\">" . $design_type . "</td>";
$content_summary .= "    </tr>";
$content_summary .= "    <tr style=\"background-color: #f9f9f9;\">";
$content_summary .= "      <th style=\"border: 1px solid #ddd; padding: 10px; text-align: left; background-color: #f5f5f5; font-weight: bold; color: #333;\">디자인 번호</th>";
$content_summary .= "      <td style=\"border: 1px solid #ddd; padding: 10px; text-align: left;\">" . $design . "</td>";
$content_summary .= "    </tr>";
$content_summary .= "  </tbody>";
$content_summary .= "  <thead style=\"background-color: #f5f5f5; font-weight: bold; color: #333;\">";
$content_summary .= "    <tr>";
$content_summary .= "      <th style=\"border: 1px solid #ddd; padding: 10px; text-align: left;\" colspan=\"2\">입력 정보</th>";
$content_summary .= "    </tr>";
$content_summary .= "  </thead>";
$content_summary .= "  <tbody style=\"\">";
$content_summary .= "    <tr style=\"\">";
$content_summary .= "      <th style=\"border: 1px solid #ddd; padding: 10px; text-align: left; background-color: #f5f5f5; font-weight: bold; color: #333;\">항목 A</th>";
$content_summary .= "      <td style=\"border: 1px solid #ddd; padding: 10px; text-align: left;\"><span style=\"font-weight: bold; color: #007bff;\">" . nl2br($name) . "</span></td>";
$content_summary .= "    </tr>";
$content_summary .= "    <tr style=\"background-color: #f9f9f9;\">";
$content_summary .= "      <th style=\"border: 1px solid #ddd; padding: 10px; text-align: left; background-color: #f5f5f5; font-weight: bold; color: #333;\">항목 B</th>";
$content_summary .= "      <td style=\"border: 1px solid #ddd; padding: 10px; text-align: left;\">" . nl2br($hours) . "</td>";
$content_summary .= "    </tr>";
$content_summary .= "    <tr>";
$content_summary .= "      <th style=\"border: 1px solid #ddd; padding: 10px; text-align: left; background-color: #f5f5f5; font-weight: bold; color: #333;\">항목 C</th>";
$content_summary .= "      <td style=\"border: 1px solid #ddd; padding: 10px; text-align: left;\">" . nl2br($contact) . "</td>";
$content_summary .= "    </tr>";
$content_summary .= "  </tbody>";
$content_summary .= "  <thead style=\"background-color: #f5f5f5; font-weight: bold; color: #333;\">";
$content_summary .= "    <tr>";
$content_summary .= "      <th style=\"border: 1px solid #ddd; padding: 10px; text-align: left;\" colspan=\"2\">스타일 정보</th>";
$content_summary .= "    </tr>";
$content_summary .= "  </thead>";
$content_summary .= "  <tbody style=\"\">";
$content_summary .= "    <tr style=\"background-color: #f9f9f9;\">";
$content_summary .= "      <th style=\"border: 1px solid #ddd; padding: 10px; text-align: left; background-color: #f5f5f5; font-weight: bold; color: #333;\">A 색상</th>";
$content_summary .= "      <td style=\"border: 1px solid #ddd; padding: 10px; text-align: left; background-color: " . $name_color . "; color: white;\">" . $name_color . "</td>";
$content_summary .= "    </tr>";
$content_summary .= "    <tr>";
$content_summary .= "      <th style=\"border: 1px solid #ddd; padding: 10px; text-align: left; background-color: #f5f5f5; font-weight: bold; color: #333;\">C 색상</th>";
$content_summary .= "      <td style=\"border: 1px solid #ddd; padding: 10px; text-align: left; background-color: " . $hours_color . "; color: white;\">" . $hours_color . "</td>";
$content_summary .= "    </tr>";
$content_summary .= "  </tbody>";
$content_summary .= "</table>";


// 저장 처리 (이전과 동일)

// SQL 삽입
$sql = "INSERT INTO wd_write_poster_save 
        SET wr_num = (SELECT IFNULL(MIN(wr_num) - 1, -1) FROM wd_write_poster_save as sq),
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
            wr_2 = '" . sql_real_escape_string($name) . "',
            wr_3 = '" . sql_real_escape_string($hours) . "',
            wr_4 = '" . sql_real_escape_string($contact) . "',
            wr_5 = '" . sql_real_escape_string($name_color) . "', -- A color
            wr_6 = '" . sql_real_escape_string($hours_color) . "', -- Now used for C color (second color)
            wr_7 = '" . sql_real_escape_string($design_type) . "',
            wr_8 = '',
            wr_9 = '',
            wr_10 = ''";

$result = sql_query($sql);

if ($result) {
    $wr_id = sql_insert_id();

    // 부모 아이디 업데이트
    sql_query("UPDATE wd_write_poster_save SET wr_parent = '$wr_id' WHERE wr_id = '$wr_id'");

    // 새글 삽입
    sql_query("INSERT INTO {$g5['board_new_table']} (bo_table, wr_id, wr_parent, bn_datetime, mb_id) 
               VALUES ('poster_save', '{$wr_id}', '{$wr_id}', '" . G5_TIME_YMDHIS . "', '{$member['mb_id']}')");

    // 게시글 수 증가
    sql_query("UPDATE {$g5['board_table']} SET bo_count_write = bo_count_write + 1 WHERE bo_table = 'poster_save'");

    // 포인트 지급
    if ($board['bo_use_point']) {
        insert_point($member['mb_id'], $board['bo_write_point'], "포스터 저장 - {$wr_id} 등록", 'poster_save', $wr_id, '등록');
    }

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
