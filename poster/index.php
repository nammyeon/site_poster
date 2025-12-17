<?php
include_once("head.php");
?>

<!-- Add CSS link in the same directory -->
<link rel="stylesheet" href="<?php echo G5_URL ?>/poster/index.css">

<?php
//  'notice'에서 최신 5개 글 가져오기 (wr_content도 포함)
$sql = "SELECT wr_id, wr_subject, wr_content, wr_datetime FROM {$g5['write_prefix']}notice WHERE wr_is_comment = 0 ORDER BY wr_datetime DESC LIMIT 5";
$result = sql_query($sql);
?>
<?php
if ($_SERVER['REMOTE_ADDR'] === '59.22.76.67') {
   echo "<span style='position:fixed; top:20px; left:20px; color:red; background:white; padding:5px 15px; border:1px solid red; z-index:9999;'><a href='poster_test.php' style='text-decoration:none'>테스트(X)</a></span>";
   echo "<div class='new_post' style='position:fixed; top:100px; left:20px; color:red; background:white; padding:5px 15px; border:1px solid red; z-index:9999;'><a href='/poster/poster_index.php' style='text-decoration:none'>POSTER LIST</a></div>";	
   //echo "<span style='position:fixed; top:70px; left:20px; color:red; background:white; padding:5px 15px; border:1px solid red; z-index:9999;'><a href='poster_index.php' style='text-decoration:none'>NEW 테스트</a></span>";
}
?>
<main>
<section class="hero">
    <div class="hero-inner">
        <picture>
            <source media="(max-width: 768px)" srcset="images/wavedream_top_image_mo.jpg">
            <img src="images/wavedream_top_image_500.jpg" alt="WaveDream Top Image">
        </picture>
    </div>
</section>

    <section class="notices">
        <div class="inner-container">
            <h2>공지사항</h2>
            <ul class="notice-list">
                <?php while ($row = sql_fetch_array($result)) { ?>
                    <li>
                        <a href="javascript:void(0)" class="notice-link" 
                           data-id="<?php echo $row['wr_id']; ?>" 
                           data-subject="<?php echo htmlspecialchars($row['wr_subject']); ?>" 
                           data-content="<?php echo htmlspecialchars($row['wr_content']); ?>" 
                           data-datetime="<?php echo $row['wr_datetime']; ?>">
                            <!-- [<?php echo date('m.d', strtotime($row['wr_datetime'])); ?>]  --><strong>·</strong> <?php echo htmlspecialchars($row['wr_subject']); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </section>

    <section id="features" class="features">
        <div class="inner-container">
            <h2>WaveDream이 약국에 제공하는 특별한 서비스</h2>
            <div class="feature-grid">
                <div class="feature">
                    <img src="images/main_icon_01.png" alt="Icon 1" style="width: 64px; height: 64px;">
                    <h3>업종별</h3>
                    <p>다양한 맞춤 컨설팅</p>
                </div>
                <div class="feature">
                    <img src="images/main_icon_02.png" alt="Icon 2" style="width: 64px; height: 64px;">
                    <h3>인증된</h3>
                    <p>우수 하드웨어 사용<br>안정된 미디어 플랫폼</p>
                </div>
                <div class="feature">
                    <img src="images/main_icon_03.png" alt="Icon 3" style="width: 64px; height: 64px;">
                    <h3>광고 컨텐츠</h3>
                    <p>관리 ~ 송출까지 <br>한번에 해결</p>
                </div>
                <div class="feature">
                    <img src="images/main_icon_04.png" alt="Icon 4" style="width: 64px; height: 64px;">
                    <h3>합리적 비용의</h3>
                    <p>광고 송출</p>
                </div>
                <div class="feature">
                    <img src="images/main_icon_05.png" alt="Icon 5" style="width: 64px; height: 64px;">
                    <h3>공간의 가치를 높이는</h3>
                    <p>디스플레이 및<br> 높은 광고 도달률</p>
                </div>
                <div class="feature">
                    <img src="images/main_icon_06.png" alt="Icon 6" style="width: 64px; height: 64px;">
                    <h3>다양한</h3>
                    <p>콘텐츠 형식 지원</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta<?php echo $is_member ? ' logged-in' : ''; ?>">
        <div class="inner-container">
            <h2>약국의 가치를 디자인으로 표현하세요!</h2>
            <p>WaveDream과 함께라면 약국을 방문하는 모든 고객에게 깊은 인상을 남길 수 있습니다。 지금 전문가의 손길을 경험해보세요。</p>
            <?php if($is_member) { ?>
                <a href="poster-A.php" class="btn">지금 디자인 의뢰하기</a>
            <?php } else { ?>
                <a href="javascript:void(0)" class="open-register btn">지금 디자인 의뢰하기</a>
            <?php } ?>
        </div>
    </section>
</main>

<!-- 로그인 팝업 -->
<div class="popup-overlay" id="login-overlay"></div>
<div class="login_form" id="login-popup">
    <div class="login-header">
        <h2>로그인</h2>
        <span class="close-btn" onclick="hideLoginPopup()">×</span>
    </div>
    <form name="foutlogin" id="registFrm" action="<?php echo G5_BBS_URL ?>/login_check.php?device=mobile" method="post">
        <input type="hidden" name="url" value="<?php echo G5_URL ?>/poster/index.php">
        <div class="login-content">
            <div class="input-group">
                <input type="text" name="mb_id" required placeholder="아이디" autocomplete="off">
            </div>
            <div class="input-group">
                <input type="password" name="mb_password" required placeholder="비밀번호" autocomplete="off">
            </div>
            <button type="button" class="login-btn" onclick="event_form()">로그인</button>
        </div>
    </form>
</div>

<!-- 회원가입 팝업 -->
<div class="popup-overlay" id="register-overlay"></div>
<div class="register_form" id="register-popup">
    <div class="register-header">
        <h2>회원가입</h2>
        <span class="close-btn" onclick="closeRegisterPopup()">×</span>
    </div>
    <div class="register-content">
        <iframe id="register-iframe" src="" frameborder="0"></iframe>
    </div>
</div>

<!-- 공지사항 내용 팝업 -->
<div class="popup-overlay" id="notice-overlay"></div>
<div class="notice-popup" id="notice-popup" style="display:none">
    <div class="notice-header">
        <h3 id="notice-title"></h3>
        <span class="close-btn" onclick="hideNoticePopup()">×</span>
    </div>
    <div class="notice-content" id="notice-content">
        <!-- 공지사항 내용이 여기에 동적으로 삽입됩니다 -->
    </div>
</div>

<!-- 팝업 스크립트 -->
<script>
$(document).ready(function() {
    // 로그인 팝업 열기
    $('.open-login').on('click', function(e) {
        e.preventDefault();
        $('#login-overlay').fadeIn();
        $('#login-popup').slideDown(200);
    });

    // 회원가입 팝업 열기
    $('.open-register').on('click', function(e) {
        e.preventDefault();
        $('#register-iframe').attr('src', '<?php echo G5_BBS_URL ?>/register.php?device=mobile');
        $('#register-overlay').fadeIn();
        $('#register-popup').slideDown(200);
    });

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

    // 팝업 닫기 (로그인)
    window.hideLoginPopup = function() {
        $('#login-popup').slideUp(200, function() {
            $('#login-overlay').fadeOut();
        });
    };

    // 팝업 닫기 (회원가입)
    window.closeRegisterPopup = function() {
        $('#register-popup').slideUp(200, function() {
            $('#register-overlay').fadeOut();
            $('#register-iframe').attr('src', '');
            window.location.reload(); // 팝업 닫힌 후 페이지 새로고침
        });
    };

    // 팝업 닫기 (공지사항)
    window.hideNoticePopup = function() {
        $('#notice-popup').slideUp(200, function() {
            $('#notice-overlay').fadeOut();
            $('#notice-content').empty(); // 내용 비우기
        });
    };

    // 오버레이 클릭으로 닫기
    $('.popup-overlay').on('click', function() {
        if (this.id === 'login-overlay') hideLoginPopup();
        if (this.id === 'register-overlay') closeRegisterPopup();
        if (this.id === 'notice-overlay') hideNoticePopup();
    });

    // 팝업 내부 클릭 시 닫히지 않도록
    $('.login_form, .register_form, .notice-popup').on('click', function(e) {
        e.stopPropagation();
    });

    // 로그인 폼 제출
    window.event_form = function() {
        var name = $("input[name='mb_id']").val();
        var phoneNum = $("input[name='mb_password']").val();

        if (!name) {
            alert("아이디를 입력하세요.");
            $("input[name='mb_id']").focus();
            return;
        }
        if (!phoneNum) {
            alert("비밀번호를 입력하세요.");
            $("input[name='mb_password']").focus();
            return;
        }

        var form = document.getElementById('registFrm');
        form.submit();
    };

    // iframe에서 메시지 수신 (회원가입 결과 페이지에서 보낸 메시지 처리)
    window.addEventListener('message', function(event) {
        if (event.data === 'closeRegisterPopup') {
            closeRegisterPopup(); // 회원가입 팝업 닫기 및 새로고침
        }
    });
});
</script>

<?php
$user_ip = $_SERVER['REMOTE_ADDR']; // 현재 접속한 사용자의 IP 주소를 가져옵니다.

if ($user_ip == '59.22.76.67') {
    include_once("tail2.php"); // IP가 59.22.76.67일 경우 tail2.php를 포함합니다.
} else {
    include_once("tail.php"); // 그 외의 모든 IP일 경우 tail.php를 포함합니다.
}
?>