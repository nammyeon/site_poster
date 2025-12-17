<?php
include_once("head.php");
if (!$is_member || !isset($member['mb_id'])) {
    alert("회원전용입니다.", "./index.php");
    exit;
}
$current_file = basename($_SERVER['PHP_SELF'], '.php');
$current_type = str_replace('poster-', '', $current_file);
$background_color = array_fill_keys(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'], '#0057ff');
?>

<link rel="stylesheet" href="<?php echo G5_URL ?>/poster/poster.css">

<script>
const designUrls = Object.fromEntries(
    Array.from({length: 6}, (_, i) => [`design${i+1}`, `images/pharm_post_${'<?php echo $current_type; ?>'}0${i+1}.jpg`])
);

let selectedDesign = 'design1';

function selectDesign(design) {
    selectedDesign = design;
    document.querySelectorAll('.designs img').forEach(img => img.classList.remove('selected'));
    document.querySelector(`[data-design="${design}"]`).classList.add('selected');
    updatePoster();
}

function updateDesignType(design) {
    window.location.href = `poster-${design}.php`;
}

function updatePoster() {
    const topTitle = document.getElementById('topTitle').value || '';
    const mainTitle = document.getElementById('mainTitle').value || '';
    const price = document.getElementById('price').value || '';
    const priceDetail = document.getElementById('priceDetail').value || '';
    const recommendText = document.getElementById('recommendText').value || '';
    const benefit1 = document.getElementById('benefit1').value || '';
    const benefit2 = document.getElementById('benefit2').value || '';
    const benefit3 = document.getElementById('benefit3').value || '';
    const benefit4 = document.getElementById('benefit4').value || '';
    const pharmacy = document.getElementById('pharmacy').value || '';

    const poster = {
        topTitle: document.getElementById('posterTopTitle'),
        mainTitle: document.getElementById('posterMainTitle'),
        price: document.getElementById('posterPrice'),
        priceDetail: document.getElementById('posterPriceDetail'),
        recommendText: document.getElementById('posterRecommendText'),
        benefit1: document.getElementById('posterBenefit1'),
        benefit2: document.getElementById('posterBenefit2'),
        benefit3: document.getElementById('posterBenefit3'),
        benefit4: document.getElementById('posterBenefit4'),
        pharmacy: document.getElementById('posterPharmacy'),
        container: document.getElementById('posterContainer')
    };

    // 텍스트 업데이트
    poster.topTitle.textContent = topTitle;
    poster.mainTitle.textContent = mainTitle;
    poster.price.textContent = price;
    poster.priceDetail.textContent = priceDetail;
    poster.recommendText.textContent = recommendText;
    poster.benefit1.textContent = benefit1;
    poster.benefit2.textContent = benefit2;
    poster.benefit3.textContent = benefit3;
    poster.benefit4.textContent = benefit4;
    poster.pharmacy.textContent = pharmacy;
    
    // 메인 타이틀 크기 조정 (글자수에 따라)
    let mainTitleSize = 58;
    let topPosition = 12; // 기본 위치
    if (mainTitle.length > 6) {
        mainTitleSize = Math.max(30, mainTitleSize / (mainTitle.length / 6));
        // 폰트가 작아질수록 아래로 이동
        topPosition = 12 + (58 - mainTitleSize) * 0.1; // 0.1은 조정 비율
    }
    poster.mainTitle.style.fontSize = `${mainTitleSize}px`;
    poster.mainTitle.style.top = `${topPosition}%`;

    // 다른 요소들 폰트 크기
    poster.topTitle.style.fontSize = '18px';
    poster.price.style.fontSize = '32px';
    poster.priceDetail.style.fontSize = '12px';
    poster.recommendText.style.fontSize = '20px';
    poster.benefit1.style.fontSize = '24px';
    poster.benefit2.style.fontSize = '24px';
    poster.benefit3.style.fontSize = '24px';
    poster.benefit4.style.fontSize = '24px';
    poster.pharmacy.style.fontSize = '24px';

    poster.container.style.backgroundImage = `url('${designUrls[selectedDesign]}')`;
    document.body.className = `design-type-<?php echo $current_type; ?>`;

    document.getElementById('hiddenDesign').value = selectedDesign;
}

window.onload = () => {
    document.querySelector('[data-design="design1"]').classList.add('selected');
    document.getElementById('hiddenDesignType').value = '<?php echo $current_type; ?>';
    updatePoster();

    document.querySelectorAll('input[name="design_type"]').forEach(radio =>
        radio.addEventListener('change', () => updateDesignType(radio.value))
    );
    window.addEventListener('resize', updatePoster);
};
</script>

<?php include_once("poster-A_popup.php"); ?>

<div class="design-selector-wrapper">
    <span style="font-size:12px">디자인 선택</span><br>

    <div class="design-selector-group">
        <form method="POST" id="designForm">
            <?php foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'] as $design): ?>
                <div style="position: relative; display: inline-block;">
                    <input type="radio" name="design_type" id="design_<?php echo strtolower($design); ?>" value="<?php echo $design; ?>" onchange="updateDesignType('<?php echo $design; ?>')" class="design-radio" <?php echo $current_type === $design ? 'checked' : ''; ?>>
                    <label for="design_<?php echo strtolower($design); ?>" class="design-label" style="background: <?php echo $current_type === $design ? $background_color[$design] : '#999'; ?>;">
                        <strong id="design_select"><?php echo $design; ?></strong>
                        <?php if ($design === 'I'): ?>
                            <span class="new-badge" style="position: absolute; top: -3px; left: 0px; background: #ff4444; color: white; font-size: 10px; padding: 2px; line-height: 1;">N</span>
                        <?php endif; ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </form>
    </div>
</div>

<div class="wrapper">
    <div class="container">
        <section class="design-selector">
            <h2>디자인 <span style="color:#0057ff;font-size:28px;"><?php echo $current_type; ?></span></h2>
            <div class="designs">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <img src="images/pharm_post_<?php echo $current_type; ?>0<?php echo $i; ?>.jpg" alt="디자인 <?php echo $i; ?>" data-design="design<?php echo $i; ?>" onclick="selectDesign('design<?php echo $i; ?>')" <?php echo $i === 1 ? 'class="selected"' : ''; ?>>
                <?php endfor; ?>
            </div>
        </section>

        <section class="info-input">
            <!-- <h2>정보 입력</h2> -->
            <form id="pharmacyForm" method="post" action="poster_save_i.php">
                <input type="hidden" name="design" id="hiddenDesign" value="design1">
                <input type="hidden" name="design_type" id="hiddenDesignType" value="<?php echo htmlspecialchars($current_type); ?>">
                <table>
                    <tr>
                        <td><label for="topTitle">상단 제목:<span style="color:red">*</span></label></td>
                        <td><input type="text" id="topTitle" name="topTitle" oninput="updatePoster()" required value="정상적인 면역기능·성인건강을 위한" placeholder="상단 제목 입력"></td>
                    </tr>
                    <tr>
                        <td><label for="mainTitle">메인 제목:<span style="color:red">*</span></label></td>
                        <td><input type="text" id="mainTitle" name="mainTitle" oninput="updatePoster()" required maxlength="10" value="글루콘산 아연" placeholder="메인 제목 입력 (최대 10글자)"></td>
                    </tr>
                    <tr>
                        <td><label for="price">가격:</label></td>
                        <td><input type="text" id="price" name="price" oninput="updatePoster()" value="20,000원" placeholder="가격 입력"></td>
                    </tr>
                    <tr>
                        <td><label for="priceDetail">가격 상세:</label></td>
                        <td><input type="text" id="priceDetail" name="priceDetail" oninput="updatePoster()"  value="(90일분 / 1일 1회 1정)" placeholder="가격 상세 정보"></td>
                    </tr>
                    <tr>
                        <td><label for="recommendText">추천 문구:</label></td>
                        <td><input type="text" id="recommendText" name="recommendText" oninput="updatePoster()" value="이런 분들께 추천합니다!" placeholder="추천 문구 입력"></td>
                    </tr>
                    <tr>
                        <td><label for="benefit1">효능 1:</label></td>
                        <td><input type="text" id="benefit1" name="benefit1" oninput="updatePoster()" value="✔ 고함량 아연 원하는 분" placeholder="첫 번째 효능"></td>
                    </tr>
                    <tr>
                        <td><label for="benefit2">효능 2:</label></td>
                        <td><input type="text" id="benefit2" name="benefit2" oninput="updatePoster()" value="✔ 정상적인 면역기능" placeholder="두 번째 효능"></td>
                    </tr>
                    <tr>
                        <td><label for="benefit3">효능 3:</label></td>
                        <td><input type="text" id="benefit3" name="benefit3" oninput="updatePoster()" value="✔ 정상적인 세포분열" placeholder="세 번째 효능"></td>
                    </tr>
                    <tr>
                        <td><label for="benefit4">효능 4:</label></td>
                        <td><input type="text" id="benefit4" name="benefit4" oninput="updatePoster()" value="✔ 활기찬 생활 원하는 분" placeholder="네 번째 효능"></td>
                    </tr>
                    <tr>
                        <td><label for="pharmacy">약국명:<span style="color:red">*</span></label></td>
                        <td><input type="text" id="pharmacy" name="pharmacy" oninput="updatePoster()" required value="행복약국" placeholder="약국명 입력"></td>
                    </tr>
                </table>
                <div class="button-group">
                    <button type="submit" class="save">저장하기</button>
                </div>
            </form>
        </section>
    </div>

    <section class="poster-preview">
        <p class="preview-notice">*미리보기는 참조용이며 실제와 동일하지 않습니다。</p>
        <div class="monitor-frame">
            <div id="posterContainer" class="poster">
                <div id="posterText">
                    <p id="posterTopTitle">정상적인 면역기능·성인건강을 위한</p>
                    <h1 id="posterMainTitle">글루콘산 아연</h1>
                    <div id="posterPriceArea">
                        <p id="posterPrice">20,000원</p>
                        <p id="posterPriceDetail">(90일분 / 1일 1회 1정)</p>
                    </div>
                    <div id="posterRecommendArea">
                        <p id="posterRecommendText">이런 분들께 추천합니다!</p>
                        <div id="posterBenefits">
                            <p id="posterBenefit1">✓ 고함량 아연 원하는 분</p>
                            <p id="posterBenefit2">✓ 정상적인 면역기능</p>
                            <p id="posterBenefit3">✓ 정상적인 세포분열</p>
                            <p id="posterBenefit4">✓ 활기찬 생활 원하는 분</p>
                        </div>
                    </div>
                    <p id="posterPharmacy">행복약국</p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
#posterContainer {
    position: relative;
    width: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

#posterText {
    position: relative;
    width: 100%;
    height: 100%;
}

/* 텍스트 위치 조정 영역 */
#posterTopTitle { 
    position: absolute;
    top: 9%;          /* 상하 위치 조정 */
    left: 50%;         /* 좌우 위치 조정 */
    transform: translateX(-50%); 
    color: white; 
    font-weight: 500;
    text-align: center;
    width: 90%;
}

#posterMainTitle { 
    position: absolute;
    top: 13%;          /* 상하 위치 조정 */
    left: 50%;         /* 좌우 위치 조정 */
    transform: translateX(-50%); 
    color: #FFD700; 
    font-weight: 900;
    text-align: center;
    width: 90%;
    margin: 0;
}

#posterPriceArea {
    position: absolute;
    top: 26%;          /* 상하 위치 조정 */
    left: 50%;         /* 좌우 위치 조정 */
    transform: translateX(-50%);
    text-align: center;
    width: 90%;
}

#posterRecommendArea {
    position: absolute;
    top: 40%;          /* 상하 위치 조정 */
    left: 50%;         /* 좌우 위치 조정 */
    transform: translateX(-50%);
    width: 85%;
    border-radius: 20px;
    padding: 20px;
    box-sizing: border-box;
}

#posterPharmacy { 
    position: absolute;
    bottom: 4%;        /* 하단에서부터 위치 조정 */
    left: 50%;         /* 좌우 위치 조정 */
    transform: translateX(-50%); 
    color: white; 
    font-weight: 700;
    text-align: center;
    width: 90%;
    margin: 0;
}

#posterPrice { 
    color: white; 
    font-weight: 900;
    display: inline-block;
    margin: 0;
}

#posterPriceDetail { 
    color: white; 
    font-weight: 400;
    margin-top: -6px
}

#posterRecommendText { 
    color: black; 
    font-weight: 700;
    text-align: center;
    margin: 0 0 15px 0;
    background: #FFD700;
    padding: 8px;
    border-radius: 10px;
}

#posterBenefits {
    text-align: left;
}

#posterBenefit1, #posterBenefit2, #posterBenefit3, #posterBenefit4 { 
    color: #E91E63; 
    font-weight: 600;
    margin: 8px 0;
    line-height: 1.3;
}

.info-input td {padding:0px}
.design-label {padding:0px 10px;}
.info-input {padding:0px 20px 5px;}
@media (max-width: 500px) {
    .design-label {padding:0px;}
    .wrapper {
        display: flex;
        flex-direction: column;
    }
	.poster-preview {
		padding-top:30px;
	}
	/*
    .poster-preview {
        order: -1;
    }*/
    .container {
        order: 0;
    }
}
</style>

<?php include_once("tail.php"); ?>