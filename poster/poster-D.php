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
    Array.from({length: 5}, (_, i) => [`design${i+1}`, `images/pharm_post_${'<?php echo $current_type; ?>'}0${i+1}.jpg`])
);

const defaultColors = {
    design1: '#2858d6', design2: '#fe49f5', design3: '#5adcfe', design4: '#a733e5', design5: '#f85c66'
};

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
    const name = document.getElementById('name').value || '';
    const hours = document.getElementById('hours').value || '';
    const contact = document.getElementById('contact').value || '';
    const nameColor = defaultColors[selectedDesign];
    const hoursColor = '#000000';

    const poster = {
        name: document.getElementById('posterName'),
        hours: document.getElementById('posterHours'),
        contact: document.getElementById('posterContact'),
        container: document.getElementById('posterContainer')
    };

    poster.name.textContent = name;
    poster.hours.textContent = hours;
    poster.contact.textContent = contact;
    poster.name.style.color = nameColor;
    poster.hours.style.color = hoursColor;
    poster.contact.style.color = nameColor;
    poster.name.style.fontSize = '40px';
    poster.hours.style.fontSize = '28px';
    poster.contact.style.fontSize = '32px';
    poster.container.style.backgroundImage = `url('${designUrls[selectedDesign]}')`;
    document.body.className = `design-type-<?php echo $current_type; ?>`;

    document.getElementById('hiddenDesign').value = selectedDesign;
    document.getElementById('hiddenNameColor').value = nameColor;
    document.getElementById('hiddenHoursColor').value = hoursColor;
}

function resetPoster() {
    const form = document.getElementById('pharmacyForm');
    form.name.value = '약사추천\n피로회복제 세트';
    form.hours.value = '아르기닌\n+\n시트룰린\n+\n종합비타민';
    form.contact.value = '약국 상호';
    selectedDesign = 'design1';
    document.querySelectorAll('.designs img').forEach(img => img.classList.remove('selected'));
    document.querySelector('[data-design="design1"]').classList.add('selected');
    updatePoster();
}

window.onload = () => {
    document.querySelector('[data-design="design1"]').classList.add('selected');
    document.getElementById('hiddenDesignType').value = '<?php echo $current_type; ?>';
    updatePoster();

    document.querySelectorAll('input[name="design_type"]').forEach(radio =>
        radio.addEventListener('change', () => updateDesignType(radio.value))
    );
    document.getElementById('pharmacyForm').addEventListener('submit', updatePoster);
    window.addEventListener('resize', updatePoster);
};
</script>

<div class="design-selector-wrapper"><span style="font-size:12px">디자인 선택</span><br>
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
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <img src="images/pharm_post_<?php echo $current_type; ?>0<?php echo $i; ?>.jpg" alt="디자인 <?php echo $i; ?>" data-design="design<?php echo $i; ?>" onclick="selectDesign('design<?php echo $i; ?>')" <?php echo $i === 1 ? 'class="selected"' : ''; ?>>
                <?php endfor; ?>
            </div>
        </section>

        <section class="info-input">
            <h2>정보 입력</h2>
            <form id="pharmacyForm" method="post" action="poster_save.php">
                <input type="hidden" name="design" id="hiddenDesign" value="design1">
                <input type="hidden" name="design_type" id="hiddenDesignType" value="<?php echo htmlspecialchars($current_type); ?>">
                <input type="hidden" name="nameColor" id="hiddenNameColor" value="#2858d6">
                <input type="hidden" name="hoursColor" id="hiddenHoursColor" value="#000000">
                <table>
                    <tr>
                        <td><label for="name">샘플문구(A):</label></td>
                        <td><textarea id="name" name="name" oninput="updatePoster()" required>약사추천
피로회복제 세트</textarea></td>
                    </tr>
                    <tr>
                        <td><label for="hours">샘플문구(B):</label></td>
                        <td><textarea id="hours" name="hours" oninput="updatePoster()" required>아르기닌
+
시트룰린
+
종합비타민</textarea></td>
                    </tr>
                    <tr>
                        <td><label for="contact">샘플문구(C):</label></td>
                        <td><input type="text" id="contact" name="contact" oninput="updatePoster()" required maxlength="8" value="약국 상호" placeholder="최대 8글자"></td>
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
                    <h2 id="posterName">약사추천
피로회복제 세트</h2>
                    <p id="posterHours">아르기닌
+
시트룰린
+
종합비타민</p>
                    <p id="posterContact">약국 상호</p>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
$user_ip = $_SERVER['REMOTE_ADDR']; // 현재 접속한 사용자의 IP 주소를 가져옵니다.

if ($user_ip == '59.22.76.67') {
    include_once("tail2.php"); // IP가 59.22.76.67일 경우 tail2.php를 포함합니다.
} else {
    include_once("tail.php"); // 그 외의 모든 IP일 경우 tail.php를 포함합니다.
}
?>