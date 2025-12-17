<?php
include_once('./_common.php');

if (!$member['mb_id'] || !$is_admin) {
    alert('접근 권한이 없습니다.');
}

// 페이지 설정
$bo_table = 'poster_save';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// 페이지 리스트 개수 설정 (쿠키 적용)
$page_rows_options = [10, 30, 50, 100];
$page_rows = isset($_GET['rows']) ? (int)$_GET['rows'] : (isset($_COOKIE['page_rows']) ? (int)$_COOKIE['page_rows'] : 30);
if (!in_array($page_rows, $page_rows_options)) {
    $page_rows = 30;
}

// 줄간격 설정 (쿠키 적용)
$line_spacing_options = [15, 25, 35];
$line_spacing = isset($_GET['spacing']) ? (int)$_GET['spacing'] : (isset($_COOKIE['line_spacing']) ? (int)$_COOKIE['line_spacing'] : 25);
if (!in_array($line_spacing, $line_spacing_options)) {
    $line_spacing = 25;
}

// 다크모드 설정 (쿠키 적용)
$theme_options = ['light', 'dark'];
$theme_mode = isset($_GET['theme']) ? clean_xss_tags($_GET['theme']) : (isset($_COOKIE['theme_mode']) ? $_COOKIE['theme_mode'] : 'dark');
if (!in_array($theme_mode, $theme_options)) {
    $theme_mode = 'dark';
}

// 쿠키 저장 (값이 변경된 경우)
if (isset($_GET['rows'])) {
    setcookie('page_rows', $page_rows, time() + (86400 * 30), '/');
}
if (isset($_GET['spacing'])) {
    setcookie('line_spacing', $line_spacing, time() + (86400 * 30), '/');
}
if (isset($_GET['theme'])) {
    setcookie('theme_mode', $theme_mode, time() + (86400 * 30), '/');
}

$from_record = ($page - 1) * $page_rows;

// 검색 조건
$sca = isset($_GET['sca']) ? clean_xss_tags($_GET['sca']) : '';
$sfl = isset($_GET['sfl']) ? clean_xss_tags($_GET['sfl']) : '';
$stx = isset($_GET['stx']) ? clean_xss_tags($_GET['stx']) : '';

// 필터 조건 추가
$filter_design = isset($_GET['filter_design']) ? clean_xss_tags($_GET['filter_design']) : '';
$filter_status = isset($_GET['filter_status']) ? clean_xss_tags($_GET['filter_status']) : '';
$filter_name = isset($_GET['filter_name']) ? clean_xss_tags($_GET['filter_name']) : '';

// 첫 번째 데이터 날짜 조회
$first_date_sql = "SELECT MIN(DATE(wr_datetime)) as first_date FROM {$g5['write_prefix']}poster_save";
$first_date_result = sql_fetch($first_date_sql);
$default_start_date = $first_date_result['first_date'] ? $first_date_result['first_date'] : '2024-01-01';

$filter_start_date = isset($_GET['filter_start_date']) ? clean_xss_tags($_GET['filter_start_date']) : $default_start_date;
$filter_end_date = isset($_GET['filter_end_date']) ? clean_xss_tags($_GET['filter_end_date']) : date('Y-m-d');

// WHERE 조건 구성
$sql_search = '';
if ($sca) {
    $sql_search .= " AND ca_name = '{$sca}' ";
}
if ($stx && $sfl) {
    switch ($sfl) {
        case 'wr_subject':
            $sql_search .= " AND wr_subject LIKE '%{$stx}%' ";
            break;
        case 'wr_content':
            $sql_search .= " AND wr_content LIKE '%{$stx}%' ";
            break;
        case 'mb_id':
            $sql_search .= " AND mb_id LIKE '%{$stx}%' ";
            break;
        case 'wr_name':
            $sql_search .= " AND wr_name LIKE '%{$stx}%' ";
            break;
    }
}

// 필터 조건 추가
if ($filter_design) {
    $sql_search .= " AND wr_7 = '{$filter_design}' ";
}

if ($filter_status) {
    if ($filter_status === '접수') {
        $sql_search .= " AND (wr_10 IS NULL OR wr_10 = '' OR wr_10 = '접수') ";
    } else {
        $sql_search .= " AND wr_10 = '{$filter_status}' ";
    }
}
if ($filter_name) {
    $sql_search .= " AND wr_name LIKE '%{$filter_name}%' ";
}

// 기간 필터 조건 추가
if ($filter_start_date && $filter_end_date) {
    $sql_search .= " AND DATE(wr_datetime) BETWEEN '{$filter_start_date}' AND '{$filter_end_date}' ";
}

// 통계 데이터 수집
$stats_sql = "SELECT 
    COUNT(*) as total_count,
    COUNT(CASE WHEN wr_10 = '작업완료' THEN 1 END) as completed_count,
    COUNT(CASE WHEN wr_10 = '작업중' THEN 1 END) as progress_count,
    COUNT(CASE WHEN wr_10 = '송출중' THEN 1 END) as broadcasting_count,
    COUNT(CASE WHEN wr_10 IS NULL OR wr_10 = '' OR wr_10 = '접수' THEN 1 END) as received_count,
    COUNT(CASE WHEN DATE(wr_datetime) = CURDATE() THEN 1 END) as today_count,
    COUNT(CASE WHEN DATE(wr_datetime) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as week_count,
    COUNT(CASE WHEN DATE(wr_datetime) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as month_count
    FROM {$g5['write_prefix']}poster_save WHERE 1";
$stats_result = sql_fetch($stats_sql);

// 완료율 계산
$completion_rate = $stats_result['total_count'] > 0 ? round(($stats_result['completed_count'] / $stats_result['total_count']) * 100, 1) : 0;

// 전체 개수 조회
$sql_count = "SELECT COUNT(*) as cnt FROM {$g5['write_prefix']}poster_save WHERE 1 {$sql_search}";
$count_result = sql_fetch($sql_count);
$total_count = $count_result['cnt'];

// 페이지 계산
$total_page = ceil($total_count / $page_rows);

// 목록 조회
$sql = "SELECT * FROM {$g5['write_prefix']}poster_save 
        WHERE 1 {$sql_search}
        ORDER BY wr_datetime DESC, wr_id DESC 
        LIMIT {$from_record}, {$page_rows}";
$result = sql_query($sql);

// 목록 데이터 가공
$list = array();
while ($row = sql_fetch_array($result)) {
    // HTML 엔티티 디코딩 함수 추가
    $row['subject'] = html_entity_decode(stripslashes($row['wr_subject']), ENT_QUOTES, 'UTF-8');
    $row['name'] = html_entity_decode(stripslashes($row['wr_name']), ENT_QUOTES, 'UTF-8');
    $row['content'] = html_entity_decode(stripslashes($row['wr_content']), ENT_QUOTES, 'UTF-8');
    $row['datetime2'] = substr($row['wr_datetime'], 5, 5); // MM-DD 형식

	// 디자인 정보 처리 (기존 코드 교체) (wr_7: A~Z 값)
	$design_letter = !empty($row['wr_7']) ? strtoupper($row['wr_7']) : 'A';
	$design_number = !empty($row['wr_1']) ? str_replace('design', '', $row['wr_1']) : '1';
	$row['design_display'] = $design_letter . "-" .  $design_number;

	// 색상 클래스 결정 (A=1, B=2, C=3... Z=26 형태로 매핑)
	$color_index = (ord($design_letter) - ord('A')) + 1;
	$row['design_color_class'] = 'design-color-' . $color_index;
	
    // 상태 처리
    if (empty($row['wr_10'])) {
        $row['wr_10'] = '접수';
    }
    
    // 아이콘 설정
    $row['icon_new'] = (time() - strtotime($row['wr_datetime']) < 86400) ? true : false;
    
    // 번호 설정
    $row['num'] = $total_count - (($page - 1) * $page_rows) - count($list);
    
	// 회원 정보 조회
	$member_info = get_member($row['mb_id']);
	$row['mb_name'] = $member_info['mb_name'];
	$row['mb_hp'] = $member_info['mb_hp'];
    
    $list[] = $row;
}

// 페이지네이션 쿼리 파라미터 생성 함수
function buildPageQuery($page_rows, $line_spacing, $theme_mode, $sca, $sfl, $stx, $filter_design, $filter_status, $filter_name, $filter_start_date, $filter_end_date) {
    $params = array();
    $params['rows'] = $page_rows;
    $params['spacing'] = $line_spacing;
    $params['theme'] = $theme_mode;
    
    if ($sca) $params['sca'] = $sca;
    if ($sfl && $stx) {
        $params['sfl'] = $sfl;
        $params['stx'] = $stx;
    }
    if ($filter_design) $params['filter_design'] = $filter_design;
    if ($filter_status) $params['filter_status'] = $filter_status;
    if ($filter_name) $params['filter_name'] = $filter_name;
    if ($filter_start_date) $params['filter_start_date'] = $filter_start_date;
    if ($filter_end_date) $params['filter_end_date'] = $filter_end_date;
    
    return http_build_query($params);
}

$page_query = buildPageQuery($page_rows, $line_spacing, $theme_mode, $sca, $sfl, $stx, $filter_design, $filter_status, $filter_name, $filter_start_date, $filter_end_date);

// 현재 적용된 필터를 제외한 WHERE 조건 구성 함수 추가
function getFilterSql($exclude_filter = '') {
    global $sca, $stx, $sfl, $filter_design, $filter_status, $filter_name, $filter_start_date, $filter_end_date;
    
    $sql_search_temp = '';
    
    if ($sca) {
        $sql_search_temp .= " AND ca_name = '{$sca}' ";
    }
    if ($stx && $sfl) {
        switch ($sfl) {
            case 'wr_subject':
                $sql_search_temp .= " AND wr_subject LIKE '%{$stx}%' ";
                break;
            case 'wr_content':
                $sql_search_temp .= " AND wr_content LIKE '%{$stx}%' ";
                break;
            case 'mb_id':
                $sql_search_temp .= " AND mb_id LIKE '%{$stx}%' ";
                break;
            case 'wr_name':
                $sql_search_temp .= " AND wr_name LIKE '%{$stx}%' ";
                break;
        }
    }
    
    if ($exclude_filter !== 'filter_design' && $filter_design) {
        $sql_search_temp .= " AND wr_7 = '{$filter_design}' ";
    }
    if ($exclude_filter !== 'filter_status' && $filter_status) {
        if ($filter_status === '접수') {
            $sql_search_temp .= " AND (wr_10 IS NULL OR wr_10 = '' OR wr_10 = '접수') ";
        } else {
            $sql_search_temp .= " AND wr_10 = '{$filter_status}' ";
        }
    }
    if ($exclude_filter !== 'filter_name' && $filter_name) {
        $sql_search_temp .= " AND wr_name LIKE '%{$filter_name}%' ";
    }
    if ($exclude_filter !== 'filter_start_date' && $exclude_filter !== 'filter_end_date' && $filter_start_date && $filter_end_date) {
        $sql_search_temp .= " AND DATE(wr_datetime) BETWEEN '{$filter_start_date}' AND '{$filter_end_date}' ";
    }
    
    return $sql_search_temp;
}

// 필터 옵션을 위한 데이터 수집 - 수정된 버전
$filter_options = array(
    'designs' => array(),
    'status' => array(),
    'names' => array()
);

// 디자인 옵션 (다른 필터의 영향을 받도록 수정)
$sql_designs = "SELECT wr_7, COUNT(*) as cnt FROM {$g5['write_prefix']}poster_save WHERE wr_7 IS NOT NULL AND wr_7 != '' AND 1 " . getFilterSql('filter_design') . " GROUP BY wr_7 ORDER BY wr_7 ASC";
$result_designs = sql_query($sql_designs);
while ($row_design = sql_fetch_array($result_designs)) {
    $filter_options['designs'][] = array(
        'name' => strtoupper($row_design['wr_7']),
        'count' => $row_design['cnt'],
        'display' => strtoupper($row_design['wr_7']) . ' (' . $row_design['cnt'] . ')'
    );
}

// 상태 옵션 (다른 필터의 영향을 받도록 수정)
$sql_status = "SELECT 
    CASE 
        WHEN wr_10 IS NULL OR wr_10 = '' THEN '접수'
        ELSE wr_10
    END as status_display,
    COUNT(*) as cnt
    FROM {$g5['write_prefix']}poster_save 
    WHERE 1 " . getFilterSql('filter_status') . "
    GROUP BY status_display 
    ORDER BY cnt DESC";
$result_status = sql_query($sql_status);
while ($row_status = sql_fetch_array($result_status)) {
    $filter_options['status'][] = array(
        'name' => $row_status['status_display'],
        'count' => $row_status['cnt'],
        'display' => $row_status['status_display'] . ' (' . $row_status['cnt'] . ')'
    );
}

// 이름 옵션 (다른 필터의 영향을 받도록 수정, 3개 이상만)
$sql_names = "SELECT wr_name, COUNT(*) as cnt FROM {$g5['write_prefix']}poster_save WHERE wr_name IS NOT NULL AND wr_name != '' " . getFilterSql('filter_name') . " GROUP BY wr_name ORDER BY cnt DESC";
$result_names = sql_query($sql_names);
while ($row_name = sql_fetch_array($result_names)) {
    // 3개 이상인 경우만 추가

        $filter_options['names'][] = array(
            'name' => $row_name['wr_name'],
            'count' => $row_name['cnt'],
            'display' => $row_name['wr_name'] . ' (' . $row_name['cnt'] . ')'
        );

}

// 헤더 설정
$g5['title'] = '포스터 신청 관리 시스템';
include_once('./head.sub2.php');
?>

<style>
/* 폰트 임포트 */
@import url('https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.8/dist/web/static/pretendard.css');
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap');

#container {margin:0 auto;width:99%;min-width:1200px}

/* Modern Design System */
:root {
    --primary-color: #1e40af;
    --primary-hover: #1d4ed8;
    --secondary-color: #475569;
    --success-color: #059669;
    --warning-color: #d97706;
    --danger-color: #dc2626;
    --bg-primary: #ffffff;
    --bg-secondary: #f8fafc;
    --bg-accent: #f1f5f9;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --border-color: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --radius: 8px;
    --radius-sm: 4px;
    --font-primary: 'Pretendard', 'Noto Sans KR', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
}

/* Dark Mode Variables */
[data-theme="dark"] {
    --primary-color: #3b82f6;
    --primary-hover: #2563eb;
    --secondary-color: #94a3b8;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --bg-primary: #1e293b;
    --bg-secondary: #0f172a;
    --bg-accent: #334155;
    --text-primary: #f1f5f9;
    --text-secondary: #94a3b8;
    --border-color: #334155;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.3);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.3), 0 2px 4px -2px rgb(0 0 0 / 0.3);
}

/* Reset & Base */
* {
    box-sizing: border-box;
}

body {
    font-family: var(--font-primary);
    color: var(--text-primary);
    line-height: 1.6;
    background-color: var(--bg-secondary);
    font-weight: 400;
    letter-spacing: -0.02em;
}

/* Container */
#bo_list {
    width: 1200px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 15px;
    background: var(--bg-primary);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
}

/* Header */
.header {
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    color: white;
    padding: 5px 24px;
    border-radius: var(--radius);
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 12px rgba(30, 64, 175, 0.15);
    transition: all 0.3s ease;
}

[data-theme="dark"] .header:not([data-sub-admin="true"]) {
    background: linear-gradient(135deg, #1e293b, #334155, #1e40af) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
}

.header h1 {
    font-size: 22px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.header h1 i {
    background: rgba(255, 255, 255, 0.2);
    padding: 8px;
    border-radius: 8px;
}

.header-right {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: flex-end;
}

.header-top-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-total-stats {
    display: flex;
    gap: 15px;
    font-size: 14px;
    font-weight: 600;
}

.total-stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: rgba(255, 255, 255, 0.1);
    padding: 6px 12px;
    border-radius: 6px;
    backdrop-filter: blur(10px);
    transition: background 0.3s ease;
}

.total-stat-label {
    font-size: 11px;
    opacity: 0.8;
    margin-bottom: 2px;
}

.total-stat-value {
    font-size: 16px;
    font-weight: 700;
}

.header-more-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.25));
    border: 1px solid rgba(255, 255, 255, 0.4);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    backdrop-filter: blur(15px);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.header-more-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.header-more-btn:hover::before {
    left: 100%;
}

.header-more-btn:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.35));
    color: white;
    border-color: rgba(255, 255, 255, 0.6);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.header-more-btn i {
    font-size: 14px;
    background: rgba(255, 255, 255, 0.2);
    padding: 4px;
    border-radius: 4px;
    margin-right: 2px;
}

[data-theme="dark"] .header-more-btn {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(147, 51, 234, 0.2));
    border-color: rgba(255, 255, 255, 0.3);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

[data-theme="dark"] .header-more-btn:hover {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.4), rgba(147, 51, 234, 0.3));
    border-color: rgba(255, 255, 255, 0.5);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
}

[data-theme="dark"] .header-more-btn i {
    background: rgba(59, 130, 246, 0.4);
}

/* Filter Section */
.filter-section {
    display: flex;
    flex-wrap: nowrap;
    gap: 6px;
    margin-bottom: 15px;
    padding: 8px;
    background: var(--bg-secondary);
    border-radius: var(--radius);
    border: 1px solid var(--border-color);
    overflow-x: auto;
    min-height: 60px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 85px;
    flex-shrink: 0;
}

.filter-group.date-range {
    min-width: 160px;
}

.filter-group.active .filter-select {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.filter-group.active .filter-label {
    color: var(--primary-color);
    font-weight: 700;
}

.filter-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
}

.filter-select, .filter-input {
    padding: 4px 6px;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    font-size: 11px;
    font-family: var(--font-primary);
    background: white;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 80px;
}

.filter-select:focus, .filter-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
}

.date-inputs {
    display: flex;
    gap: 3px;
    align-items: center;
}

.date-inputs input {
    flex: 1;
    min-width: 65px;
    font-size: 10px;
    padding: 4px 6px;
}

.date-inputs span {
    color: var(--text-secondary);
    font-size: 11px;
}

.filter-clear {
    padding: 4px 8px;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
    font-size: 10px;
    align-self: flex-end;
    white-space: nowrap;
    min-width: 60px;
    flex-shrink: 0;
}

.filter-clear:hover {
    background: var(--bg-accent);
    color: var(--text-primary);
}

.filter-clear.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

/* Controls Section */
.controls-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding: 8px 12px;
    background: var(--bg-secondary);
    border-radius: var(--radius);
    border: 1px solid var(--border-color);
}

.list-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.total-count {
    font-weight: 600;
    color: var(--primary-color);
    font-size: 14px;
}

.page-info {
    color: var(--text-secondary);
    font-size: 13px;
}

.rows-selector {
    display: flex;
    align-items: center;
    gap: 8px;
}

.rows-selector label {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-secondary);
}

.rows-select {
    padding: 4px 8px;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-family: var(--font-primary);
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.admin-controls {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 13px;
}

.action-buttons {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
    font-family: var(--font-primary);
	justify-content: center;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-secondary {
    background: var(--bg-primary);
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--bg-accent);
    color: var(--text-primary);
}

/* Stats Section */
.stats-mini-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 6px;
    padding: 10px;
    background: var(--bg-primary);
    border-radius: var(--radius-sm);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-md);
}

.stats-mini-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 6px 8px;
    background: var(--bg-secondary);
    border-radius: var(--radius-sm);
    font-size: 11px;
    text-align: center;
}

.stats-mini-card .label {
    color: var(--text-secondary);
    font-weight: 500;
    margin-bottom: 2px;
    font-size: 10px;
}

.stats-mini-card .value {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 13px;
}

.stats-mini-card .sub-value {
    color: var(--text-secondary);
    font-size: 9px;
    margin-top: 1px;
}

/* Table */
.modern-table-container {
    background: var(--bg-primary);
    border-radius: var(--radius);
    overflow: hidden;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    font-family: var(--font-primary);
}

.modern-table thead {
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    border-bottom: 2px solid var(--border-color);
}

[data-theme="dark"] .modern-table thead {
    background: linear-gradient(135deg, #334155, #475569);
}

.modern-table th {
    padding: 10px 8px;
    text-align: center;
    font-weight: 600;
    color: var(--text-primary);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
    z-index: 10;
}

.modern-table td {
    padding: 7px 5px;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
    text-align: center;
    font-weight: 400;
}
/* Table cell text overflow handling */
.modern-table td:nth-child(3) {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 300px;
}
.modern-table tbody tr {
    transition: background-color 0.2s;
    line-height: <?php echo $line_spacing; ?>px;
}

.modern-table tbody tr:hover {
    background: var(--bg-secondary);
}

.modern-table tbody tr.even {
    background: rgba(248, 250, 252, 0.5);
}

[data-theme="dark"] .modern-table tbody tr:hover {
    background: #334155;
}

[data-theme="dark"] .modern-table tbody tr.even {
    background: rgba(51, 65, 85, 0.3);
}

/* Status Styles */
.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.status-접수 {
    background: #fef3c7;
    color: var(--warning-color);
}

.status-작업중 {
   background: #dbeafe;
   color: var(--primary-color);
}

.status-작업완료 {
   background: #dcfce7;
   color: var(--success-color);
}

.status-송출중 {
   background: #fce7f3;
   color: #ec4899;
}

[data-theme="dark"] .status-접수 {
   background: rgba(245, 158, 11, 0.2);
   color: #f59e0b;
}

[data-theme="dark"] .status-작업중 {
   background: rgba(59, 130, 246, 0.2);
   color: #3b82f6;
}

[data-theme="dark"] .status-작업완료 {
   background: rgba(16, 185, 129, 0.2);
   color: #10b981;
}

[data-theme="dark"] .status-송출중 {
   background: rgba(236, 72, 153, 0.2);
   color: #ec4899;
}

/* New Badge */
.new-badge {
    background: var(--danger-color);
    color: white;
    padding: 1px 4px;
    border-radius: 5px;
    font-size: 10px;
    margin-left: 3px;
    animation: pulse 1.5s infinite ease-in-out, colorChange 1.5s infinite ease-in-out;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.7;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes colorChange {
    0% {
        background: var(--danger-color);
    }
    50% {
        background: var(--bg-primary); /* 흰색으로 변경, 다른 색상 원하시면 수정 가능 */
    }
    100% {
        background: var(--danger-color);
    }
}

/* Status Form Styles */
.status_form {
   display: flex;
   align-items: center;
   gap: 5px;
}

.status_select {
   padding: 4px 6px;
   border: 1px solid var(--border-color);
   border-radius: var(--radius-sm);
   font-size: 11px;
   background: var(--bg-primary);
   color: var(--text-primary);
   cursor: pointer;
   transition: all 0.2s;
}

.status_select:focus {
   outline: none;
   border-color: var(--primary-color);
}

.status_button {
   padding: 4px 8px;
   border: 1px solid var(--success-color);
   background-color: var(--success-color);
   color: white;
   cursor: pointer;
   border-radius: var(--radius-sm);
   font-size: 11px;
   transition: all 0.2s;
}

.status_button:hover {
   background-color: #047857;
}

/* Dark Mode Form Elements */
[data-theme="dark"] .filter-select,
[data-theme="dark"] .filter-input,
[data-theme="dark"] .form-select,
[data-theme="dark"] .form-input,
[data-theme="dark"] .rows-select,
[data-theme="dark"] .status_select {
   background: var(--bg-accent);
   color: var(--text-primary);
   border-color: var(--border-color);
}

[data-theme="dark"] .filter-select:focus,
[data-theme="dark"] .filter-input:focus,
[data-theme="dark"] .form-select:focus,
[data-theme="dark"] .form-input:focus,
[data-theme="dark"] .rows-select:focus,
[data-theme="dark"] .status_select:focus {
   background: var(--bg-accent);
   border-color: var(--primary-color);
}

/* Search */
.search-container {
   position: fixed;
   top: 0;
   left: 0;
   right: 0;
   bottom: 0;
   background: rgba(0, 0, 0, 0.5);
   display: none;
   align-items: center;
   justify-content: center;
   z-index: 1000;
}

.search-dialog {
   background: var(--bg-primary);
   padding: 30px;
   border-radius: var(--radius);
   width: 90%;
   max-width: 500px;
   box-shadow: var(--shadow-md);
}

.search-form {
   display: flex;
   flex-direction: column;
   gap: 15px;
}

.form-group {
   display: flex;
   flex-direction: column;
   gap: 5px;
}

.form-label {
   font-weight: 500;
   color: var(--text-primary);
   font-size: 14px;
}

.form-select,
.form-input {
   padding: 10px;
   border: 1px solid var(--border-color);
   border-radius: var(--radius-sm);
   font-size: 14px;
   transition: border-color 0.2s;
   font-family: var(--font-primary);
}

.form-select:focus,
.form-input:focus {
   outline: none;
   border-color: var(--primary-color);
}

.search-actions {
   display: flex;
   gap: 10px;
   justify-content: flex-end;
}

/* Pagination Styles */
.pagination-container {
   display: flex;
   justify-content: center;
   align-items: center;
   margin: 20px 0;
   padding: 15px;
   background: var(--bg-secondary);
   border-radius: var(--radius);
   border: 1px solid var(--border-color);
}

.pagination {
   display: flex;
   align-items: center;
   gap: 5px;
   font-family: var(--font-primary);
}

.pagination a,
.pagination span {
   display: inline-flex;
   align-items: center;
   justify-content: center;
   min-width: 36px;
   height: 36px;
   padding: 0 8px;
   font-size: 14px;
   font-weight: 500;
   text-decoration: none;
   border-radius: var(--radius-sm);
   transition: all 0.2s ease;
   border: 1px solid transparent;
}

.pagination a {
   color: var(--text-secondary);
   background: var(--bg-primary);
   border-color: var(--border-color);
}

.pagination a:hover {
   color: var(--primary-color);
   background: var(--bg-accent);
   border-color: var(--primary-color);
   transform: translateY(-1px);
}

.pagination .current {
   color: white;
   background: var(--primary-color);
   border-color: var(--primary-color);
   font-weight: 600;
   cursor: default;
}

.pagination .disabled {
   color: var(--text-secondary);
   background: var(--bg-secondary);
   border-color: var(--border-color);
   cursor: not-allowed;
   opacity: 0.5;
}

.pagination .disabled:hover {
   transform: none;
   color: var(--text-secondary);
   background: var(--bg-secondary);
   border-color: var(--border-color);
}

.pagination .nav-btn {
   padding: 0 12px;
   font-weight: 600;
}

.pagination .nav-btn i {
   font-size: 12px;
}

.pagination .dots {
   color: var(--text-secondary);
   background: transparent;
   border: none;
   cursor: default;
   font-weight: 600;
}

.pagination .dots:hover {
   transform: none;
   background: transparent;
   border: none;
}

/* Dark Mode Pagination */
[data-theme="dark"] .pagination a {
   background: var(--bg-accent);
   border-color: var(--border-color);
   color: var(--text-primary);
}

[data-theme="dark"] .pagination a:hover {
   background: var(--bg-primary);
   border-color: var(--primary-color);
}

.sound_only {
   position: absolute !important;
   left: -9999px !important;
   width: 1px !important;
   height: 1px !important;
   overflow: hidden !important;
}

/* Dark Mode Date Input Calendar Icon */
[data-theme="dark"] .filter-input[type="date"]::-webkit-calendar-picker-indicator {
   filter: invert(1);
   cursor: pointer;
}

[data-theme="dark"] .filter-input[type="date"]::-webkit-calendar-picker-indicator:hover {
   filter: invert(1) brightness(1.2);
}

/* Empty State */
.empty-state {
   text-align: center;
   padding: 60px 20px;
   color: var(--text-secondary);
}

/* Active Search Button */
.btn_bo_sch.active-search {
   background: var(--primary-color);
   color: white;
   border-color: var(--primary-color);
}

.btn_bo_sch.active-search:hover {
   background: var(--primary-hover);
   color: white;
}

/* Design Color Styles - A~Z 전체 26개 색상 */
.design-color-1 { background: #1e40af; color: white; } /* A - 진한 블루 */
.design-color-2 { background: #059669; color: white; } /* B - 진한 그린 */  
.design-color-3 { background: #d97706; color: white; } /* C - 진한 오렌지 */
.design-color-4 { background: #dc2626; color: white; } /* D - 진한 레드 */
.design-color-5 { background: #7c2d12; color: white; } /* E - 진한 브라운 */
.design-color-6 { background: #581c87; color: white; } /* F - 진한 퍼플 */
.design-color-7 { background: #0f766e; color: white; } /* G - 진한 틸 */
.design-color-8 { background: #365314; color: white; } /* H - 진한 올리브 */
.design-color-9 { background: #9a3412; color: white; } /* I - 진한 오렌지-브라운 */
.design-color-10 { background: #374151; color: white; } /* J - 진한 그레이 */
.design-color-11 { background: #701a75; color: white; } /* K - 진한 마젠타 */
.design-color-12 { background: #0c4a6e; color: white; } /* L - 진한 스카이블루 */
.design-color-13 { background: #166534; color: white; } /* M - 진한 에메랄드 */
.design-color-14 { background: #a21caf; color: white; } /* N - 진한 핑크 */
.design-color-15 { background: #92400e; color: white; } /* O - 진한 앰버 */
.design-color-16 { background: #1f2937; color: white; } /* P - 진한 슬레이트 */
.design-color-17 { background: #7c3aed; color: white; } /* Q - 진한 바이올렛 */
.design-color-18 { background: #be123c; color: white; } /* R - 진한 로즈 */
.design-color-19 { background: #0d9488; color: white; } /* S - 진한 청록 */
.design-color-20 { background: #ea580c; color: white; } /* T - 진한 오렌지레드 */
.design-color-21 { background: #4338ca; color: white; } /* U - 진한 인디고 */
.design-color-22 { background: #15803d; color: white; } /* V - 진한 라임그린 */
.design-color-23 { background: #b45309; color: white; } /* W - 진한 골드 */
.design-color-24 { background: #991b1b; color: white; } /* X - 진한 크림슨 */
.design-color-25 { background: #6d28d9; color: white; } /* Y - 진한 퍼플블루 */
.design-color-26 { background: #334155; color: white; } /* Z - 진한 블루그레이 */

.design-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
    min-width: 45px;
    justify-content: center;
    letter-spacing: 0.5px;
}

/* 다크모드에서도 동일한 색상 유지 */
[data-theme="dark"] .design-badge {
    /* 배경색은 그대로 유지 */
}

/* Delete Button Styles */
.delete-btn {
    background: var(--danger-color);
    color: white;
    border: none;
    border-radius: var(--radius-sm);
    padding: 6px 8px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.delete-btn:hover {
    background: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
}

.delete-btn i {
    font-size: 11px;
}

[data-theme="dark"] .delete-btn {
    background: #ef4444;
}

[data-theme="dark"] .delete-btn:hover {
    background: #dc2626;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">

<?php
// 페이지네이션 생성 함수
function createPagination($current_page, $total_pages, $base_url) {
   if ($total_pages <= 1) {
       return '';
   }
   
   $pagination = '<div class="pagination-container"><div class="pagination">';
   
   // 이전 페이지
   if ($current_page > 1) {
       $pagination .= '<a href="' . $base_url . '&page=' . ($current_page - 1) . '" class="nav-btn"><i class="fas fa-chevron-left"></i> 이전</a>';
   } else {
       $pagination .= '<span class="nav-btn disabled"><i class="fas fa-chevron-left"></i> 이전</span>';
   }
   
   // 페이지 번호들
   $start_page = max(1, $current_page - 2);
   $end_page = min($total_pages, $current_page + 2);
   
   // 첫 페이지
   if ($start_page > 1) {
       $pagination .= '<a href="' . $base_url . '&page=1">1</a>';
       if ($start_page > 2) {
           $pagination .= '<span class="dots">...</span>';
       }
   }
   
   // 중간 페이지들
   for ($i = $start_page; $i <= $end_page; $i++) {
       if ($i == $current_page) {
           $pagination .= '<span class="current">' . $i . '</span>';
       } else {
           $pagination .= '<a href="' . $base_url . '&page=' . $i . '">' . $i . '</a>';
       }
   }
   
   // 마지막 페이지
   if ($end_page < $total_pages) {
       if ($end_page < $total_pages - 1) {
           $pagination .= '<span class="dots">...</span>';
       }
       $pagination .= '<a href="' . $base_url . '&page=' . $total_pages . '">' . $total_pages . '</a>';
   }
   
   // 다음 페이지
   if ($current_page < $total_pages) {
       $pagination .= '<a href="' . $base_url . '&page=' . ($current_page + 1) . '" class="nav-btn">다음 <i class="fas fa-chevron-right"></i></a>';
   } else {
       $pagination .= '<span class="nav-btn disabled">다음 <i class="fas fa-chevron-right"></i></span>';
   }
   
   $pagination .= '</div></div>';
   
   return $pagination;
}

// 페이지네이션 HTML 생성
$pagination_html = createPagination($page, $total_page, "?" . $page_query);
?>

<div id="bo_list" data-theme="<?php echo $theme_mode; ?>">
   
   <div class="header" <?php echo ($member['mb_level'] == 9) ? 'data-sub-admin="true"' : ''; ?>>
       <h1><i class="fas fa-image"></i> Wavedream Poster Management System</h1>
       <div class="header-right">
		<div class="header-top-section">
			<?php if ($member['mb_level'] != 9 && $is_admin): ?>
			<div class="header-total-stats">
				<div class="header-total-stats">
					<div class="total-stat-item">
						<div class="total-stat-label">전체접수</div>
						<div class="total-stat-value"><?php echo number_format($stats_result['total_count']); ?></div>
					</div>
					<div class="total-stat-item">
						<div class="total-stat-label"><?php echo date('n'); ?>월접수</div>
						<div class="total-stat-value"><?php echo number_format($stats_result['month_count']); ?></div>
					</div>
					<div class="total-stat-item" style="margin-right:20px;">
						<div class="total-stat-label">오늘접수</div>
						<div class="total-stat-value"><?php echo number_format($stats_result['today_count']); ?></div>
					</div>
					<div class="total-stat-item">
						<div class="total-stat-label">접수</div>
						<div class="total-stat-value" style="color:yellow"><?php echo number_format($stats_result['received_count']); ?></div>
					</div>
					<div class="total-stat-item">
						<div class="total-stat-label">작업중</div>
						<div class="total-stat-value" style="color:yellow"><?php echo number_format($stats_result['progress_count']); ?></div>
					</div>
					<div class="total-stat-item">
						<div class="total-stat-label">작업완료</div>
						<div class="total-stat-value" style="color:yellow"><?php echo number_format($stats_result['completed_count']); ?></div>
					</div>
					<div class="total-stat-item">
						<div class="total-stat-label">송출중</div>
						<div class="total-stat-value" style="color:yellow"><?php echo number_format($stats_result['broadcasting_count']); ?></div>
					</div>
				</div>
				<?php if ($member['mb_level'] != 9 && $is_admin): ?>
				<!-- <a href="#" target="_blank" class="header-more-btn" title="관리자 연결">
					<i class="fas fa-cog fa-spin"></i>
					<span>관리자연결</span>
				</a> -->
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
       </div>
   </div>

   <!-- Filter Section -->
   <div class="filter-section" style="position:relative">
		<div class="filter-group <?php echo $filter_design ? 'active' : ''; ?>">
			<label class="filter-label">디자인</label>
			<select class="filter-select" onchange="applyFilter('filter_design', this.value)">
				<option value="">전체</option>
				<?php foreach ($filter_options['designs'] as $design): ?>
					<option value="<?php echo $design['name'] ?>" <?php echo ($filter_design == $design['name']) ? 'selected' : ''; ?>>
						<?php echo $design['display'] ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
       <div class="filter-group <?php echo $filter_status ? 'active' : ''; ?>">
           <label class="filter-label">상태</label>
           <select class="filter-select" onchange="applyFilter('filter_status', this.value)">
               <option value="">전체</option>
               <?php foreach ($filter_options['status'] as $status): ?>
                   <option value="<?php echo $status['name'] ?>" <?php echo ($filter_status == $status['name']) ? 'selected' : ''; ?>>
                       <?php echo $status['display'] ?>
                   </option>
               <?php endforeach; ?>
           </select>
       </div>
       
       <div class="filter-group <?php echo $filter_name ? 'active' : ''; ?>">
           <label class="filter-label">신청자</label>
           <select class="filter-select" onchange="applyFilter('filter_name', this.value)">
               <option value="">전체</option>
               <?php foreach ($filter_options['names'] as $name): ?>
                   <option value="<?php echo $name['name'] ?>" <?php echo ($filter_name == $name['name']) ? 'selected' : ''; ?>>
                       <?php echo $name['display'] ?>
                   </option>
               <?php endforeach; ?>
           </select>
       </div>
       
       <div class="filter-group date-range <?php echo ($filter_start_date != $default_start_date || $filter_end_date != date('Y-m-d')) ? 'active' : ''; ?>">
           <label class="filter-label">신청일</label>
           <div class="date-inputs">
               <input type="date" class="filter-input" id="startDate" value="<?php echo $filter_start_date; ?>" onchange="applyDateFilter()">
               <span>~</span>
               <input type="date" class="filter-input" id="endDate" value="<?php echo $filter_end_date; ?>" onchange="applyDateFilter()">
           </div>
       </div>
       
       <button type="button" class="filter-clear" onclick="clearAllFilters()" title="필터 초기화">
           <i class="fa fa-times"></i> <span style="font-size:12px">초기화</span>
       </button>
       
		<a href="poster_excel_down.php?<?php echo $page_query; ?>" class="btn btn-primary" title="엑셀 다운로드" target="_blank" style="position: absolute;right: 10px;bottom: 10px;background:#217346">
			<i class="fa fa-file-excel" style="color:white"></i>
		</a>
   </div>

   <!-- Controls Section -->
   <div class="controls-section">
       <div class="list-info">
           <div class="total-count">Total <?php echo number_format($total_count) ?>건</div>
           <div class="page-info"><?php echo $page ?> 페이지 / <?php echo $total_page ?> 페이지</div>
           <div class="rows-selector">
               <label for="rowsSelect">표시:</label>
               <select id="rowsSelect" class="rows-select" onchange="changeRowsPerPage(this.value)">
                   <option value="10" <?php echo ($page_rows == 10) ? 'selected' : ''; ?>>10개</option>
                   <option value="30" <?php echo ($page_rows == 30) ? 'selected' : ''; ?>>30개</option>
                   <option value="50" <?php echo ($page_rows == 50) ? 'selected' : ''; ?>>50개</option>
                   <option value="100" <?php echo ($page_rows == 100) ? 'selected' : ''; ?>>100개</option>
               </select>
               
               <label for="spacingSelect" style="margin-left: 15px;">줄간격:</label>
               <select id="spacingSelect" class="rows-select" onchange="changeLineSpacing(this.value)">
                   <option value="15" <?php echo ($line_spacing == 15) ? 'selected' : ''; ?>>좁게</option>
                   <option value="25" <?php echo ($line_spacing == 25) ? 'selected' : ''; ?>>보통</option>
                   <option value="35" <?php echo ($line_spacing == 35) ? 'selected' : ''; ?>>넓게</option>
               </select>
               
               <label for="themeSelect" style="margin-left: 15px;">테마:</label>
               <select id="themeSelect" class="rows-select" onchange="window.location.href = updateUrlParameter('theme', this.value)">
                   <option value="light" <?php echo ($theme_mode == 'light') ? 'selected' : ''; ?>>라이트</option>
                   <option value="dark" <?php echo ($theme_mode == 'dark') ? 'selected' : ''; ?>>다크</option>
               </select>
           </div>
      
       </div>

       <div class="admin-controls">
		<div class="action-buttons">
			<button type="button" class="btn btn-secondary btn_bo_sch <?php echo ($stx && $sfl) ? 'active-search' : ''; ?>" title="검색">
				<i class="fa fa-search"></i>
			</button>
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary" title="전체목록">
				<i class="fa fa-list"></i><!-- <span style="font-size:15px;margin-top: 3px;">전체목록</span> -->
			</a>
			<?php if ($member['mb_level'] != 9 && $is_admin): ?>
			<a href="/adm" class="btn btn-secondary" title="관리자 연결" target="_blank">
				<i class="fa fa-cog fa-spin" style="color:red"></i>
			</a>
			<?php endif; ?>
			<a href="/bbs/logout.php" class="btn btn-secondary" title="로그아웃">
				<i class="fa fa-sign-out-alt"></i>
			</a>
		</div>
       </div>
   </div>

   <!-- Modern Table -->
   <div class="modern-table-container">
       <form name="fboardlist" id="fboardlist" method="post">
           <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
           <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
           <input type="hidden" name="stx" value="<?php echo $stx ?>">
           <input type="hidden" name="page" value="<?php echo $page ?>">
           
           <table class="modern-table">
               <thead>
                   <tr>
						<th style="width: 40px;">번호</th>
						<th style="width: 70px;">디자인</th>
						<th style="width: ;">제목</th>
						<th style="width: 100px;">ID</th>
						<th style="width: 100px;">이름</th>
						<th style="width: 100px;">약국</th>
						<th style="width: 80px;">날짜</th>
						<th style="width: 140px;">상태</th>
						<th style="width: 40px;">삭제</th>
                   </tr>
               </thead>
               <tbody>
                   <?php
                   for ($i=0; $i<count($list); $i++) {
                       $lt_class = ($i%2==0) ? "even" : "";
                   ?>
					<tr class="<?php echo $lt_class ?>">
						<td><?php echo $list[$i]['num']; ?></td>
						<td>
							<span class="design-badge <?php echo $list[$i]['design_color_class']; ?>">
								<?php echo $list[$i]['design_display']; ?>
							</span>
						</td>
						<td style="text-align: left; padding-left: 10px;">
							<a href="./sub_view3_detail.php?wr_id=<?php echo $list[$i]['wr_id']; ?>"  style="color: var(--text-primary); text-decoration: none;">
								<?php echo $list[$i]['subject']; ?>
							</a>
							<?php if ($list[$i]['icon_new']): ?>
								<span class="new-badge">N</span>
							<?php endif; ?>
						</td>
						<td><?php echo $list[$i]['mb_id']; ?></td>
						<td title="<?php echo $list[$i]['mb_hp']; ?>"><?php echo $list[$i]['mb_name']; ?></td>
						<td><?php echo $list[$i]['name']; ?></td>
						<td title="<?php echo $list[$i]['wr_datetime'] ?>"><?php echo $list[$i]['datetime2'] ?></td>
						<td>
							<div class="status_form" 
								 data-bo_table="<?php echo $bo_table ?>" 
								 data-wr_id="<?php echo $list[$i]['wr_id'] ?>" 
								 data-page="<?php echo $page ?>">
								<select name="wr_10" class="status_select">
									<option value="접수" <?php if ($list[$i]['wr_10'] == '접수' || empty($list[$i]['wr_10'])) echo 'selected'; ?>>접수</option>
									<option value="작업중" <?php if ($list[$i]['wr_10'] == '작업중') echo 'selected'; ?>>작업중</option>
									<option value="작업완료" <?php if ($list[$i]['wr_10'] == '작업완료') echo 'selected'; ?>>작업완료</option>
									<option value="송출중" <?php if ($list[$i]['wr_10'] == '송출중') echo 'selected'; ?>>송출중</option>
								</select>
								<button type="button" class="status_button" onclick="update_status(this.parentElement)">확인</button>
							</div>
						</td>
						<td>
							<button type="button" class="delete-btn" 
									onclick="confirmDelete('<?php echo htmlspecialchars($list[$i]['wr_id']); ?>', '<?php echo htmlspecialchars($list[$i]['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($list[$i]['subject'], ENT_QUOTES); ?>')"
									title="삭제">
								<i class="fa fa-trash"></i>
							</button>
						</td>
					</tr>
                   <?php } ?>

                   <?php if (count($list) == 0): ?>
                   <tr>
                       <td colspan="8" class="empty-state">
                           <div class="empty-state-icon">📋</div>
                           <div>포스터 신청을 기다리고 있습니다.</div>
                       </td>
                   </tr>
                   <?php endif; ?>
               </tbody>
           </table>
       </form>
   </div>

   <!-- Pagination -->
   <?php echo $pagination_html; ?>

</div>

<!-- Search Dialog -->
<div class="search-container bo_sch_wrap">
   <div class="search-dialog bo_sch">
       <h3 style="margin: 0 0 20px 0; font-size: 18px; color: var(--text-primary);">게시판 검색</h3>
       <form name="fsearch" method="get" class="search-form">
           <input type="hidden" name="rows" value="<?php echo $page_rows ?>">
		   <input type="hidden" name="filter_design" value="<?php echo $filter_design ?>">
           <input type="hidden" name="filter_status" value="<?php echo $filter_status ?>">
           <input type="hidden" name="filter_name" value="<?php echo $filter_name ?>">
           <input type="hidden" name="filter_start_date" value="<?php echo $filter_start_date ?>">
           <input type="hidden" name="filter_end_date" value="<?php echo $filter_end_date ?>">
           
           <div class="form-group">
               <label for="sfl" class="form-label">검색대상</label>
               <select name="sfl" id="sfl" class="form-select">
                   <option value="wr_subject" <?php echo ($sfl == 'wr_subject') ? 'selected' : ''; ?>>제목</option>
                   <option value="wr_content" <?php echo ($sfl == 'wr_content') ? 'selected' : ''; ?>>내용</option>
                   <option value="mb_id" <?php echo ($sfl == 'mb_id') ? 'selected' : ''; ?>>ID</option>
                   <option value="wr_name" <?php echo ($sfl == 'wr_name') ? 'selected' : ''; ?>>약국명</option>
               </select>
           </div>
           
           <div class="form-group">
               <label for="stx" class="form-label">검색어</label>
               <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required id="stx" class="form-input" placeholder="검색어를 입력해주세요">
           </div>
           
           <div class="search-actions">
               <button type="button" class="btn btn-secondary bo_sch_cls" style="width:70px">취소</button>
               <button type="submit" class="btn btn-primary" style="width:70px">
                   <i class="fa fa-search"></i> 검색
               </button>
           </div>
       </form>
   </div>
   <div class="bo_sch_bg"></div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// 상태 업데이트 함수
function update_status(element) {
   const bo_table = element.getAttribute('data-bo_table');
   const wr_id = element.getAttribute('data-wr_id');
   const page = element.getAttribute('data-page');
   const status = element.querySelector('select[name="wr_10"]').value;
   
   // AJAX 요청을 통한 상태 업데이트
   $.ajax({
       url: 'update_status.php',
       type: 'POST',
       data: {
           bo_table: bo_table,
           wr_id: wr_id,
           status: status,
           page: page
       },
       success: function(response) {
           if (response.trim() === 'success') {
               alert('상태가 업데이트되었습니다.');
               location.reload();
           } else {
               alert('업데이트에 실패했습니다.');
           }
       },
       error: function() {
           alert('서버 오류가 발생했습니다.');
       }
   });
}

// 삭제 확인 및 실행 함수
function confirmDelete(wrId, pharmacyName, title) {
    // 삭제 확인
    if (!confirm(`삭제하시겠습니까?\n\n약국명: ${pharmacyName}\n제목: ${title}`)) {
        return;
    }
    
    // AJAX로 삭제 실행
    $.ajax({
        url: 'delete_post.php',
        type: 'POST',
        data: {
            bo_table: 'poster_save',
            wr_id: wrId
        },
        success: function(response) {
            if (response.trim() === 'success') {
                alert('삭제가 완료되었습니다.');
                location.reload();
            } else {
                alert('삭제에 실패했습니다: ' + response);
            }
        },
        error: function() {
            alert('서버 오류가 발생했습니다.');
        }
    });
}

// URL 파라미터 업데이트 함수
function updateUrlParameter(param, value) {
   const url = new URL(window.location);
   url.searchParams.set(param, value);
   return url.toString();
}

// 기간 필터 적용 함수
function applyDateFilter() {
   const startDate = document.getElementById('startDate').value;
   const endDate = document.getElementById('endDate').value;
   
   const url = new URL(window.location);
   if (startDate) {
       url.searchParams.set('filter_start_date', startDate);
   } else {
       url.searchParams.delete('filter_start_date');
   }
   if (endDate) {
       url.searchParams.set('filter_end_date', endDate);
   } else {
       url.searchParams.delete('filter_end_date');
   }
   url.searchParams.delete('page');
   
   // 현재 테마 설정 유지
   const currentTheme = '<?php echo $theme_mode; ?>';
   url.searchParams.set('theme', currentTheme);
   
   window.location.href = url.toString();
}

// 페이지 행 수 변경 함수
function changeRowsPerPage(rows) {
   const url = new URL(window.location);
   url.searchParams.set('rows', rows);
   url.searchParams.delete('page');
   
   const currentTheme = document.querySelector('#bo_list').getAttribute('data-theme') || 'light';
   url.searchParams.set('theme', currentTheme);
   
   window.location.href = url.toString();
}

// 줄간격 변경 함수
function changeLineSpacing(spacing) {
   const url = new URL(window.location);
   url.searchParams.set('spacing', spacing);
   
   const currentTheme = document.querySelector('#bo_list').getAttribute('data-theme') || 'light';
   url.searchParams.set('theme', currentTheme);
   
   window.location.href = url.toString();
}

// 필터 적용 함수
function applyFilter(filterName, filterValue) {
   const url = new URL(window.location);
   if (filterValue) {
       url.searchParams.set(filterName, filterValue);
   } else {
       url.searchParams.delete(filterName);
   }
   url.searchParams.delete('page');
   
   // 현재 테마 설정 유지
   const currentTheme = '<?php echo $theme_mode; ?>';
   url.searchParams.set('theme', currentTheme);
   
   window.location.href = url.toString();
}

// 모든 필터 초기화 함수
function clearAllFilters() {
    const url = new URL(window.location);
    url.searchParams.delete('filter_design');
    url.searchParams.delete('filter_status');
    url.searchParams.delete('filter_name');
    url.searchParams.delete('filter_start_date');
    url.searchParams.delete('filter_end_date');
    url.searchParams.delete('page');
    
    // 현재 테마 설정 유지
    const currentTheme = '<?php echo $theme_mode; ?>';
    url.searchParams.set('theme', currentTheme);
    
    window.location.href = url.toString();
}

// DOMContentLoaded 이벤트
document.addEventListener('DOMContentLoaded', function() {
   // 테마 적용
   const urlParams = new URLSearchParams(window.location.search);
   const currentTheme = urlParams.get('theme') || 'light';
   document.querySelector('#bo_list').setAttribute('data-theme', currentTheme);
   
   // 필터 활성화 상태 체크
   function checkFilterStatus() {
       const filterClearBtn = document.querySelector('.filter-clear');
       const filterParams = ['filter_design', 'filter_status', 'filter_name', 'filter_start_date', 'filter_end_date'];
       
       let hasActiveFilter = false;
       
       const defaultStartDate = '<?php echo $default_start_date; ?>';
       const defaultEndDate = '<?php echo date('Y-m-d'); ?>';
       const currentStartDate = urlParams.get('filter_start_date');
       const currentEndDate = urlParams.get('filter_end_date');
       
       if (currentStartDate && currentStartDate !== defaultStartDate) {
           hasActiveFilter = true;
       }
       if (currentEndDate && currentEndDate !== defaultEndDate) {
           hasActiveFilter = true;
       }
       
       filterParams.forEach(param => {
           if (param.startsWith('filter_start_date') || param.startsWith('filter_end_date')) {
               return;
           }
           if (urlParams.get(param)) {
               hasActiveFilter = true;
           }
       });
       
       if (hasActiveFilter) {
           filterClearBtn.classList.add('active');
       } else {
           filterClearBtn.classList.remove('active');
       }
   }
   
   checkFilterStatus();
   
   // Search Dialog
   const searchBtn = document.querySelector('.btn_bo_sch');
   const searchContainer = document.querySelector('.bo_sch_wrap');
   const searchBg = document.querySelector('.bo_sch_bg');
   const searchClose = document.querySelector('.bo_sch_cls');
   
   if (searchBtn && searchContainer) {
       searchBtn.addEventListener('click', () => {
           searchContainer.style.display = 'flex';
       });
       
       [searchBg, searchClose].forEach(el => {
           if (el) {
               el.addEventListener('click', () => {
                   searchContainer.style.display = 'none';
               });
           }
       });
   }
});
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>