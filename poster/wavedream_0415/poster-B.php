<?php
include_once("head.php");
if (!isset($member['mb_id']) || !$is_member) {
    alert("회원전용입니다.","./index.php");
    exit;
}
?>

<link rel="stylesheet" href="<?php echo G5_URL ?>/poster/poster.css">

<script>
let selectedDesign = 'design1';
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
    return '#000000';
}

function getDefaultOtherColor(design) {
    switch(design) {
        case 'design1': return '#00BA88';
        case 'design2': return '#3068d0';
        case 'design3': return '#ffc132';
        case 'design4': return '#d633fe';
        case 'design5': return '#f33e31';
        default: return '#00BA88';
    }
}

function selectDesign(design) {
    selectedDesign = design;
    document.querySelectorAll('.designs img').forEach(img => img.classList.remove('selected'));
    document.querySelector(`[data-design="${design}"]`).classList.add('selected');
    document.getElementById('nameColor').value = getDefaultGuidanceColor();
    document.getElementById('hoursColor').value = getDefaultOtherColor(design);
    updateHiddenDesign(); // Update hidden input
    updatePoster();
}

function updateHiddenDesign() {
    document.getElementById('hiddenDesign').value = selectedDesign;
}

function updateDesignType(design) {
    window.location.href = `poster-${design}.php`;
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
    document.getElementById('posterContact').style.color = getDefaultOtherColor(selectedDesign);

    let hoursFontSize = 16, contactFontSize = 40;
    if (window.innerWidth <= 500) {
        hoursFontSize *= 0.8;
        contactFontSize *= 0.8;
    }

    document.getElementById('posterHours').style.fontSize = `${hoursFontSize}px`;
    document.getElementById('posterContact').style.fontSize = `${contactFontSize}px`;

    let nameFontSize = Math.max(20, Math.min(60, 30));
    if (window.innerWidth <= 500) nameFontSize *= 1;

    document.getElementById('posterName').style.fontSize = `${nameFontSize}px`;

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
        nameInput.value = '약사추천 필수영양제\n\n1. 종합비타민\n2. 마 그 네 슘\n3. 오 메 가  3\n4. 유   산   균';
    }
    if (hoursInput.value.trim() !== '') {
        hoursInput.value = 'OPEN 08:00 ~ CLOSE 20:00\n매달 2, 4번째 일요일 정기 휴무\nTEL : 000. 1234. 5678';
    }
    if (contactInput.value.trim() !== '') {
        contactInput.value = '약국 상호';
    }
    if (nameColorInput.value !== '#000000') {
        nameColorInput.value = '#000000';
    }
    if (hoursColorInput.value !== getDefaultOtherColor('design1')) {
        hoursColorInput.value = getDefaultOtherColor('design1');
    }

    selectedDesign = 'design1';
    document.querySelectorAll('.designs img').forEach(img => img.classList.remove('selected'));
    document.querySelector(`[data-design="design1"]`).classList.add('selected');
    updateHiddenDesign(); // Update hidden input
    updatePoster();
}

window.onload = function() {
    if (!selectedDesign) {
        selectedDesign = 'design1';
        document.querySelector(`[data-design="design1"]`).classList.add('selected');
    }
    document.getElementById('nameColor').value = getDefaultGuidanceColor();
    document.getElementById('hoursColor').value = getDefaultOtherColor('design1');
    updateHiddenDesign(); // Ensure initial design is set
    updatePoster();
    window.addEventListener('resize', updatePoster);

    document.getElementById('hiddenDesignType').value = selectedDesignType;
    document.querySelectorAll('input[name="design_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateDesignType(this.value);
        });
    });

    // Ensure hidden design is updated before form submission
    document.getElementById('pharmacyForm').addEventListener('submit', function(e) {
        updateHiddenDesign(); // Update hidden input just before submission
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
                <img src="images/pharm_post_<?php echo $current_type; ?>01.jpg" alt="디자인 1" data-design="design1" onclick="selectDesign('design1')">
                <img src="images/pharm_post_<?php echo $current_type; ?>02.jpg" alt="디자인 2" data-design="design2" onclick="selectDesign('design2')">
                <img src="images/pharm_post_<?php echo $current_type; ?>03.jpg" alt="디자인 3" data-design="design3" onclick="selectDesign('design3')">
                <img src="images/pharm_post_<?php echo $current_type; ?>04.jpg" alt="디자인 4" data-design="design4" onclick="selectDesign('design4')">
                <img src="images/pharm_post_<?php echo $current_type; ?>05.jpg" alt="디자인 5" data-design="design5" onclick="selectDesign('design5')">
            </div>
        </section>

        <section class="info-input">
            <h2>정보 입력</h2>
            <form id="pharmacyForm" method="post" action="poster_save.php">
                <input type="hidden" name="design" id="hiddenDesign" value="<?php echo htmlspecialchars($selectedDesign); ?>">
                <input type="hidden" name="design_type" id="hiddenDesignType" value="<?php echo htmlspecialchars($current_type); ?>">
                <table>
                    <tr>
                        <td><label for="name">안내문구(A):</label></td>
                        <td><textarea id="name" name="name" oninput="updatePoster(); updateHiddenDesign();" required style="white-space: normal;">약사추천 필수영양제

1. 종합비타민
2. 마 그 네 슘
3. 오 메 가  3
4. 유   산   균
</textarea></td>
                    </tr>
                    <tr>
                        <td><label></label></td>
                        <td class="name-style-group">
                            <label>(A)컬러:</label>
                            <input type="color" id="nameColor" name="nameColor" value="#000000" oninput="updatePoster()">
                            <label>(B)(C)컬러:</label>
                            <input type="color" id="hoursColor" name="hoursColor" value="#000000" oninput="updatePoster()">
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
                        <td><label for="contact">약국상호(B):</label></td>
                        <td><input type="text" id="contact" name="contact" oninput="updatePoster(); updateHiddenDesign();" required placeholder="약국 상호 (최대 8글자)" maxlength="8" value="약국 상호"></td>
                    </tr>
                    <tr>
                        <td><label for="hours">영업시간(C):</label></td>
                        <td><textarea id="hours" name="hours" oninput="updatePoster(); updateHiddenDesign();" required style="white-space: normal;">OPEN 08:00 ~ CLOSE 20:00
매달 2, 4번째 일요일 정기 휴무
TEL : 000. 1234. 5678</textarea></td>
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
                    <h2 id="posterName" style="text-align:right">약사추천 필수영양제</h2>
                    <p id="posterHours">OPEN 08:00 ~ CLOSE 20:00</p>
                    <p id="posterContact" style="font-size:40px">약국 상호</p>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
include_once("tail.php");
?>