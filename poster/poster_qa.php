<?php
include_once("head.php");

// Check if user is logged in
if ($is_guest) {
    alert('회원이시라면 로그인 후 이용해 보십시오.', './login.php?url='.urlencode(G5_BBS_URL.'/poster_qa.php'));
}

// Get QA configuration
$qaconfig = get_qa_config();

$token = '';
if ($is_admin) {
    $token = _token();
    set_session('ss_qa_delete_token', $token);
}

$g5['title'] = $qaconfig['qa_title'];

// Category options
$category_option = '';
if ($qaconfig['qa_category']) {
    $category_href = G5_BBS_URL.'/poster_qa.php';
    $category_option .= '<li><a href="'.$category_href.'"'.($sca == '' ? ' id="bo_cate_on"' : '').'>전체</a></li>';
    $categories = explode('|', $qaconfig['qa_category']);
    for ($i = 0; $i < count($categories); $i++) {
        $category = trim($categories[$i]);
        if ($category == '') continue;
        $category_option .= '<li><a href="'.($category_href.'?sca='.urlencode($category)).'"'.($category == $sca ? ' id="bo_cate_on"' : '').'>'.$category.'</a></li>';
    }
}

// Query setup
$sql_common = " FROM {$g5['qa_content_table']} ";
$sql_search = " WHERE qa_type = '0' ";
if (!$is_admin) {
    $sql_search .= " AND mb_id = '{$member['mb_id']}' ";
}
if ($sca) {
    if (preg_match("/[a-zA-Z]/", $sca)) {
        $sql_search .= " AND INSTR(LOWER(qa_category), LOWER('$sca')) > 0 ";
    } else {
        $sql_search .= " AND INSTR(qa_category, '$sca') > 0 ";
    }
}

$stx = trim($stx);
if ($stx) {
    $sfl = trim($sfl) ?: 'qa_subject';
    if (!in_array($sfl, ['qa_subject', 'qa_content', 'qa_name', 'mb_id'])) {
        $sfl = 'qa_subject';
    }
    $sql_search .= " AND (`{$sfl}` LIKE '%{$stx}%') ";
}

$sql_order = " ORDER BY qa_datetime DESC ";

// Total count
$sql = "SELECT COUNT(*) AS cnt $sql_common $sql_search";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$page_rows = G5_IS_MOBILE ? $qaconfig['qa_mobile_page_rows'] : $qaconfig['qa_page_rows'];
$from_record = ($page - 1) * $page_rows;
$total_pages = ceil($total_count / $page_rows);

// Fetch QA list
$sql = "SELECT * $sql_common $sql_search $sql_order LIMIT $from_record, $page_rows";
$result = sql_query($sql);
$list = array();
$num = $total_count - ($page - 1) * $page_rows;
$subject_len = G5_IS_MOBILE ? $qaconfig['qa_mobile_subject_len'] : $qaconfig['qa_subject_len'];
for ($i = 0; $row = sql_fetch_array($result); $i++) {
    $list[$i] = $row;
    $list[$i]['category'] = get_text($row['qa_category']);
    $list[$i]['subject'] = conv_subject(preg_replace('/^\[|\]$/', '', $row['qa_subject']), $subject_len, '…');
    $list[$i]['content'] = conv_content($row['qa_content'], 1);
    if ($stx) {
        $list[$i]['subject'] = search_font($stx, $list[$i]['subject']);
        $list[$i]['content'] = search_font($stx, $list[$i]['content']);
    }
    $list[$i]['date'] = substr($row['qa_datetime'], 2, 8);
    $list[$i]['num'] = $num - $i;

    // Fetch answer (qa_type = 1, same qa_parent)
    $answer_sql = "SELECT * FROM {$g5['qa_content_table']} WHERE qa_type = '1' AND qa_parent = '{$row['qa_id']}'";
    $list[$i]['answer'] = sql_fetch($answer_sql);
    if ($list[$i]['answer']) {
        $list[$i]['answer']['content'] = conv_content($list[$i]['answer']['qa_content'], 1);
    }
}

$is_checkbox = $is_admin;
$admin_href = $is_admin ? G5_ADMIN_URL.'/qa_config.php' : '';
$write_href = G5_BBS_URL.'/qawrite.php';

?>

<!-- Add CSS link -->
<link rel="stylesheet" href="<?php echo G5_URL ?>/poster/index.css">

<main>
    <section class="notices" style="padding:10px;">
        <div class="inner-container" style="position:relative;">
            <h2><?php echo htmlspecialchars($qaconfig['qa_title']); ?></h2>
            <?php if ($total_count > 0) { ?>
                <ul class="notice-list">
                    <?php foreach ($list as $row) { ?>
                        <li style="padding:0;border:inherit;">
                            <?php if ($is_checkbox) { ?>
                                <input type="checkbox" name="chk_qa_id[]" value="<?php echo $row['qa_id']; ?>" class="qa-checkbox">
                            <?php } ?>
                            <a href="javascript:void(0)" class="notice-link" 
                               data-id="<?php echo $row['qa_id']; ?>" 
                               data-subject="<?php echo htmlspecialchars($row['subject']); ?>" 
                               data-content="<?php echo htmlspecialchars($row['content']); ?>"
                               data-category="<?php echo htmlspecialchars($row['category']); ?>"
                               data-date="<?php echo $row['date']; ?>"
                               data-name="<?php echo htmlspecialchars($row['qa_name']); ?>"
                               data-email="<?php echo htmlspecialchars($row['qa_email']); ?>"
                               data-hp="<?php echo htmlspecialchars($row['qa_hp']); ?>"
                               data-answer="<?php echo $row['answer'] ? htmlspecialchars($row['answer']['content']) : ''; ?>"
                               data-answer-id="<?php echo $row['answer'] ? $row['answer']['qa_id'] : ''; ?>">
                                [<?php echo date('m.d', strtotime($row['qa_datetime'])); ?>] 
								<?php if ($row['answer']) { ?>
                                    <span class="answer-status">답변완료</span>
                                <?php } ?>
                                <?php echo $row['subject']; ?>

                            </a>
                        </li>
                    <?php } ?>
                </ul>

                <!-- Pagination -->
                <div class="pagination">
                    <?php
                    $range = 5;
                    $start_page = max(1, $page - floor($range / 2));
                    $end_page = min($total_pages, $start_page + $range - 1);

                    if ($start_page > 1) {
                        echo '<a href="?sca='.urlencode($sca).'&page=1" class="page-link">«</a>';
                        echo '<a href="?sca='.urlencode($sca).'&page='.($start_page - 1).'" class="page-link">‹</a>';
                    }

                    for ($i = $start_page; $i <= $end_page; $i++) {
                        echo '<a href="?sca='.urlencode($sca).'&page='.$i.'" class="page-link'.($i == $page ? ' active' : '').'">'.$i.'</a>';
                    }

                    if ($end_page < $total_pages) {
                        echo '<a href="?sca='.urlencode($sca).'&page='.($end_page + 1).'" class="page-link">›</a>';
                        echo '<a href="?sca='.urlencode($sca).'&page='.$total_pages.'" class="page-link">»</a>';
                    }
                    ?>
                </div>
            <?php } else { ?>
                <p class="no-notices">등록된 문의가 없습니다.</p>
            <?php } ?>
            <?php if ($write_href) { ?>
                <div class="write-link" style="position:absolute;top:10px;right:10px;">
                    <a href="<?php echo $write_href; ?>" class="btn-write">작성하기</a>
                </div>
            <?php } ?>
        </div>
    </section>
</main>

<!-- QA content popup -->
<div class="popup-overlay" id="qa-overlay"></div>
<div class="notice-popup" id="qa-popup" style="display:none">
    <div class="notice-header">
        <h3 id="qa-title">[Title will be inserted here]</h3>
        <span class="close-btn" onclick="hideQaPopup()" style="right:3px;">×</span>
    </div>
    <div class="notice-content">
        <div id="qa-info">
            <strong id="qa-category"  style="color:#007bff"></strong>
            <!-- <span id="qa-name"></span> |  --><span id="qa-date"></span>
        </div>
        <div id="qa-content"></div>
        <div id="qa-answer" style="display:none;">
            <h4 style="color:#007bff">답변</h4>
            <div id="qa-answer-content"></div>
        </div>
    </div>
</div>

<!-- QA Popup Script -->
<script>
$(document).ready(function() {
    $('.notice-link').on('click', function(e) {
        e.preventDefault();
        var qaId = $(this).data('id');
        var subject = $(this).data('subject');
        var content = $(this).data('content');
        var category = $(this).data('category');
        var date = $(this).data('date');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var hp = $(this).data('hp');
        var answer = $(this).data('answer');
        var answerId = $(this).data('answer-id');

        if (content) {
            $('#qa-title').text(subject);
            $('#qa-category').text(category);
            $('#qa-name').text(name);
            $('#qa-date').text(date);
            $('#qa-content').html(content.replace(/\n/g, '<br>'));
            if (email || hp) {
                $('#qa-email').text(email || '없음');
                $('#qa-hp').text(hp || '없음');
                $('#qa-contact').show();
            } else {
                $('#qa-contact').hide();
            }
            if (answer) {
                $('#qa-answer-content').html(answer.replace(/\n/g, '<br>'));
                $('#qa-answer').show();
                $('#qa-answer-form').hide();
            } else if (<?php echo $is_admin ? 'true' : 'false'; ?>) {
                $('#qa-answer-id').val(qaId);
                $('#qa-answer-form').show();
                $('#qa-answer').hide();
            } else {
                $('#qa-answer').hide();
                $('#qa-answer-form').hide();
            }
            $('#qa-overlay').fadeIn();
            $('#qa-popup').slideDown(200);
        } else {
            alert('문의 내용을 찾을 수 없습니다.');
        }
    });

    window.hideQaPopup = function() {
        $('#qa-popup').slideUp(200, function() {
            $('#qa-overlay').fadeOut();
            $('#qa-content').empty();
            $('#qa-answer-content').empty();
        });
    };

    $('#qa-overlay').on('click', function() {
        hideQaPopup();
    });

    $('.notice-popup').on('click', function(e) {
        e.stopPropagation();
    });

    window.editQa = function() {
        var qaId = $('#qa-answer-id').val();
        window.location.href = '<?php echo G5_BBS_URL; ?>/qawrite.php?w=u&qa_id=' + qaId;
    };

    window.deleteQa = function() {
        if (confirm('정말 삭제하시겠습니까?')) {
            var qaId = $('#qa-answer-id').val();
            window.location.href = '<?php echo G5_BBS_URL; ?>/qadelete.php?qa_id=' + qaId + '&token=<?php echo $token; ?>';
        }
    };
});
</script>

<!-- QA Styles -->
<style>
.inner-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}
h2 {
    font-size: 1.8em;
    margin-bottom: 20px;
    color: #333;
}
.category-list {
    list-style: none;
    padding: 0;
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.category-list li {
    display: inline-block;
}
.category-list a {
    padding: 8px 12px;
    text-decoration: none;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 4px;
    transition: all 0.2s ease;
}
.category-list a#bo_cate_on {
    background-color: #007bff;
    color: #fff;
    border-color: #007bff;
}
.category-list a:hover {
    background-color: #f5f5f5;
}
.notice-list {
    list-style: none;
    padding: 0;
}
.qa-checkbox {
    margin-right: 10px;
    vertical-align: middle;
}
.notice-link {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #333;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 8px;
    transition: background-color 0.2s ease;
    white-space: nowrap; /* Prevents text from wrapping */
    overflow: hidden; /* Hides any overflowing text */
    text-overflow: ellipsis; /* Adds ellipsis for truncated text */
}
.notice-link:hover {
    background-color: #f5f5f5;
}
.answer-status {
    display: inline-block;
    padding: 2px 8px;
    margin-left: 5px;
    font-size: 0.85em;
    color: #007bff;
    background-color: #e7f3ff;
    border-radius: 4px;
}
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    z-index: 1000;
}
.notice-popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    width: 90%;
    max-width: 600px;
    border-radius: 8px;
    z-index: 1001;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}
.notice-header {
    padding: 15px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.notice-header h3 {
    margin: 0;
    font-size: 1.2em;
}
.close-btn {
    cursor: pointer;
    font-size: 1.5em;
    color: #666;
}
.close-btn:hover {
    color: #333;
}
.notice-content {
    padding: 15px;
}
#qa-info {
    margin-bottom: 10px;
    color: #666;
}
#qa-admin-actions {
    margin-bottom: 10px;
}
.btn-admin-action {
    padding: 5px 10px;
    margin-right: 5px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s ease;
}
.btn-admin-action:hover {
    background-color: #0056b3;
}
#qa-contact p {
    margin: 5px 0;
}
#qa-answer {
    margin-top: 20px;
    padding-top: 10px;
    border-top: 1px solid #ddd;
}
#qa-answer-form {
    margin-top: 20px;
    padding-top: 10px;
    border-top: 1px solid #ddd;
}
#qa-answer-text {
    width: 100%;
    height: 100px;
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
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
    transition: all 0.2s ease;
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
.write-link, .admin-link {
    text-align: right;
    margin: 10px 0;
}
.btn-write, .btn-admin {
    display: inline-block;
    padding: 8px 12px;
    background-color: #007bff;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}
.btn-write:hover, .btn-admin:hover {
    background-color: #0056b3;
}
</style>

<?php
$user_ip = $_SERVER['REMOTE_ADDR'];
if ($user_ip == '59.22.76.67') {
    include_once("tail2.php");
} else {
    include_once("tail.php");
}
?>