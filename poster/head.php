<?php
include ("_common.php"); 
//include_once(G5_LIB_PATH.'/latest.lib.php');
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
	<title>디지털미디어플랫폼 - Wave Dream 접수 시스템 </title>
    <meta property="og:title" content="디지털미디어플랫폼 - Wave Dream 접수 시스템" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="//wavedream.co.kr" />
    <meta property="og:image" content="/img/wavedream_ogimage.jpg" />
    <meta property="og:description" content="삼성전자 비즈니스 파트너 (주)나눔드림 디지털미디어플랫폼 - Wave Dream 접수 시스템" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Black+Han+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo G5_URL ?>/poster/index.css">
</head>
<body>
<?php
    if (!isset($member['mb_id']) || !$is_member) {
        echo '<div class="top-bar">';
        echo '<a href="#" id="login-link" class="open-login">로그인</a>';
        echo '<a href="#" id="register-link" class="open-register">회원가입</a>';
        echo '</div>';
    } else {
        echo '<div class="top-bar" style="background:#025aff;padding:2px;">';
        //echo '<span>안녕하세요, <span style="color:#0057ff">' . $member['mb_nick'] . '</span>님</span>';
        //echo '<a href="' . G5_BBS_URL . '/logout.php?url=' . urlencode('/poster/index.php') . '">로그아웃</a>';
        echo '</div>';
    }
?>