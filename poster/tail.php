<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// Show navigation only if the user is logged in
if ($is_member) {
?>
<nav class="ph_navbar">
    <a href="<?php echo G5_URL ?>/poster/" class="ph_nav-item">
        <i class="fas fa-home"></i> <span>홈</span>
    </a>
    <a href="<?php echo G5_URL ?>/poster/poster_new.php" class="ph_nav-item">
        <i class="fas fa-plus"></i> <span>포스터신청</span>
    </a>
    <a href="<?php echo G5_URL ?>/poster/poster_history.php" class="ph_nav-item">
        <i class="fas fa-list"></i> <span>신청내역</span>
    </a>
    <a href="<?php echo G5_URL ?>/poster/poster_notice.php" class="ph_nav-item">
        <i class="fas fa-bullhorn"></i> <span>공지사항</span>
    </a>
    <a href="<?php echo G5_BBS_URL ?>/logout.php?url=<?php echo urlencode('/poster/index.php') ?>" class="ph_nav-item">
        <i class="fas fa-sign-out-alt"></i> <span>로그아웃</span>
    </a>
</nav>

<style>
    .ph_navbar {
        position: fixed;
        bottom: 0;
        width: 100%;
        background: linear-gradient(135deg, rgba(0, 87, 255, 0.95), rgba(57, 194, 255, 0.85));
        background-color: #0057ff;
        display: flex;
        justify-content: space-around;
        padding: 11px 0;
        z-index: 35;
    }

    .ph_nav-item {
        height: 41px;
        color: white;
        text-align: center;
        text-decoration: none;
        flex: 1;
        font-size: 14px;
    }

    .ph_nav-item i {
        color: white;
        display: block;
        font-size: 20px;
        margin-bottom: 3px;
        transition: color 0.3s ease; /* Smooth transition for color change */
    }

    .ph_nav-item span {
        color: white;
        display: block;
    }

    /* Hover effect: Only change the icon color */
    .ph_nav-item:hover i {
        color: #39c2ff; /* Change only the icon color on hover */
    }

    /* PC 화면에서의 스타일 */
    @media (min-width: 768px) {
        .ph_navbar {
            max-width: 500px;
            margin: 0 auto;
            left: 0;
            right: 0;
        }
    }
</style>

<?php
} // End of $is_member check
?>

</body>
</html>

<?php echo html_end(); // HTML 마지막 처리 함수 : 반드시 넣어주시기 바랍니다.