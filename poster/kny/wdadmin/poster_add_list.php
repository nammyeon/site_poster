<?php
include_once('./_common.php');
include_once('./admin.head.php');

// 보안 함수
function esc($str) { return sql_escape_string(trim($str)); }


// 필터 및 페이징
$filter_type = isset($_GET['filter_type']) ? esc($_GET['filter_type']) : '';
$filter_active = isset($_GET['filter_active']) ? esc($_GET['filter_active']) : '';
$filter_guest = isset($_GET['filter_guest']) ? esc($_GET['filter_guest']) : '';
$search_keyword = isset($_GET['search']) ? esc($_GET['search']) : '';
$date_start = isset($_GET['date_start']) ? esc($_GET['date_start']) : '';
$date_end = isset($_GET['date_end']) ? esc($_GET['date_end']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// WHERE 조건
$where = [];
if ($filter_type) $where[] = "type LIKE '%$filter_type%'";
if ($filter_active !== '') $where[] = "poster_active = '$filter_active'";
if ($filter_guest !== '') $where[] = "poster_guest = '$filter_guest'";
if ($search_keyword) $where[] = "type LIKE '%$search_keyword%'";
if ($date_start) $where[] = "DATE(created_at) >= '$date_start'";
if ($date_end) $where[] = "DATE(created_at) <= '$date_end'";
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// 전체 개수
$count_result = sql_fetch_array(sql_query("SELECT COUNT(*) as cnt FROM wd_poster_data $where_sql"));
$total_count = $count_result['cnt'];
$total_pages = ceil($total_count / $per_page);

// 데이터 조회
$sql = "SELECT * FROM wd_poster_data $where_sql ORDER BY poster_order DESC, id DESC LIMIT $offset, $per_page";
$result = sql_query($sql);

$current_number = $total_count - $offset;


?>



<div class="admin-content poster-write">
    <h2 class="admin-content__title">포스터 데이터 등록</h2>
</div>