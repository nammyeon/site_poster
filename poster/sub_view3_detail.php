<?php
include_once('./_common.php');

if (!$member['mb_id'] || $member['mb_level'] < 9) {
    alert('접근 권한이 없습니다.');
}

// URL 파라미터로 게시물 ID 받기
$wr_id = isset($_GET['wr_id']) ? (int)$_GET['wr_id'] : 0;
$bo_table = 'poster_save';

if (!$wr_id) {
    alert('잘못된 접근입니다.');
}

// 게시물 정보 조회
$sql = "SELECT * FROM {$g5['write_prefix']}{$bo_table} WHERE wr_id = '{$wr_id}'";
$view = sql_fetch($sql);

if (!$view) {
    alert('존재하지 않는 게시물입니다.');
}

// Type I의 경우 필드 매핑이 다를 수 있으므로 확인 필요
// poster_new.php의 설정을 기반으로 한 매핑:

// Type I 필드 매핑:
// field1 -> wr_2 (상단 제목)
// field2 -> wr_3 (메인 제목) 
// field3 -> wr_4 (가격)
// field4 -> wr_5 (가격 상세)
// field5 -> wr_6 (추천 문구)
// field6 -> wr_8 (효능 1)
// field7 -> wr_9 (효능 2)
// field8 -> wr_link1 (효능 3)
// field9 -> wr_link2 (효능 4)
// field10 -> wr_homepage (약국명)

// 일반 타입 필드 매핑:
// field1 -> wr_2 (샘플 A)
// field2 -> wr_3 (샘플 B)
// field3 -> wr_4 (샘플 C)

// HTML 엔티티 디코딩 함수
function clean_entities($text) {
    $text = str_replace('&#038;#038;amp;', '&', $text);
    $text = str_replace('&#038;amp;', '&', $text);
    $text = str_replace('&amp;amp;', '&', $text);
    $text = str_replace('&amp;', '&', $text);
	$text = str_replace('&amp;', '&', $text);
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    $text = stripslashes($text);
    return $text;
}

// 회원 정보 조회
$member_info = get_member($view['mb_id']);

// 필드 데이터 정제
$design = get_text($view['wr_1']); // 디자인 번호
$name = get_text($view['wr_2']); // 이름
$hours = get_text($view['wr_3']); // 시간
$contact = get_text($view['wr_4']); // 연락처
$name_color = get_text($view['wr_5']); // 컬러1
$hours_color = get_text($view['wr_6']); // 컬러2
$design_type = get_text($view['wr_7']); // 디자인 타입

// 이미지 경로 생성
$w7 = $design_type;
$w1 = str_replace('design', '', $design);
$num = intval($w1) < 10 ? '0' . intval($w1) : intval($w1);
$imgSrc = "/poster/images/pharm_post_" . $w7 . $num . ".jpg";


// 삭제 처리
if (isset($_POST['delete_confirm']) && $_POST['delete_confirm'] == 'yes') {
    $delete_sql = "DELETE FROM {$g5['write_prefix']}{$bo_table} WHERE wr_id = '{$wr_id}'";
    if (sql_query($delete_sql)) {
        alert('게시물이 삭제되었습니다.', './sub_view3.php');
    } else {
        alert('삭제에 실패했습니다.');
    }
}

// 헤더 설정
$g5['title'] = '포스터 상세보기 - ' . clean_entities($view['wr_subject']);
include_once('./head.sub2.php');
?>

<style>
/* 폰트 임포트 */
@import url('https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.8/dist/web/static/pretendard.css');
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap');

#container {margin:0 auto;width:99%;min-width:1200px}

/* Modern Design System */
:root {
    --primary-color: #2563eb;
    --primary-hover: #1d4ed8;
    --secondary-color: #64748b;
    --success-color: #059669;
    --warning-color: #d97706;
    --danger-color: #dc2626;
    --bg-primary: #ffffff;
    --bg-secondary: #f8fafc;
    --bg-accent: #e2e8f0;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --border-color: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --radius: 8px;
    --radius-sm: 4px;
    --font-primary: 'Pretendard', 'Noto Sans KR', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
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
.detail-container {
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
    background: linear-gradient(135deg, #7c3aed, #3b82f6);
    color: white;
    padding: 15px 24px;
    border-radius: var(--radius);
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
}

.header h1 {
    font-size: 22px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

.header h1 i {
    background: rgba(255, 255, 255, 0.2);
    padding: 8px;
    border-radius: 8px;
}

.header-actions {
    display: flex;
    gap: 10px;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
    font-family: var(--font-primary);
}

.btn-primary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-primary:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
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

/* Article Info */
.article-info {
    background: var(--bg-secondary);
    padding: 20px;
    border-radius: var(--radius);
    margin-bottom: 20px;
    border: 1px solid var(--border-color);
}

.article-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.article-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    flex: 1;
    margin-right: 20px;
}

.article-meta {
    display: flex;
    gap: 20px;
    font-size: 14px;
    color: var(--text-secondary);
}

.article-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Design Info */
/* Design Preview 섹션 수정 - 기존 .design-preview 스타일 교체 */
.design-preview {
    display: flex;
    align-items: stretch; /* flex-start에서 stretch로 변경 */
    gap: 20px;
    background: var(--bg-primary);
   	background-color:#243143;
    padding: 20px;
    border-radius: var(--radius);
    border: 1px solid var(--border-color);
    margin-bottom: 20px;
    min-height: 400px;
}

.design-info-left {
    flex: 0 0 400px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.design-image-right {
    flex: 1;
    background-image: url('<?php echo $imgSrc; ?>');
    background-repeat: no-repeat;
    background-position: center center;
    background-size: contain;
    border-radius: var(--radius);
    position: relative;
	height:500px;
	display: flex;
	align-items: center;
	justify-content: center;
    /* min-height 제거하여 왼쪽 높이에 맞춤 */
}

.design-info-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: rgba(0, 0, 0, 0.8);
    padding: 10px;
    border-radius: var(--radius-sm);
    color: white;
    font-weight: 600;
    font-size: 12px;
}

.design-info-overlay span {
    display: block;
    margin-bottom: 5px;
}

.design-info-overlay span:last-child {
    margin-bottom: 0;
}

/* 포스터 텍스트 오버레이 스타일 */
.poster-text-overlay {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 335px;
    height: 100%;
    color: #000;
    padding: 8px;
    text-align: center;
    line-height: normal;
    pointer-events: none;
}

.poster-field {
    position: absolute;
    font-weight: 500;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 90%;
    margin: 0;
    line-height: 1.2;
}

/* Type I 전용 스타일 */
.poster-type-I .poster-field1 { 
    top: 9%; left: 50%; transform: translateX(-50%); 
    color: white; font-weight: 500; text-align: center; 
    width: 90%; font-size: 18px;
}
.poster-type-I .poster-field2 { 
    top: 13%; left: 50%; transform: translateX(-50%); 
    color: #FFD700; font-weight: 900; text-align: center; 
    width: 90%; font-size: 58px;
}
.poster-type-I .poster-price-area {
    position: absolute; top: 26%; left: 50%; transform: translateX(-50%);
    text-align: center; width: 90%;
}
.poster-type-I .poster-field3 { 
    color: white; font-weight: 900; display: inline-block; 
    margin: -34px 0px 0px -10px; font-size: 32px;
}
.poster-type-I .poster-field4 { 
    color: white; font-weight: 400; margin-top: 40px; font-size: 12px;
}
.poster-type-I .poster-field10 { 
    position: absolute; bottom: 4%; left: 50%; transform: translateX(-50%); 
    color: white; font-weight: 700; text-align: center; 
    width: 90%; font-size: 24px;
}



/* 기본 디자인 타입별 위치 설정 */
.poster-type-A .poster-field1 { top: 20%; left: 50%; transform: translateX(-50%); color: #000 !important;padding: 0 16px;font-size: 40px}
.poster-type-A .poster-field2 { top: 52%; left: 50%; transform: translate(-50%, -50%); padding: 0 10px;text-align: center;line-height: 1.4;font-weight: 700;font-size: 14px;}
.poster-type-A .poster-field3 { top: 80%; left: 50%; transform: translateX(-50%);padding: 0 20px;line-height: 1.4;font-weight: 700;font-size: 10px;text-align: left;}

.poster-type-S .poster-field1 { top: 9%; left: 50%; transform: translateX(-50%); color: #000 !important;padding: 0 16px;font-size: 40px}
.poster-type-S .poster-field2 { top: 58%; left: 50%; transform: translate(-50%, -50%); padding: 0 10px;text-align: center;line-height: 1.4;font-weight: 700;font-size: 14px;}
.poster-type-S .poster-field3 { top: 66%; left: 50%; transform: translateX(-50%);padding: 0 20px;line-height: 1.4;font-weight: 700;font-size: 10px;}

.poster-type-B .poster-field1 { top: 12%; left: 50%; transform: translateX(-50%); text-align: right; }
.poster-type-B .poster-field2 { top: 85%; left: 5%; text-align: center; }
.poster-type-B .poster-field3 { top: 75%; left: 5%; text-align: center; font-weight: 900; }

.poster-type-C .poster-field1 { top: 21%; left: 50%; transform: translateX(-50%); }
.poster-type-C .poster-field2 { top: 47%; left: 5%; text-align: center; font-weight: 900; }
.poster-type-C .poster-field3 { top: 35%; left: 5%; text-align: center; font-weight: 900; }

.poster-type-D .poster-field1 { top: 10%; left: 50%; transform: translateX(-50%); text-align: center; font-weight: 900; }
.poster-type-D .poster-field2 { top: 32%; left: 5%; text-align: center; font-weight: 900; }
.poster-type-D .poster-field3 { top: 73%; left: 22%; text-align: center; font-weight: 900; }

.poster-type-E .poster-field1 { top: 10%; left: 50%; transform: translateX(-50%); text-align: center; font-weight: 900; }
.poster-type-E .poster-field2 { top: 30%; left: 5%; text-align: center; font-weight: 900; }

.poster-type-F .poster-field1 { top: 10%; left: 50%; transform: translateX(-50%); }
.poster-type-F .poster-field2 { top: 31%; left: 5%; text-align: center; font-weight: 900; line-height: 1.8em; }

.poster-type-G .poster-field1 { top: 10%; left: 50%; transform: translateX(-50%); }
.poster-type-G .poster-field2 { top: 40%; left: 5%; text-align: center; font-weight: 900; line-height: 34px; }

.poster-type-H .poster-field1 { top: 10%; left: 50%; transform: translateX(-50%); }
.poster-type-H .poster-field2 { top: 28%; left: 5%; text-align: center; font-weight: 900; line-height: 40px; }

.poster-type-J .poster-field1 { top: 9%; font-weight: 900; }
.poster-type-J .poster-field2 { top: 40%; left: 5%; text-align: center; line-height: 30px; font-weight: 900; }

.poster-type-K .poster-field1 { top: 15%; }

.poster-type-L .poster-field1 { top: 92%; font-weight: 900; }

.poster-type-M .poster-field1 { top: 22%; font-weight: 900; }
.poster-type-M .poster-field2 { top: 40%; left: 5%; text-align: center; line-height: 40px; font-weight: 900; }

.poster-type-N .poster-field1 { top: 88.7%; font-weight: 900; }

.poster-type-O .poster-field1 { top: 8%; left: 50%; transform: translateX(-50%); }
.poster-type-O .poster-field2 { top: 69%; left: 5%; text-align: center; font-weight: 900; }
.poster-type-O .poster-field3 { top: 91%; left: 5%; text-align: center; }



/* Type T 전용 스타일 */
.poster-type-T .poster-field1 { width:100% ;top: 7%; left: 50%; transform: translateX(-50%); text-align:center;font-weight: 900; color: #fff;font-size: 30px; } 
.poster-type-T .poster-field2 { top: 36.2%; left: 50%; transform: translateX(-50%); text-align:center; color: #004356; font-weight: 700;font-size: 16px;}
.poster-type-T .poster-field3 { top: 52%;left: 50%; transform: translateX(-50%);line-height: 1.4; font-weight: 700; text-align:center;color: #004356; font-size: 16px;}



/* Type U 전용 스타일 */
.poster-type-U .poster-field1 { width:100% ;top: 43%; left: 50%; transform: translateX(-50%); text-align:center;font-weight: 700; color: #000;line-height: 1.4; font-size: 12px; } 
.poster-type-U .poster-field2 { top: auto;bottom: 2%; left: 50%; transform: translateX(-50%); text-align:center; color: #ffcb41; font-weight: 900; font-size: 20px;}

/* Type V 전용 스타일 */
.poster-type-V .poster-field1 { width:100% ;top: 18%; left: 50%; transform: translateX(-50%); text-align:center;font-weight: 900; color: #fff; font-size: 26px;} 
.poster-type-V .poster-field2 { top: 59%; left: 50%; transform: translate(-50%, -50%); text-align:center; color: #000; font-weight: 700;line-height: 1.4;font-size: 29px;}
.poster-type-V .poster-field3 { top: auto;bottom: 1.5%; left: 50%; transform: translateX(-50%); text-align:center; color: #0e00e9; font-weight: 900;font-size: 24px;}

/* Type W 전용 스타일 */
.poster-type-W .poster-field1 { width:100% ;top: 18%; left: 50%; transform: translateX(-50%); text-align:center;font-weight: 900; color: #1a1a1a;font-size: 26px; } 
.poster-type-W .poster-field2 { top: 61%; left: 50%; transform: translate(-50%, -50%); width: max-content; text-align:left; max-width: 100%; padding: 0 45px;color: #000; font-weight: 700;line-height: 1.4;font-size: 24px;}
.poster-type-W .poster-field3 { top: auto;bottom: 1.5%; left: 50%; transform: translateX(-50%); text-align:center; color: #47d0da; font-weight: 900;font-size: 24px;}

/* Type X 전용 스타일 */
.poster-type-X .poster-field1 { width:100% ;top: 18.5%; left: 50%; transform: translateX(-50%); padding: 0 25px; text-align:center;font-weight: 900; color: #1a1a1a;font-size: 25px; } 
.poster-type-X .poster-field2 { top: 63%; left: 50%; transform: translate(-50%, -50%); width: 100%; padding: 0 65px;color: #000; font-weight: 700;line-height: 1.2;text-align: center; font-size: 23px;}
.poster-type-X .poster-field3 { top: auto;bottom: 1.5%; left: 50%; transform: translateX(-50%); text-align:center; color: #26b6d9; font-weight: 900; font-size: 24px;}

@media all and (max-width:500px) {
    .poster-type-X .poster-field1 {font-size: 23px !important;}
    .poster-type-X .poster-field2 {font-size: 19px !important;}
}



/* Tables */
.info-table {
    background: var(--bg-secondary);
    border-radius: var(--radius);
    overflow: hidden;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
}

.table-header {
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    padding: 12px 15px;
    font-weight: 700;
    color: var(--primary-color);
    border-bottom: 2px solid var(--border-color);
    font-size: 14px;
}

.table-body {
    padding: 0;
}

.table-row {
    display: flex;
    border-bottom: 1px solid var(--border-color);
}

.table-row:last-child {
    border-bottom: none;
}

.table-row:nth-child(even) {
    background: #f9f9f9;
}

.table-label {
    flex: 0 0 100px;
    padding: 10px 12px;
    background: #f5f5f5;
    font-weight: 600;
    color: var(--text-primary);
    border-right: 1px solid var(--border-color);
    font-size: 13px;
}

.table-value {
    flex: 1;
    padding: 10px 12px;
    color: var(--text-primary);
    word-break: break-all;
    font-size: 13px;
}

.color-preview {
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 4px;
    margin-right: 8px;
    vertical-align: middle;
    border: 1px solid #ddd;
}

/* Special Content */
.special-content {
    background: var(--bg-secondary);
    padding: 20px;
    border-radius: var(--radius);
    border: 1px solid var(--border-color);
    margin-bottom: 20px;
}

.special-content h3 {
    color: var(--primary-color);
    font-size: 24px;
    margin-bottom: 15px;
    text-align: center;
}

.special-content p {
    font-size: 16px;
    line-height: 1.8;
    color: var(--text-primary);
}

/* Actions */
.article-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    padding: 20px 0;
    border-top: 1px solid var(--border-color);
}

/* Modal */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-dialog {
    background: var(--bg-primary);
    padding: 30px;
    border-radius: var(--radius);
    width: 90%;
    max-width: 400px;
    box-shadow: var(--shadow-md);
}

.modal-header {
    margin-bottom: 20px;
}

.modal-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.modal-body {
    margin-bottom: 20px;
    color: var(--text-secondary);
    line-height: 1.6;
}

.modal-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

/* Responsive */
@media (max-width: 768px) {
    .info-tables {
        grid-template-columns: 1fr;
    }
    
    .article-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .article-meta {
        flex-wrap: wrap;
    }
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="detail-container">
    <div class="header">
        <h1><i class="fas fa-image"></i> 포스터 상세보기</h1>
        <div class="header-actions">
            <a href="./sub_view3.php" class="btn btn-primary">
                <i class="fa fa-list"></i>
            </a>
        </div>
    </div>

    <div class="article-info">
        <div class="article-header">
            <h1 class="article-title"><?php echo clean_entities($view['wr_subject']); ?></h1>
        </div>
        
        <div class="article-meta">
            <span><i class="fa fa-user"></i> <?php echo $view['wr_name']; ?></span>
            <span><i class="fa fa-id-card"></i> <?php echo $member_info['mb_name']; ?> (<?php echo $view['mb_id']; ?>)</span>
            <span><i class="fa fa-clock"></i> <?php echo date("Y-m-d H:i", strtotime($view['wr_datetime'])); ?></span>
            <span><i class="fa fa-eye"></i> 조회 <?php echo number_format($view['wr_hit']); ?>회</span>
        </div>
    </div>

<div class="design-preview">
        <div class="design-info-left">
            <!-- 디자인 정보 테이블 -->
            <div class="info-table">
                <div class="table-header">
                    <i class="fa fa-palette"></i> 디자인 정보
                </div>
                <div class="table-body">
                    <div class="table-row">
                        <div class="table-label">디자인 타입</div>
                        <div class="table-value"><?php echo htmlspecialchars($design_type); ?></div>
                    </div>
                    <div class="table-row">
                        <div class="table-label">디자인 번호</div>
                        <div class="table-value"><?php echo htmlspecialchars($design); ?></div>
                    </div>
                </div>
            </div>

            <!-- 입력 정보 테이블 -->
            <div class="info-table">
                <div class="table-header">
                    <i class="fa fa-edit"></i> 입력 정보
                </div>
                <div class="table-body">
                    <div class="table-row">
                        <div class="table-label">항목 A</div>
                        <div class="table-value"><?php echo nl2br(clean_entities($name)); ?></div>
                    </div>
                    <div class="table-row">
                        <div class="table-label">항목 B</div>
                        <div class="table-value"><?php echo nl2br(htmlspecialchars_decode(clean_entities($hours))); ?></div>
                    </div>
                    <div class="table-row">
                        <div class="table-label">항목 C</div>
                        <div class="table-value"><?php echo nl2br(clean_entities($contact)); ?></div>
                    </div>
                    <?php if($design_type == 'I'): ?>
                    <div class="table-row">
                        <div class="table-label">항목 D</div>
                        <div class="table-value"><?php echo nl2br(clean_entities($view['wr_5'])); ?></div>
                    </div>
                    <div class="table-row">
                        <div class="table-label">항목 E</div>
                        <div class="table-value"><?php echo nl2br(clean_entities($view['wr_6'])); ?></div>
                    </div>
                    <div class="table-row">
                        <div class="table-label">항목 F</div>
                        <div class="table-value"><?php echo nl2br(clean_entities($view['wr_8'])); ?></div>
                    </div>
                    <div class="table-row">
                        <div class="table-label">항목 G</div>
                        <div class="table-value"><?php echo nl2br(clean_entities($view['wr_9'])); ?></div>
                    </div>
                    <div class="table-row">
                        <div class="table-label">항목 H</div>
                        <div class="table-value"><?php echo nl2br(clean_entities(get_text($view['wr_link1']))); ?></div>
                    </div>
                    <div class="table-row">
                        <div class="table-label">항목 I</div>
                        <div class="table-value"><?php echo nl2br(clean_entities($view['wr_link2'])); ?></div>
                    </div>
                    <div class="table-row">
                        <div class="table-label">항목 J</div>
                        <div class="table-value"><?php echo nl2br(clean_entities($view['wr_homepage'])); ?></div>
                    </div>					
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
		<div class="design-image-right poster-type-<?php echo $design_type; ?>">
            <div class="design-info-overlay">
                <span>Type: <?php echo htmlspecialchars($design_type); ?></span>
                <span>Design: <?php echo htmlspecialchars($w1); ?></span>
            </div>
            
            <!-- 포스터 텍스트 오버레이 추가 -->
            <div class="poster-text-overlay">
                <?php if ($design_type === 'I'): ?>
                    <!-- Type I 전용 레이아웃 -->
                    <div class="poster-field poster-field1"><?php echo clean_entities($view['wr_2']); ?></div>
                    <div class="poster-field poster-field2"><?php echo clean_entities($view['wr_3']); ?></div>
                    <div class="poster-price-area">
                        <div class="poster-field poster-field3"><?php echo clean_entities($view['wr_4']); ?></div>
                        <div class="poster-field poster-field4"><?php echo clean_entities($view['wr_5']); ?></div>
                    </div>
                    <div class="poster-field poster-field10"><?php echo clean_entities($view['wr_homepage']); ?></div>
                <?php else: ?>
                    <!-- 일반 디자인 타입 레이아웃 -->
                    <?php if (!empty($view['wr_2'])): ?>
                        <div class="poster-field poster-field1"><?php echo nl2br(clean_entities($view['wr_2'])); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($view['wr_3'])): ?>
                        <div class="poster-field poster-field2"><?php echo nl2br(clean_entities($view['wr_3'])); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($view['wr_4'])): ?>
                        <div class="poster-field poster-field3"><?php echo nl2br(clean_entities($view['wr_4'])); ?></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>




    <div class="article-actions">
        <a href="./sub_view3.php" class="btn btn-secondary" style="width:inherit;">
            <i class="fa fa-list"></i> 목록으로
        </a>
        
        <?php if ($is_admin == 'super' || $member['mb_id'] == $view['mb_id']): ?>
        <button type="button" class="btn btn-danger" onclick="confirmDelete()" style="width:inherit;">
            <i class="fa fa-trash"></i> 삭제
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- 삭제 확인 모달 -->
<div id="deleteModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title">게시물 삭제</h3>
        </div>
        <div class="modal-body">
            정말로 이 게시물을 삭제하시겠습니까?<br>
            삭제된 게시물은 복구할 수 없습니다.
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">취소</button>
            <form method="post" style="display: inline;">
                <input type="hidden" name="delete_confirm" value="yes">
                <button type="submit" class="btn btn-danger">삭제</button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// 모달 외부 클릭시 닫기
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>