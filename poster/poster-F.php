<?php
include_once("head.php");
if (!isset($member['mb_id']) || !$is_member) {
    alert("회원전용입니다.", "./index.php");
    exit;
}
$current_file = basename($_SERVER['PHP_SELF'], '.php');
$current_type = str_replace('poster-', '', $current_file);
$background_color = array_fill_keys(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'], '#0057ff');
?>

<link rel="stylesheet" href="<?php echo G5_URL ?>/poster/poster.css">

<script>
    let selectedDesign = 'design1';
    const currentFile = '<?php echo $current_file; ?>';
    const selectedDesignType = '<?php echo $current_type; ?>';

    const designUrls = {
        'design1': `images/pharm_post_${selectedDesignType}01.jpg`,
        'design2': `images/pharm_post_${selectedDesignType}02.jpg`,
        'design3': `images/pharm_post_${selectedDesignType}03.jpg`,
        'design4': `images/pharm_post_${selectedDesignType}04.jpg`,
        'design5': `images/pharm_post_${selectedDesignType}05.jpg`
    };

    const defaultTextColors = {
        'design1': '#9a88ff',
        'design2': '#ff99b6',
        'design3': '#0eced3',
        'design4': '#ffae4a',
        'design5': '#25ccff'
    };

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
        const nameInput = document.getElementById('name');
        const hoursInput = document.getElementById('hours');
        const name = nameInput.value || '';
        const hours = hoursInput.value || '';
        const nameColor = defaultTextColors[selectedDesign] || '#9a88ff';
        const hoursColor = '#ffffff';

        const posterNameElement = document.getElementById('posterName');
        const posterHoursElement = document.getElementById('posterHours');
        const posterContainerElement = document.getElementById('posterContainer');

        posterNameElement.textContent = name;
        posterHoursElement.textContent = hours;
        posterNameElement.style.color = nameColor;
        posterHoursElement.style.color = hoursColor;
        posterNameElement.style.fontSize = '60px';
        posterHoursElement.style.fontSize = '30px';
        posterContainerElement.style.backgroundImage = `url('${designUrls[selectedDesign]}')`;
        document.body.className = `design-type-${selectedDesignType}`;

        document.getElementById('hiddenDesign').value = selectedDesign;
        document.getElementById('hiddenNameColor').value = nameColor;
        document.getElementById('hiddenHoursColor').value = hoursColor;
    }

    function resetPoster() {
        document.getElementById('name').value = '당뇨관리';
        document.getElementById('hours').value = '혈당 점검\n인슐린 주사\n약사 상담';
        selectedDesign = 'design1';
        document.querySelectorAll('.designs img').forEach(img => img.classList.remove('selected'));
        document.querySelector('[data-design="design1"]').classList.add('selected');
        updatePoster();
    }

    window.onload = function() {
        document.querySelector('[data-design="design1"]').classList.add('selected');
        updatePoster();
        window.addEventListener('resize', updatePoster);
        document.getElementById('hiddenDesignType').value = selectedDesignType;
        document.querySelectorAll('input[name="design_type"]').forEach(radio => {
            radio.addEventListener('change', () => updateDesignType(radio.value));
        });
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
                    <?php
                    $design_key = "design" . $i;
                    $image_url = "images/pharm_post_{$current_type}0{$i}.jpg";
                    $is_selected = ($i === 1) ? 'selected' : '';
                    ?>
                    <img src="<?php echo $image_url; ?>"
                         alt="디자인 <?php echo $i; ?>"
                         data-design="<?php echo $design_key; ?>"
                         onclick="selectDesign('<?php echo $design_key; ?>')"
                         class="<?php echo $is_selected; ?>">
                <?php endfor; ?>
            </div>
        </section>

        <section class="info-input">
            <h2>정보 입력</h2>
            <form id="pharmacyForm" method="post" action="poster_save.php">
                <input type="hidden" name="design" id="hiddenDesign" value="design1">
                <input type="hidden" name="design_type" id="hiddenDesignType" value="<?php echo htmlspecialchars($current_type); ?>">
                <input type="hidden" name="contact" value="-">
                <input type="hidden" name="nameColor" id="hiddenNameColor" value="#9a88ff">
                <input type="hidden" name="hoursColor" id="hiddenHoursColor" value="#ffffff">
                <table>
                    <tr>
                        <td><label for="name">샘플문구(A):</label></td>
                        <td><input type="text" id="name" name="name" oninput="updatePoster()" required maxlength="5" value="당뇨관리" placeholder="최대 5글자"></td>
                    </tr>
                    <tr>
                        <td><label for="hours">샘플문구(B):</label></td>
                        <td><textarea id="hours" name="hours" oninput="updatePoster()" required>혈당 점검
인슐린 주사
약사 상담
</textarea></td>
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
                    <h2 id="posterName">당뇨관리</h2>
                    <p id="posterHours">혈당 점검
인슐린 주사
약사 상담</p>
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