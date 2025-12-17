<?php
include_once("./_common.php"); // Include GnuBoard common functions

if (!$is_member) {
    alert("로그인 후 이용해 주세요.", G5_URL);
}

$wr_id = isset($_POST['wr_id']) ? intval($_POST['wr_id']) : 0;

if (!$wr_id) {
    alert("잘못된 접근입니다.", "./poster_history.php");
}

$mb_id = $member['mb_id'];

// Check if the poster belongs to the logged-in user
$sql = "SELECT COUNT(*) AS cnt FROM `wd_write_poster_save` WHERE `wr_id` = '$wr_id' AND `mb_id` = '$mb_id'";
$row = sql_fetch_array(sql_query($sql));

if ($row['cnt'] == 0) {
    alert("삭제 권한이 없습니다.", "./poster_history.php");
}

// Delete the poster data
$sql = "DELETE FROM `wd_write_poster_save` WHERE `wr_id` = '$wr_id'";
$result = sql_query($sql);

if ($result) {
    alert("포스터 신청 내역이 삭제되었습니다.", "./poster_history.php");
} else {
    alert("삭제에 실패했습니다. 관리자에게 문의해 주세요.", "./poster_history.php");
}
?>