<?php
include_once("head.php");

if (!isset($member['mb_id']) || !$is_member) {
    alert("로그인 후 이용해 주세요.");
    exit;
}

$mb_id = $member['mb_id'];

// 삭제 처리 로직
if (isset($_GET['delete']) && $_GET['delete']) {
    $delete_id = sql_escape_string($_GET['delete']); // wr_id 값
    $mb_id = sql_escape_string($member['mb_id']);

    // 삭제 쿼리 (보안 상 주의: SQL 인젝션 방지)
    $delete_sql = "DELETE FROM `wd_write_poster_save` WHERE `wr_id` = '$delete_id' AND `mb_id` = '$mb_id' LIMIT 1";
    sql_query($delete_sql);

    // 삭제 후 리다이렉트
    alert("선택된 포스터가 삭제되었습니다.");
    echo "<script>location.href='".$_SERVER['PHP_SELF']."';</script>";
    exit;
}

// Fetch saved posters for the current user from wd_write_poster_save
$sql = "SELECT * FROM `wd_write_poster_save` WHERE `mb_id` = '$mb_id' AND `wr_is_comment` = 0 ORDER BY `wr_datetime` DESC";
$result = sql_query($sql);
?>

<style>
/* 기본 스타일 */
.history-container {
    padding: 20px;
    max-width: 1200px;
    margin: 20px auto;
    font-size: 15px;
    color: #444;
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    padding-bottom: 70px; /* 하단 메뉴 높이 고려 */
    display: flex;
    flex-direction: column;
    align-items: center; /* 가운데 정렬 */
}

.history-container h1 {
    font-size: 26px;
    color: #3a4d69;
    font-weight: 700;
    margin-bottom: 10px;
    text-align: center; /* 텍스트 자체 가운데 정렬 */
}

.preview-notice-table {
    color: #dc3545;
    font-size: 12px;
    font-style: italic;
    margin-bottom: 10px;
    text-align: center; /* 텍스트 자체 가운데 정렬 */
}

/* 모바일 스타일 */
.scroll-hint-mobile {
    display: none; /* 기본적으로 숨김 */
    position: fixed;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(255, 255, 255, 0.9);
    padding: 10px;
    border-radius: 3px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    text-align: center;
    font-size: 13px;
    color: #777;
    z-index: 1000;
    width: 90%;
    max-width: 300px;
}

/* 테이블 래퍼 스타일 (스크롤 적용) */
.history-table-wrapper {
    width: 100%; /* 부모 컨테이너에 맞춰 너비 설정 */
    overflow-x: auto;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    border: 0px solid #e0e0e0; /* 테이블 래퍼에 테두리 추가 */
}

/* 테이블 스타일 */
.history-table {
    width: 100%;
    min-width: 800px;
    border-collapse: separate; /* 인접한 셀 테두리 분리 */
    border-spacing: 1px; /* 셀 사이의 간격 설정 (테두리 두께 역할) */
    background-color: #e0e0e0; /* 간격 색상 (테두리 색상) */
}

.history-table th, .history-table td {
    background-color: #fff; /* 셀 배경색 */
    padding: 12px 15px;
    text-align: center;
    white-space: nowrap;
    font-size: 14px;
}

.history-table th {
    background-color: #007bff;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
}

.history-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.history-table tr:last-child td {
    border-bottom: none;
}

.history-table tr:hover {
    background-color: #cccccc;
    transition: background-color 0.3s ease;
}

/* 포스터 미리보기 스타일 */
.poster-preview-cell {
    width: 120px; /* 이미지와 텍스트를 위해 공간 확보 */
    padding: 8px;
    text-align: center;
    position: relative; /* 텍스트 오버레이를 위한 position 설정 */
}

.poster-preview-mini {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.preview-text-overlay {
    position: absolute;
    top: 5px; /* 위쪽 여백 조절 */
    left: 5px; /* 왼쪽 여백 조절 */
    background-color: rgba(55, 55, 99, 0.9);
    color: white;
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 18px;
    font-weight: bold;
}

/* 컬러 미리보기 스타일 */
.color-preview {
    display: inline-block;
    width: 20px;
    height: 20px;
    margin-right: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    vertical-align: middle;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

/* 결과 없음 메시지 */
.no-results {
    text-align: center;
    padding: 20px;
    color: #6c757d;
    font-size: 16px;
    font-style: italic;
}

/* 삭제 버튼 스타일 */
.delete-button {
    color: #dc3545;
    text-decoration: none;
    padding: 5px 10px;
    border: 1px solid #dc3545;
    border-radius: 4px;
    font-size: 14px;
}

.delete-button:hover {
    background-color: #dc3545;
    color: white;
    text-decoration: none;
}

/* 컬러 미리보기 컨테이너 */
.color-preview-container {
    text-align: left;
    padding-left: 20px;
}

.color-preview-container div {
    margin-top: 3px;
}

/* 안내문구 셀 */
.guide-cell {
    min-width: 100px;
    white-space: pre-wrap; /* 줄바꿈 허용 */
    text-align: left; /* 좌측 정렬 */
    max-width: 200px; /* 최대 너비 제한 */
    word-wrap: break-word; /* 긴 단어 줄바꿈 */
}

/* 관리 셀 */
.manage-cell {
    min-width: 80px;
}

/* 타입 I 전용 스타일 */
.type-i-content {
    font-size: 12px;
    line-height: 1.4;
}

.type-i-content .content-group {
    margin-bottom: 8px;
}

.type-i-content .group-title {
    font-weight: bold;
    color: #007bff;
    margin-bottom: 3px;
}

.type-i-content .content-item {
    margin-bottom: 2px;
    color: #666;
}

/* 값이 없는 항목 스타일 */
.type-i-content .content-item.empty {
    color: #999;
    font-style: italic;
    font-size: 11px;
}

/* 모바일 스타일 */
@media (max-width: 768px) {
    .history-container {
        padding: 15px;
        margin: 15px auto;
        padding-bottom: 70px;
    }

    .history-container h1 {
        font-size: 22px;
        margin-bottom: 8px;
    }

    .preview-notice-table {
        font-size: 11px;
        margin-bottom: 8px;
    }

    .scroll-hint-mobile {
        display: block;
    }

    .history-table th, .history-table td {
        padding: 10px 8px;
        font-size: 12px;
    }

    .poster-preview-cell {
        width: 100px;
    }

    .preview-text-overlay {
        font-size: 18px;
        padding: 2px 5px;
    }

    .color-preview {
        width: 16px;
        height: 16px;
        margin-right: 3px;
    }

    .delete-button {
        padding: 4px 8px;
        font-size: 12px;
    }

    .guide-cell {
        max-width: 150px;
    }

    .type-i-content {
        font-size: 11px;
    }
}

@media (min-width: 769px) {
    .scroll-hint-mobile {
        display: none;
    }
}
</style>

<div class="history-container">
    <h1>포스터 신청 내역</h1>
    <p class="preview-notice-table">*미리보기는 참조용이며 실제와 동일하지 않습니다.</p>
	<p class="preview-notice-table"></p>
    <div class="history-table-wrapper">
        <div class="scroll-hint-mobile">가로로 스크롤하여 더 많은 정보를 확인하세요.</div>
        <?php if (sql_num_rows($result) > 0) { ?>
            <table class="history-table">
			<caption style="font-size:12px;color:darkred;text-align:right;">*삭제 버튼은 작업 시작전까지 노출됩니다.</caption>
                <thead>
                    <tr>
                        <th>선택 디자인</th>
                        <th style="display:none">기본 컬러</th>
                        <th>안내문구(A)</th>
                        <th>안내문구(B)</th>
                        <th>안내문구(C)</th>
                        <th>신청일</th>
                        <th>상태</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = sql_fetch_array($result)) {
                        $design = htmlspecialchars($row['wr_1']);
                        $design_type = htmlspecialchars($row['wr_7']);
                        $process = htmlspecialchars($row['wr_10']);
                        $reg_date = $row['wr_datetime'];
                        $wr_id = $row['wr_id'];

                        // 타입별로 데이터 처리
                        if ($design_type == 'I') {
                            // 타입 I의 경우
                            $topTitle = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_2'])));
                            $mainTitle = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_3'])));
                            $price = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_4'])));
                            $priceDetail = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_5'])));
                            $recommendText = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_6'])));
                            $benefit1 = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_8'])));
                            $benefit2 = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_9'])));
                            $benefit3 = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_link1'])));
                            $benefit4 = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_link2'])));
                            $pharmacy = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_homepage'])));
                            
                            // 안내문구 A: 제목 및 가격 정보 (모든 항목 항상 표시)
                            $guide_a = '<div class="type-i-content">';
                            $guide_a .= '<div class="content-group"><div class="group-title">상단 제목:</div><div class="content-item' . (empty(trim($topTitle)) ? ' empty' : '') . '">' . (empty(trim($topTitle)) ? '---' : nl2br($topTitle)) . '</div></div>';
                            $guide_a .= '<div class="content-group"><div class="group-title">메인 제목:</div><div class="content-item' . (empty(trim($mainTitle)) ? ' empty' : '') . '">' . (empty(trim($mainTitle)) ? '---' : nl2br($mainTitle)) . '</div></div>';
                            $guide_a .= '<div class="content-group"><div class="group-title">가격:</div><div class="content-item' . (empty(trim($price)) ? ' empty' : '') . '">' . (empty(trim($price)) ? '---' : nl2br($price)) . '</div></div>';
                            $guide_a .= '<div class="content-group"><div class="group-title">가격 상세:</div><div class="content-item' . (empty(trim($priceDetail)) ? ' empty' : '') . '">' . (empty(trim($priceDetail)) ? '---' : nl2br($priceDetail)) . '</div></div>';
                            $guide_a .= '</div>';
                            
                            // 안내문구 B: 혜택 정보 (모든 항목 항상 표시)
                            $guide_b = '<div class="type-i-content">';
                            $guide_b .= '<div class="content-group"><div class="group-title">혜택1:</div><div class="content-item' . (empty(trim($benefit1)) ? ' empty' : '') . '">' . (empty(trim($benefit1)) ? '---' : nl2br($benefit1)) . '</div></div>';
                            $guide_b .= '<div class="content-group"><div class="group-title">혜택2:</div><div class="content-item' . (empty(trim($benefit2)) ? ' empty' : '') . '">' . (empty(trim($benefit2)) ? '---' : nl2br($benefit2)) . '</div></div>';
                            $guide_b .= '<div class="content-group"><div class="group-title">혜택3:</div><div class="content-item' . (empty(trim($benefit3)) ? ' empty' : '') . '">' . (empty(trim($benefit3)) ? '---' : nl2br($benefit3)) . '</div></div>';
                            $guide_b .= '<div class="content-group"><div class="group-title">혜택4:</div><div class="content-item' . (empty(trim($benefit4)) ? ' empty' : '') . '">' . (empty(trim($benefit4)) ? '---' : nl2br($benefit4)) . '</div></div>';
                            $guide_b .= '</div>';
                            
                            // 안내문구 C: 추천 문구 및 약국 정보 (모든 항목 항상 표시)
                            $guide_c = '<div class="type-i-content">';
                            $guide_c .= '<div class="content-group"><div class="group-title">추천 문구:</div><div class="content-item' . (empty(trim($recommendText)) ? ' empty' : '') . '">' . (empty(trim($recommendText)) ? '---' : nl2br($recommendText)) . '</div></div>';
                            $guide_c .= '<div class="content-group"><div class="group-title">약국명:</div><div class="content-item' . (empty(trim($pharmacy)) ? ' empty' : '') . '">' . (empty(trim($pharmacy)) ? '---' : nl2br($pharmacy)) . '</div></div>';
                            $guide_c .= '</div>';
                            
                        } else {
	
                            // 기존 타입들 (A~H)의 경우
                            $name = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_2'])));
                            $hours = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_3'])));
                            $contact = stripslashes(html_entity_decode(htmlspecialchars_decode($row['wr_4'])));
                            $name_color = htmlspecialchars($row['wr_5']);
                            $hours_color = htmlspecialchars($row['wr_6']);
                            
                            $guide_a = nl2br($name);
                            $guide_b = nl2br($hours);
                            $guide_c = nl2br($contact);
                        }

                        $design_number_only = preg_replace("/[^0-9]/", "", $design);
                        $design_number_padded = str_pad(intval($design_number_only), 2, '0', STR_PAD_LEFT);
                        $background_image_mini = "images/pharm_post_{$design_type}{$design_number_padded}.jpg";
                        
                        if($design_type === "A"){
                            $standard_date = "2025-10-15";
                            if (date('Y-m-d', strtotime($reg_date)) >= date('Y-m-d', strtotime($standard_date))) {
                                $background_image_mini = "images/pharm_post_A{$design_number_padded}.jpg";
                            } else {
                                $background_image_mini = "images/pharm_post_S{$design_number_padded}.jpg";
                            }
                        } else {
                            $background_image_mini = "images/pharm_post_{$design_type}{$design_number_padded}.jpg";
                        }

                        $preview_text = strtoupper($design_type) . '-' . $design_number_only;

                        echo '<tr>';
                        echo '<td class="poster-preview-cell">';
                        echo '<div class="preview-text-overlay">' . $preview_text . '</div>';
                        echo '<img src="' . $background_image_mini . '" alt="Poster Preview" class="poster-preview-mini">';
                        echo '</td>';
                        
                        // 컬러 미리보기 (I타입에서는 숨김)
                        echo '<td class="color-preview-container" style="display:none">';
                        if ($design_type != 'I') {
                            if (in_array($design_type, ['A', 'B'])) {
                                $name_color_label = '(A)';
                                $hour_color_label = '(B)(C)';
                            } elseif (in_array($design_type, ['C', 'D', 'E'])) {
                                $name_color_label = '(A)(B)';
                                $hour_color_label = '(C)';
                            }

                            if (isset($name_color) && $name_color) {
                                echo '<span class="color-preview" style="background-color: ' . $name_color . ';"></span>' . $name_color_label . ': ' . $name_color;
                            } else {
                                echo '-';
                            }
                            if (isset($hours_color) && $hours_color) {
                                echo '<div><span class="color-preview" style="background-color: ' . $hours_color . ';"></span>' . $hour_color_label . ': ' . $hours_color . '</div>';
                            }
                        } else {
                            echo '-';
                        }
                        echo '</td>';
                        
                        echo '<td class="guide-cell">' . $guide_a . '</td>';
                        echo '<td class="guide-cell">' . $guide_b . '</td>';
                        echo '<td class="guide-cell">' . $guide_c . '</td>';
                        echo '<td>' . date('Y-m-d', strtotime($reg_date)) . '</td>';
                        echo '<td class="manage-cell">';

                        if (isset($process) && $process != "" && $process != "접수") {
                            echo $process;
                        } else {
                            echo '<a href="?delete=' . $wr_id . '" class="delete-button" onclick="return confirm(\'정말 삭제하시겠습니까?\');">삭제</a>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="no-results">저장된 포스터가 없습니다.</p>
        <?php } ?>
    </div>
</div>

<?php
$user_ip = $_SERVER['REMOTE_ADDR'];

if ($user_ip == '59.22.76.67') {
    include_once("tail2.php");
} else {
    include_once("tail.php");
}
?>


