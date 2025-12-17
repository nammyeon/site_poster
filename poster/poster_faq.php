<?php
include_once("head.php");

// Check if user is logged in
if ($is_guest) {
    alert('회원이시라면 로그인 후 이용해 보십시오.', '/poster/');
}

// Check for required GnuBoard FAQ table variables
if (!isset($g5['faq_table']) || !isset($g5['faq_master_table'])) {
    die('<meta charset="utf-8">관리자 모드에서 게시판관리->FAQ관리를 먼저 확인해 주세요.');
}

// FAQ Master List
$faq_master_list = array();
$sql = "SELECT * FROM {$g5['faq_master_table']} ORDER BY fm_order, fm_id";
$result = sql_query($sql);
while ($row = sql_fetch_array($result)) {
    $key = $row['fm_id'];
    if (!isset($fm_id)) $fm_id = $key;
    $faq_master_list[$key] = $row;
}

$fm = array();
if (isset($fm_id) && $fm_id) {
    $fm_id = (int)$fm_id;
    $qstr .= '&fm_id=' . $fm_id; // Master FAQ key_id
    $fm = $faq_master_list[$fm_id];
}

if (!isset($fm['fm_id']) || !$fm['fm_id']) {
    alert('등록된 FAQ가 없습니다.');
}

$g5['title'] = $fm['fm_subject'];

// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$page_rows = 5; // Set to 5 FAQs per page
$from_record = ($page - 1) * $page_rows;

// Search handling
$stx = isset($stx) ? trim($stx) : '';
$sql_search = '';
if ($stx) {
    $sql_search = " AND ( INSTR(fa_subject, '$stx') > 0 OR INSTR(fa_content, '$stx') > 0 ) ";
}

// Total FAQ count
$sql = "SELECT COUNT(*) AS cnt FROM {$g5['faq_table']} WHERE fm_id = '$fm_id' $sql_search";
$total = sql_fetch($sql);
$total_count = $total['cnt'];
$total_pages = ceil($total_count / $page_rows);

// FAQ list query
$sql = "SELECT fa_id, fa_subject, fa_content 
        FROM {$g5['faq_table']} 
        WHERE fm_id = '$fm_id' $sql_search 
        ORDER BY fa_order ASC, fa_id DESC 
        LIMIT $from_record, $page_rows";
$result = sql_query($sql);
$faq_list = array();
for ($i = 0; $row = sql_fetch_array($result); $i++) {
    $faq_list[$i] = $row;
    if ($stx) {
        $faq_list[$i]['fa_subject'] = search_font($stx, conv_content($row['fa_subject'], 1));
        $faq_list[$i]['fa_content'] = search_font($stx, conv_content($row['fa_content'], 1));
    } else {
        $faq_list[$i]['fa_subject'] = conv_content(preg_replace('/^\[|\]$/', '', $row['fa_subject']), 1);
        $faq_list[$i]['fa_content'] = conv_content($row['fa_content'], 1);
    }
}

?>

<!-- Add CSS link -->
<link rel="stylesheet" href="<?php echo G5_URL ?>/poster/index.css">

<main>
    <section class="notices">
        <div class="inner-container">
            <h2 style="margin-bottom:inherit"><?php echo htmlspecialchars($fm['fm_subject']); ?></h2>
            <?php if ($total_count > 0) { ?>
                <ul class="notice-list">
                    <?php foreach ($faq_list as $row) { ?>
                        <li>
                            <a href="javascript:void(0)" class="notice-link truncate" 
                               data-id="<?php echo $row['fa_id']; ?>" 
                               data-content="<?php echo htmlspecialchars($row['fa_content']); ?>">
                                <?php echo $row['fa_subject']; ?>
                            </a>
                            <div class="notice-content" id="notice-content-<?php echo $row['fa_id']; ?>" style="display:none;">
                                <?php echo $row['fa_content']; ?>
                            </div>
                        </li>
                    <?php } ?>
                </ul>

                <!-- Pagination -->
                <div class="pagination">
                    <?php
                    $range = 5; // Displayed page range
                    $start_page = max(1, $page - floor($range / 2));
                    $end_page = min($total_pages, $start_page + $range - 1);

                    if ($start_page > 1) {
                        echo '<a href="?fm_id='.$fm_id.'&page=1" class="page-link">«</a>';
                        echo '<a href="?fm_id='.$fm_id.'&page='.($start_page - 1).'" class="page-link">‹</a>';
                    }

                    for ($i = $start_page; $i <= $end_page; $i++) {
                        echo '<a href="?fm_id='.$fm_id.'&page='.$i.'" class="page-link'.($i == $page ? ' active' : '').'">'.$i.'</a>';
                    }

                    if ($end_page < $total_pages) {
                        echo '<a href="?fm_id='.$fm_id.'&page='.($end_page + 1).'" class="page-link">›</a>';
                        echo '<a href="?fm_id='.$fm_id.'&page='.$total_pages.'" class="page-link">»</a>';
                    }
                    ?>
                </div>
            <?php } else { ?>
                <p class="no-notices">등록된 FAQ가 없습니다.</p>
            <?php } ?>
        </div>
    </section>
</main>

<!-- FAQ Slide Script -->
<script>
$(document).ready(function() {
    $('.notice-link').on('click', function(e) {
        e.preventDefault();
        var faqId = $(this).data('id');
        var contentDiv = $('#notice-content-' + faqId);

        // Toggle truncate class on the clicked link
        $(this).toggleClass('truncate');

        // Hide other FAQ contents and restore truncate class on other links
        $('.notice-content').not(contentDiv).slideUp(200);
        $('.notice-link').not(this).addClass('truncate');

        // Toggle clicked FAQ content
        contentDiv.slideToggle(200);
    });
});
</script>

<!-- FAQ Styles -->
<style>
.notice-list {
    list-style: none;
    padding: 0;
}
.notice-list li {
    padding: 10px 0;
}
.notice-link {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #333;
}
.notice-link.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.notice-content {
    padding: 15px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
}
.notice-content img {
    max-width: 100%;
    height: auto;
    display: block; /* Removes extra space below images */
}
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

/* Mobile-specific styles */
@media screen and (max-width: 768px) {
    .notice-content img {
        width: 100%; /* Ensures image fits container width */
        max-width: 100%;
        height: auto;
    }
    .inner-container {
        padding: 0 10px; /* Adds padding for better mobile display */
    }
}
</style>

<?php
$user_ip = $_SERVER['REMOTE_ADDR']; // 현재 접속한 사용자의 IP 주소를 가져옵니다.

if ($user_ip == '59.22.76.67') {
    include_once("tail2.php"); // IP가 59.22.76.67일 경우 tail2.php를 포함합니다.
} else {
    include_once("tail.php"); // 그 외의 모든 IP일 경우 tail.php를 포함합니다.
}
?>