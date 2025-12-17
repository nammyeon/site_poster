<?php
include_once("head.php");
if (!isset($member['mb_id']) || !$is_member) {
    alert("회원전용입니다.","./index.php");
    exit;
}
?>

<link rel="stylesheet" href="<?php echo G5_URL ?>/poster/poster.css">

<script>
let selectedDesign = 'design1'; // 기본값 설정
let currentFile = '<?php echo basename($_SERVER['PHP_SELF'], '.php'); ?>';
let selectedDesignType = currentFile.replace('poster-', '');

const designUrls = {
    'design1': `images/pharm_post_${selectedDesignType}01.jpg`,
    'design2': `images/pharm_post_${selectedDesignType}02.jpg`,
    'design3': `images/pharm_post_${selectedDesignType}03.jpg`,
    'design4': `images/pharm_post_${selectedDesignType}04.jpg`,
    'design5': `images/pharm_post_${selectedDesignType}05.jpg`
};

function getDefaultGuidanceColor() {
    return '#00a7db';
}

function getDefaultOtherColor(design) {
    switch(design) {
        case 'design1': return '#00a7db';
        case 'design2': return '#ED008C';
        case 'design3': return '#528BFF';
        case 'design4': return '#fb4844';
        case 'design5': return '#9c57ff';
        default: return '#00a7db';
    }
}

function selectDesign(design) {
    selectedDesign = design;
    document.querySelectorAll('.designs img').forEach(img => img.classList.remove('selected'));
    document.querySelector(`[data-design="${design}"]`).classList.add('selected');
    document.getElementById('nameColor').value = getDefaultGuidanceColor();
    document.getElementById('hoursColor').value = getDefaultOtherColor(design);
    updatePoster();
    updateHiddenDesign(); // 숨겨진 입력값 업데이트
}

function updateDesignType(design) {
    window.location.href = `poster-${design}.php`;
}

function updateHiddenDesign() {
    document.getElementById('hiddenDesign').value = selectedDesign; // 숨겨진 입력값 업데이트
}

function updatePoster() {
    const name = document.getElementById('name').value || '';
    const hours = document.getElementById('hours').value || '';
    const contact = document.getElementById('contact').value || '';
    const nameColor = document.getElementById('nameColor').value || getDefaultGuidanceColor();
    const hoursColor = document.getElementById('hoursColor').value || getDefaultOtherColor(selectedDesign);

    document.getElementById('posterName').textContent = name;
    document.getElementById('posterHours').textContent = hours;
    document.getElementById('posterContact').textContent = contact;

    document.getElementById('posterName').style.color = nameColor;
    document.getElementById('posterHours').style.color = hoursColor;
    document.getElementById('posterContact').style.color = hoursColor; // B컬러가 C에도 적용

    let hoursFontSize = 16, contactFontSize = 16;
    if (window.innerWidth <= 500) {
        hoursFontSize *= 0.8;
        contactFontSize *= 0.8;
    }

    document.getElementById('posterHours').style.fontSize = `${hoursFontSize}px`;
    document.getElementById('posterContact').style.fontSize = `${contactFontSize}px`;

    let nameFontSize = Math.max(20, Math.min(parseInt(document.getElementById('nameSize').value) || 60, 60));
    const textLength = name ? name.length : 0;
    if (textLength > 5) nameFontSize = Math.max(16, nameFontSize / (textLength / 5));
    if (window.innerWidth <= 500) nameFontSize *= 1;

    document.getElementById('posterName').style.fontSize = `${nameFontSize}px`;

    let topValue = 7;
    if (textLength >= 6) topValue += (textLength - 5);
    document.getElementById('posterName').style.top = `${topValue}%`;

    document.getElementById('posterContainer').style.backgroundImage = `url('${designUrls[selectedDesign]}')`;
    document.body.className = `design-type-${selectedDesignType}`;
}

function resetPoster() {
    const nameInput = document.getElementById('name');
    const hoursInput = document.getElementById('hours');
    const contactInput = document.getElementById('contact');
    const nameColorInput = document.getElementById('nameColor');
    const hoursColorInput = document.getElementById('hoursColor');

    if (nameInput.value.trim() !== '') {
        nameInput.value = '약국 상호';
    }
    if (hoursInput.value.trim() !== '') {
        hoursInput.value = '월~금 : 오전 9:00 ~ 오후 7:00\n토요일 : 오전 9:00 ~ 오후 1:00\n일요일,공휴일 : 휴무';
    }
    if (contactInput.value.trim() !== '') {
        contactInput.value = '02-1234-1234';
    }
    if (nameColorInput.value !== getDefaultGuidanceColor()) {
        nameColorInput.value = getDefaultGuidanceColor();
    }
    if (hoursColorInput.value !== getDefaultOtherColor('design1')) {
        hoursColorInput.value = getDefaultOtherColor('design1');
    }

    selectedDesign = 'design1';
    document.querySelectorAll('.designs img').forEach(img => img.classList.remove('selected'));
    document.querySelector(`[data-design="design1"]`).classList.add('selected');
    updatePoster();
    updateHiddenDesign(); // 초기화 후 숨겨진 입력값 업데이트
}

window.onload = function() {
    // 항상 'design1'로 초기화
    if (!selectedDesign) {
        selectedDesign = 'design1';
    }
    document.querySelector(`[data-design="${selectedDesign}"]`).classList.add('selected');
    document.getElementById('nameColor').value = getDefaultGuidanceColor();
    document.getElementById('hoursColor').value = getDefaultOtherColor(selectedDesign);
    updateHiddenDesign(); // 초기 숨겨진 입력값 설정
    updatePoster();
    window.addEventListener('resize', updatePoster);

    document.getElementById('hiddenDesignType').value = selectedDesignType;
    document.querySelectorAll('input[name="design_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateDesignType(this.value);
        });
    });

    // 폼 제출 시 디자인 업데이트
    document.getElementById('pharmacyForm').addEventListener('submit', function(e) {
        updateHiddenDesign(); // 제출 전 최신 디자인 저장
    });
};
</script>

<?php
$current_file = basename($_SERVER['PHP_SELF'], '.php');
$current_type = str_replace('poster-', '', $current_file);

$background_color = ["A" => "#0057ff", "B" => "#0057ff", "C" => "#0057ff", "D" => "#0057ff", "E" => "#0057ff"];
$designs = ['A', 'B', 'C', 'D', 'E'];
?>

<div class="design-selector-wrapper">
    <div class="design-selector-group">
        <form method="POST" action="" id="designForm" style="margin: 0;">
            <?php foreach ($designs as $design): ?>
                <?php $id = 'design_' . strtolower($design); ?>
                <input type="radio" name="design_type" id="<?php echo $id; ?>" value="<?php echo $design; ?>" onchange="updateDesignType('<?php echo $design; ?>')" class="design-radio" <?php echo ($current_type === $design) ? 'checked' : ''; ?>>
                <label for="<?php echo $id; ?>" class="design-label" style="background: <?php echo ($current_type === $design) ? $background_color[$design] : '#999'; ?>;">디자인 <?php echo $design; ?></label>
            <?php endforeach; ?>
        </form>
    </div>
</div>

<div class="wrapper">
    <div class="container">
        <section class="design-selector">
            <div style="display:flex;align-items: baseline;">
                <h2>디자인 <span style="color:#0057ff;font-size:28px;"><?php echo $current_type; ?></span></h2>
            </div>
            <div class="designs">
                <img src="images/pharm_post_<?php echo $current_type; ?>01.jpg" alt="디자인 1" data-design="design1" onclick="selectDesign('design1')" class="selected">
                <img src="images/pharm_post_<?php echo $current_type; ?>02.jpg" alt="디자인 2" data-design="design2" onclick="selectDesign('design2')">
                <img src="images/pharm_post_<?php echo $current_type; ?>03.jpg" alt="디자인 3" data-design="design3" onclick="selectDesign('design3')">
                <img src="images/pharm_post_<?php echo $current_type; ?>04.jpg" alt="디자인 4" data-design="design4" onclick="selectDesign('design4')">
                <img src="images/pharm_post_<?php echo $current_type; ?>05.jpg" alt="디자인 5" data-design="design5" onclick="selectDesign('design5')">
            </div>
        </section>

        <section class="info-input">
            <h2>정보 입력</h2>
            <form id="pharmacyForm" method="post" action="poster_save.php">
                <input type="hidden" name="design" id="hiddenDesign" value="design1">
                <input type="hidden" name="design_type" id="hiddenDesignType" value="<?php echo htmlspecialchars($current_type); ?>">
                <table>
                    <tr>
                        <td><label for="name">안내문구(A):</label></td>
                        <td><input type="text" id="name" name="name" oninput="updatePoster(); updateHiddenDesign();" required placeholder="약국 상호 (최대 8글자)" maxlength="8" value="약국 상호"></td>
                    </tr>
                    <tr>
                        <td><label></label></td>
                        <td class="name-style-group">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <label>(A)컬러:</label>
                                <input type="color" id="nameColor" name="nameColor" value="#00a7db" oninput="updatePoster()">
                                <label>(B)(C)컬러:</label>
                                <input type="color" id="hoursColor" name="hoursColor" value="#00a7db" oninput="updatePoster()">
                            </div>
                            <label for="nameSize"></label>
                            <select id="nameSize" name="nameSize" onchange="updatePoster()" style="display: none;" disabled title="사용할 수 없습니다;">
                                <option value="48">48px</option>
                                <option value="52">52px</option>
                                <option value="56">56px</option>
                                <option value="60" selected>60px</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="hours">안내문구(B):</label></td>
                        <td><textarea id="hours" name="hours" oninput="updatePoster(); updateHiddenDesign();" required style="white-space: normal;">월~금 : 오전 9:00 ~ 오후 7:00
토요일 : 오전 9:00 ~ 오후 1:00
일요일,공휴일 : 휴무</textarea></td>
                    </tr>
                    <tr>
                        <td><label for="contact">안내문구(C):</label></td>
                        <td><input type="text" id="contact" name="contact" oninput="updatePoster(); updateHiddenDesign();" required placeholder="02-1234-1234" value="02-1234-1234"></td>
                    </tr>
                </table>
                <div class="button-group">
                    <button type="button" class="reset" onclick="resetPoster()">초기화</button>
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
                    <h2 id="posterName">약국 이름</h2>
                    <p id="posterHours">영업 시간</p>
                    <p id="posterContact">연락처</p>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
include_once("tail.php");
?>