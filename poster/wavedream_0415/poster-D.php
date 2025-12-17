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

function getDefaultTextColor(design) {
    switch(design) {
        case 'design1': return '#2858d6';
        case 'design2': return '#fe49f5';
        case 'design3': return '#5adcfe';
        case 'design4': return '#a733e5';
        case 'design5': return '#f85c66';
        default: return '#2858d6';
    }
}

function selectDesign(design) {
    selectedDesign = design;
    document.querySelectorAll('.designs img').forEach(img => img.classList.remove('selected'));
    document.querySelector(`[data-design="${design}"]`).classList.add('selected');
    document.getElementById('nameColor').value = getDefaultTextColor(design);
    document.getElementById('hoursColor').value = '#000000';
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
    const nameColor = document.getElementById('nameColor').value || getDefaultTextColor(selectedDesign);
    const hoursColor = document.getElementById('hoursColor').value || '#000000';

    document.getElementById('posterName').textContent = name;
    document.getElementById('posterHours').textContent = hours;
    document.getElementById('posterContact').textContent = contact;

    document.getElementById('posterName').style.color = nameColor;
    document.getElementById('posterHours').style.color = hoursColor;
    document.getElementById('posterContact').style.color = nameColor;

    let hoursFontSize = 28, contactFontSize = 32;

    document.getElementById('posterHours').style.fontSize = `${hoursFontSize}px`;
    document.getElementById('posterContact').style.fontSize = `${contactFontSize}px`;

    let nameFontSize = 40;
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
        nameInput.value = '약사추천\n피로회복제 세트';
    }
    if (hoursInput.value.trim() !== '') {
        hoursInput.value = '아르기닌\n+\n시트룰린\n+\n종합비타민';
    }
    if (contactInput.value.trim() !== '') {
        contactInput.value = '약국 상호';
    }
    if (nameColorInput.value !== '#2858d6') {
        nameColorInput.value = '#2858d6';
    }
    if (hoursColorInput.value !== '#000000') {
        hoursColorInput.value = '#000000';
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
    document.getElementById('nameColor').value = '#2858d6';
    document.getElementById('hoursColor').value = '#000000';
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
                        <td><textarea id="name" name="name" oninput="updatePoster(); updateHiddenDesign();" required style="white-space: normal;">약사추천
피로회복제 세트
</textarea></td>
                    </tr>
                    <tr>
                        <td><label></label></td>
                        <td class="name-style-group">
                            <label>(A)(B)컬러</label>
                            <input type="color" id="nameColor" name="nameColor" value="#2858d6" oninput="updatePoster()">
                            <label for="nameSize" style="display: none;"></label>
                            <select id="nameSize" name="nameSize" onchange="updatePoster()" style="display: none;" disabled title="사용할 수 없습니다;">
                                <option value="48">48px</option>
                                <option value="52">52px</option>
                                <option value="56">56px</option>
                                <option value="60" selected>60px</option>
                            </select>
                            <label>(C)컬러</label>
                            <input type="color" id="hoursColor" name="hoursColor" value="#000000" oninput="updatePoster()">
                        </td>
                    </tr>
                    <tr>
                        <td><label for="contact">안내문구(B):</label></td>
                        <td><input type="text" id="contact" name="contact" oninput="updatePoster(); updateHiddenDesign();" required placeholder="약국 상호" maxlength="8" value="약국 상호"></td>
                    </tr>
                    <tr>
                        <td><label for="hours">안내문구(C):</label></td>
                        <td><textarea id="hours" name="hours" oninput="updatePoster(); updateHiddenDesign();" required style="white-space: normal;">아르기닌
+
시트룰린
+
종합비타민</textarea></td>
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
                    <h2 id="posterName">동물의약품</h2>
                    <p id="posterHours">힘찬 활력의 에너지로 피로를 풀어보세요。</p>
                    <p id="posterContact">약국 상호</p>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
include_once("tail.php");
?>