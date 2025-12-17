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
        <i class="fas fa-folder-open"></i> <span>신청내역</span>
    </a>
    <a href="<?php echo G5_URL ?>/poster/poster_notice.php" class="ph_nav-item">
        <i class="fas fa-bullhorn"></i> <span>공지사항</span>
    </a>
    <div class="ph_nav-item hamburger-menu">
        <i class="fas fa-bars"></i> <span>메뉴</span>
        <div class="dropdown-menu" id="hamburger-dropdown">
            <a href="<?php echo G5_URL ?>/poster/poster_qa.php" class="dropdown-item">1:1문의</a>
			<a href="<?php echo G5_URL ?>/poster/poster_faq.php" class="dropdown-item">자주하는질문</a>
            <a href="<?php echo G5_BBS_URL ?>/logout.php?url=<?php echo urlencode('/poster/index.php') ?>" class="dropdown-item">로그아웃</a>
        </div>
    </div>
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

    /* New styles for hamburger menu and dropdown */
    .hamburger-menu {
        position: relative;
        cursor: pointer;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        bottom: 60px; /* Position above the navbar */
        right: 0;
        background: linear-gradient(135deg, rgba(0, 87, 255, 0.95), rgba(57, 194, 255, 0.85));
        background-color: #0057ff;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2);
        border-radius: 4px;
        min-width: 120px;
        z-index: 40;
    }

    .dropdown-menu.active {
        display: block;
    }

    .dropdown-item {
        display: block;
        padding: 10px 15px;
        color: white;
        text-decoration: none;
        font-size: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        text-align: center;
    }

    .dropdown-item:last-child {
        border-bottom: none;
    }

    .dropdown-item:hover {
        color: #39c2ff;
        background: rgba(255, 255, 255, 0.1);
    }
</style>

<script>
    // Hamburger menu toggle
    $(document).ready(function() {
        $('.hamburger-menu').on('click', function() {
            $('#hamburger-dropdown').toggleClass('active');
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.hamburger-menu').length) {
                $('#hamburger-dropdown').removeClass('active');
            }
        });
    });
</script>

<?php
} // End of $is_member check
?>

</body>
</html>

<?php echo html_end(); // HTML 마지막 처리 함수 : 반드시 넣어주시기 바랍니다.