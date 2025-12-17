<?php
include_once("./_common.php");

// Permission check
if(!$is_member) { 
    alert('글을 쓸 권한이 없습니다.');
	exit;
}

// 데이터 수집 및 정제
$mb_id = isset($member['mb_id']) ? clean_xss_tags($member['mb_id']) : '';
$mb_nick = isset($member['mb_nick']) ? clean_xss_tags($member['mb_nick']) : '';
$design_type = isset($_POST['design_type']) ? trim(clean_xss_tags($_POST['design_type'])) : '';
$design = isset($_POST['design']) ? trim(clean_xss_tags($_POST['design'])) : '';

// poster-I.php의 필드들
$topTitle = isset($_POST['topTitle']) ? get_text(htmlspecialchars($_POST['topTitle'], ENT_QUOTES)) : '';
$mainTitle = isset($_POST['mainTitle']) ? get_text(htmlspecialchars($_POST['mainTitle'], ENT_QUOTES)) : '';
$price = isset($_POST['price']) ? get_text(htmlspecialchars($_POST['price'], ENT_QUOTES)) : '';
$priceDetail = isset($_POST['priceDetail']) ? get_text(htmlspecialchars($_POST['priceDetail'], ENT_QUOTES)) : '';
$recommendText = isset($_POST['recommendText']) ? get_text(htmlspecialchars($_POST['recommendText'], ENT_QUOTES)) : '';
$benefit1 = isset($_POST['benefit1']) ? get_text(htmlspecialchars($_POST['benefit1'], ENT_QUOTES)) : '';
$benefit2 = isset($_POST['benefit2']) ? get_text(htmlspecialchars($_POST['benefit2'], ENT_QUOTES)) : '';
$benefit3 = isset($_POST['benefit3']) ? get_text(htmlspecialchars($_POST['benefit3'], ENT_QUOTES)) : '';
$benefit4 = isset($_POST['benefit4']) ? get_text(htmlspecialchars($_POST['benefit4'], ENT_QUOTES)) : '';
$pharmacy = isset($_POST['pharmacy']) ? get_text(htmlspecialchars($_POST['pharmacy'], ENT_QUOTES)) : '';

// 필수 입력값 체크
$msg = array();
if (empty($design)) $msg[] = '<strong>디자인</strong>을 입력하세요.';
if (empty($topTitle)) $msg[] = '<strong>상단 제목</strong>을 입력하세요.';
if (empty($mainTitle)) $msg[] = '<strong>메인 제목</strong>을 입력하세요.';
//if (empty($price)) $msg[] = '<strong>가격</strong>을 입력하세요.';
//if (empty($priceDetail)) $msg[] = '<strong>가격 상세</strong>을 입력하세요.';
//if (empty($recommendText)) $msg[] = '<strong>추천 문구</strong>을 입력하세요.';
//if (empty($benefit1)) $msg[] = '<strong>효능 1</strong>을 입력하세요.';
//if (empty($benefit2)) $msg[] = '<strong>효능 2</strong>을 입력하세요.';
//if (empty($benefit3)) $msg[] = '<strong>효능 3</strong>을 입력하세요.';
//if (empty($benefit4)) $msg[] = '<strong>효능 4</strong>을 입력하세요.';
//if (empty($pharmacy)) $msg[] = '<strong>약국명</strong>을 입력하세요.';

$msg = implode('<br>', $msg);
if ($msg) {
    alert($msg);
    exit;
}

$wr_subject = $pharmacy . " / Type: " . $design_type . " / Num: " . $design . " / " . $mainTitle;
$content_summary = "design_type: " . $design_type . " / design: " . $design . " / topTitle: " . $topTitle . " / mainTitle: " . $mainTitle . " / price: " . $price . " / priceDetail: " . $priceDetail . " / recommendText: " . $recommendText . " / benefit1: " . $benefit1 . " / benefit2: " . $benefit2 . " / benefit3: " . $benefit3 . " / benefit4: " . $benefit4 . " / pharmacy: " . $pharmacy;

// SEO 제목 생성 함수 (기본 함수가 없는 경우)
if (!function_exists('generate_seo_title')) {
    function generate_seo_title($title) {
        return strip_tags($title);
    }
}

// SQL 삽입
$sql = "INSERT INTO wd_write_poster_save 
        SET wr_num = (SELECT IFNULL(MIN(wr_num) - 1, -1) FROM wd_write_poster_save as sq),
            wr_reply = '',
            wr_comment = 0,
            wr_option = 'html1',
            wr_subject = '" . sql_real_escape_string($wr_subject) . "',
            wr_content = '" . sql_real_escape_string($content_summary) . "',
            wr_seo_title = '" . sql_real_escape_string(generate_seo_title($wr_subject)) . "',
            wr_link1 = '" . sql_real_escape_string($benefit3) . "',
            wr_link2 = '" . sql_real_escape_string($benefit4) . "',
            wr_hit = 0,
            wr_good = 0,
            wr_nogood = 0,
            mb_id = '" . sql_real_escape_string($mb_id) . "',
            wr_password = '',
            wr_name = '" . sql_real_escape_string($mb_nick) . "',
            wr_email = '',
            wr_homepage =  '" . sql_real_escape_string($pharmacy) . "',
            wr_datetime = '" . G5_TIME_YMDHIS . "',
            wr_last = '" . G5_TIME_YMDHIS . "',
            wr_ip = '" . sql_real_escape_string($_SERVER['REMOTE_ADDR']) . "',
            wr_1 = '" . sql_real_escape_string($design) . "',
            wr_2 = '" . sql_real_escape_string($topTitle) . "',
            wr_3 = '" . sql_real_escape_string($mainTitle) . "',
            wr_4 = '" . sql_real_escape_string($price) . "',
            wr_5 = '" . sql_real_escape_string($priceDetail) . "',
            wr_6 = '" . sql_real_escape_string($recommendText) . "',
            wr_7 = '" . sql_real_escape_string($design_type) . "',
            wr_8 = '" . sql_real_escape_string($benefit1) . "',
            wr_9 = '" . sql_real_escape_string($benefit2) . "',
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