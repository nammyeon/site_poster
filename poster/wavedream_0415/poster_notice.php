<?php
include_once("head.php");

// 페이지네이션 설정
$list_per_page = 5; // 한 페이지에 표시할 공지사항 수
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $list_per_page;

// 전체 공지사항 수 조회
$total_sql = "SELECT COUNT(*) as cnt FROM {$g5['write_prefix']}notice WHERE wr_is_comment = 0 ";
$total_result = sql_fetch($total_sql);
$total_count = $total_result['cnt'];
$total_pages = ceil($total_count / $list_per_page);

// 공지사항 목록 조회
$sql = "SELECT wr_id, wr_subject, wr_content, wr_datetime 
        FROM {$g5['write_prefix']}notice 
        WHERE wr_is_comment = 0 
        ORDER BY wr_datetime DESC 
        LIMIT $start, $list_per_page";
$result = sql_query($sql);
?>

<!-- Add CSS link in the same directory -->
<link rel="stylesheet" href="<?php echo G5_URL ?>/poster/index.css">

<main>
    <section class="notices">
        <div class="inner-container">
            <h2>공지사항</h2>
            <?php if ($total_count > 0) { ?>
                <ul class="notice-list">
                    <?php while ($row = sql_fetch_array($result)) { ?>
                        <li>
                            <a href="javascript:void(0)" class="notice-link" 
                               data-id="<?php echo $row['wr_id']; ?>" 
                               data-subject="<?php echo htmlspecialchars($row['wr_subject']); ?>" 
                               data-content="<?php echo htmlspecialchars($row['wr_content']); ?>" 
                               data-datetime="<?php echo $row['wr_datetime']; ?>">
                                [<?php echo date('m.d', strtotime($row['wr_datetime'])); ?>] <?php echo htmlspecialchars($row['wr_subject']); ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>

                <!-- 페이지네이션 -->
                <div class="pagination">
                    <?php
                    $range = 5; // 표시할 페이지 범위
                    $start_page = max(1, $page - floor($range / 2));
                    $end_page = min($total_pages, $start_page + $range - 1);

                    if ($start_page > 1) {
                        echo '<a href="?page=1" class="page-link">«</a>';
                        echo '<a href="?page='.($start_page - 1).'" class="page-link">‹</a>';
                    }

                    for ($i = $start_page; $i <= $end_page; $i++) {
                        echo '<a href="?page='.$i.'" class="page-link'.($i == $page ? ' active' : '').'">'.$i.'</a>';
                    }

                    if ($end_page < $total_pages) {
                        echo '<a href="?page='.($end_page + 1).'" class="page-link">›</a>';
                        echo '<a href="?page='.$total_pages.'" class="page-link">»</a>';
                    }
                    ?>
                </div>
            <?php } else { ?>
                <p class="no-notices">등록된 공지사항이 없습니다.</p>
            <?php } ?>
        </div>
    </section>
</main>

<!-- 공지사항 내용 팝업 -->
<div class="popup-overlay" id="notice-overlay"></div>
<div class="notice-popup" id="notice-popup">
    <div class="notice-header">
        <h3 id="notice-title">[Title will be inserted here]</h3>
        <span class="close-btn" onclick="hideNoticePopup()">×</span>
    </div>
    <div class="notice-content" id="notice-content">
        <!-- 공지사항 내용이 여기에 동적으로 삽입됩니다 -->
    </div>
</div>

<!-- 팝업 스크립트 -->
<script>
$(document).ready(function() {
    // 공지사항 팝업 열기
    $('.notice-link').on('click', function(e) {
        e.preventDefault();
        var wrId = $(this).data('id');
        var subject = $(this).data('subject');
        var content = $(this).data('content');

        if (content) {
            $('#notice-content').html(content.replace(/\n/g, '<br>')); // 줄바꿈 처리
            $('#notice-title').text(subject); // 제목 설정
            $('#notice-overlay').fadeIn();
            $('#notice-popup').slideDown(200);
        } else {
            alert('공지사항 내용을 찾을 수 없습니다.');
        }
    });

    // 팝업 닫기 (공지사항)
    window.hideNoticePopup = function() {
        $('#notice-popup').slideUp(200, function() {
            $('#notice-overlay').fadeOut();
            $('#notice-content').empty(); // 내용 비우기
        });
    };

    // 오버레이 클릭으로 닫기
    $('#notice-overlay').on('click', function() {
        hideNoticePopup();
    });

    // 팝업 내부 클릭 시 닫히지 않도록
    $('.notice-popup').on('click', function(e) {
        e.stopPropagation();
    });
});
</script>

<!-- 페이지네이션 스타일 -->
<style>
.pagination {
    text-align: center;
    margin: 20px 0;
}
.page-link {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
}
.page-link:hover {
    background-color: #f5f5f5;
}
.page-link.active {
    background-color: #007bff;
    color: #fff;
    border-color: #007bff;
}
.no-notices {
    text-align: center;
    padding: 20px;
    color: #666;
}
</style>

<?php include_once("tail.php"); ?>