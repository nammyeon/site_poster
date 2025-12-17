<?php
include_once("head.php");
if (!$is_member || !isset($member['mb_id'])) {
    alert("회원전용입니다.", "./index.php");
    exit;
}

$current_file_base_name = pathinfo(__FILE__, PATHINFO_FILENAME);

if ($_SERVER["REMOTE_ADDR"] == "59.22.76.67") {
    if (strpos($current_file_base_name, '_test') !== false) {
        //현재 테스트 파일에 접속 중입니다.
        //echo "<span style='position:fixed; top:10px; left:10px; color:red; background:white; padding:5px; border:1px solid red; z-index:9999;'>테스트 파일</span>";
    } else {
        // 현재 실적용 파일에 접속 중입니다.
        //echo "<span style='position:fixed; top:10px; left:10px; color:red; background:white; padding:5px; border:1px solid red; z-index:9999;'>운영중 파일</span>";
    }
}

// 통합 디자인 설정 - 새로운 디자인을 추가할 때 여기만 수정하면 됩니다.
  $design_master_config = [
      'valid_types' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'],
      'default_background_color' => '#0057ff', // 모든 디자인의 기본 배경색
      'new_badges' => ['I', 'J', 'K', 'L', 'M', 'N'], // NEW 뱃지를 표시할 디자인들
      'layout_rows' => [
          'row1' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'],
          'row2' => ['I', 'J', 'K', 'L', 'M', 'N']
      ]
  ];

  // 기존 변수들을 새로운 설정에서 가져오기
  $valid_types = $design_master_config['valid_types'];
  $background_color = $design_master_config['default_background_color'];


// 배열의 마지막 항목을 동적으로 가져옵니다.
$last_item = end($valid_types);

// URL 파라미터가 없거나 유효하지 않으면 마지막 항목을 기본값으로 설정합니다.
$current_type = isset($_GET['type']) ? trim($_GET['type']) : $last_item;

// URL 파라미터 값이 유효성 목록에 없는 경우, 다시 마지막 항목으로 설정합니다.
if (!in_array($current_type, $valid_types)) {
    $current_type = $last_item;
}

// design 파라미터를 읽어오고 없으면 design1을 기본값으로 설정
$selected_design = isset($_GET['design']) ? trim($_GET['design']) : 'design1';

// 포스터 타입별 설정 배열
$poster_configs = [
'A' => [
        'popup' => false,
        'designs' => 5,
        'design_colors' => ['design1' => '#00a7db', 'design2' => '#ED008C', 'design3' => '#528BFF', 'design4' => '#fb4844', 'design5' => '#9c57ff'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 8, 'default' => '약국 상호', 'required' => true],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "월~금 : 오전 9:00 ~ 오후 7:00\n토요일 : 오전 9:00 ~ 오후 1:00\n일요일,공휴일 : 휴무", 'required' => true, 'height' => 80],
            'field3' => ['label' => '샘플(C)', 'type' => 'input', 'default' => '02-1234-1234', 'required' => true]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => 'design_color', 'field3' => 'design_color'],
        'font_sizes' => ['field1' => 60, 'field2' => 16, 'field3' => 16]
    ],
    'B' => [
        'popup' => false,
        'designs' => 5,
        'design_colors' => ['design1' => '#00BA88', 'design2' => '#3068d0', 'design3' => '#ffc132', 'design4' => '#d633fe', 'design5' => '#f33e31'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'textarea', 'default' => "약사추천 필수영양제\n\n1. 종합비타민\n2. 마 그 네 슘\n3. 오 메 가  3\n4. 유   산   균", 'required' => true, 'height' => 120],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "OPEN 08:00 ~ CLOSE 20:00\n매달 2, 4번째 일요일 정기 휴무\nTEL : 000. 1234. 5678", 'required' => true, 'height' => 80],
            'field3' => ['label' => '샘플(C)', 'type' => 'input', 'maxlength' => 8, 'default' => '약국 상호', 'required' => true]
        ],
        'color_schemes' => ['field1' => '#000000', 'field2' => 'design_color', 'field3' => 'design_color'],
        'font_sizes' => ['field1' => 30, 'field2' => 16, 'field3' => 40]
    ],
    'C' => [
        'popup' => false,
        'designs' => 5,
        'design_colors' => ['design1' => '#a499ff', 'design2' => '#ff99b6', 'design3' => '#008cbd', 'design4' => '#ff7062', 'design5' => '#4cbf64'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 5, 'default' => '동물의약품', 'required' => true],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "심장사상충약, 종합구충제, 동물의약품\n약국에서 상담받고 구매하세요.", 'required' => true, 'height' => 80],
            'field3' => ['label' => '샘플(C)', 'type' => 'input', 'maxlength' => 12, 'default' => '취급,판매허가약국', 'required' => true]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#000000', 'field3' => 'design_color'],
        'font_sizes' => ['field1' => 60, 'field2' => 16, 'field3' => 28]
    ],
    'D' => [
        'popup' => false,
        'designs' => 5,
        'design_colors' => ['design1' => '#2858d6', 'design2' => '#fe49f5', 'design3' => '#5adcfe', 'design4' => '#a733e5', 'design5' => '#f85c66'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'textarea', 'default' => "약사추천\n피로회복제 세트", 'required' => true, 'height' => 80],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "아르기닌\n+\n시트룰린\n+\n종합비타민", 'required' => true, 'height' => 100],
            'field3' => ['label' => '샘플(C)', 'type' => 'input', 'maxlength' => 8, 'default' => '약국 상호', 'required' => true]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#000000', 'field3' => 'design_color'],
        'font_sizes' => ['field1' => 40, 'field2' => 28, 'field3' => 32]
    ],
    'E' => [
        'popup' => false,
        'designs' => 5,
        'design_colors' => ['design1' => '#7b0ea9', 'design2' => '#1e71ff', 'design3' => '#de339d', 'design4' => '#ffc04f', 'design5' => '#97d6a7'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 6, 'default' => '코엔자임큐텐', 'required' => true],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "혈압 높은 분\n활력이 떨어진 분\n체내 코큐텐 감소 연령\n항산화 건강 필요하신 분", 'required' => true, 'height' => 100],
            'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#000000', 'field3' => 'design_color'],
        'font_sizes' => ['field1' => 50, 'field2' => 30, 'field3' => 28]
    ],
    'F' => [
        'popup' => false,
        'designs' => 5,
        'design_colors' => ['design1' => '#9a88ff', 'design2' => '#ff99b6', 'design3' => '#0eced3', 'design4' => '#ffae4a', 'design5' => '#25ccff'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 5, 'default' => '당뇨관리', 'required' => true],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "혈당 점검\n인슐린 주사\n약사 상담", 'required' => true, 'height' => 80],
            'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#ffffff', 'field3' => 'design_color'],
        'font_sizes' => ['field1' => 60, 'field2' => 30, 'field3' => 28]
    ],
    'G' => [
        'popup' => false,
        'designs' => 5,
        'design_colors' => ['design1' => '#9a88ff', 'design2' => '#ff99b6', 'design3' => '#0eced3', 'design4' => '#ffae4a', 'design5' => '#25ccff'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 10, 'default' => '방광염예방', 'required' => true],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "크랜베리의 프로안토시아니딘은\n방광염을 유발하는 대장균에 달라붙어\n방광염을 일으키지 못하도록 합니다.", 'required' => true, 'height' => 80],
            'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#ffffff', 'field3' => 'design_color'],
        'font_sizes' => ['field1' => 60, 'field2' => 19, 'field3' => 28]
    ],
    'H' => [
        'popup' => false,
        'designs' => 5,
        'design_colors' => ['design1' => '#32a4c6', 'design2' => '#3bbbff', 'design3' => '#000000', 'design4' => '#27a1dc', 'design5' => '#ffffff'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 8, 'default' => '행복약국', 'required' => true],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "· 병원처방전 조제 ·\n· 전  문  의  약  품 ·\n· 일  반  의  약  품 ·\n· 건 강 기 능 식 품 ·", 'required' => true, 'height' => 100],
            'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#000000', 'field3' => 'design_color'],
        'font_sizes' => ['field1' => 60, 'field2' => 30, 'field3' => 28]
    ],
    'I' => [
        'popup' => false,
        'designs' => 6,
        'design_colors' => ['design1' => '#FFD700', 'design2' => '#FFD700', 'design3' => '#FFD700', 'design4' => '#FFD700', 'design5' => '#FFD700', 'design6' => '#FFD700'],
        'fields' => [
            'field1' => ['label' => '상단 제목', 'type' => 'input', 'default' => '정상적인 면역기능·성인건강을 위한', 'required' => true],
            'field2' => ['label' => '메인 제목', 'type' => 'input', 'maxlength' => 10, 'default' => '글루콘산 아연', 'required' => true],
            'field3' => ['label' => '가격', 'type' => 'input', 'default' => '20,000원', 'required' => false],
            'field4' => ['label' => '가격 상세', 'type' => 'input', 'default' => '(90일분 / 1일 1회 1정)', 'required' => false],
            'field5' => ['label' => '추천 문구', 'type' => 'input', 'default' => '이런 분들께 추천합니다!', 'required' => false],
            'field6' => ['label' => '효능 1', 'type' => 'input', 'default' => '✔ 고함량 아연 원하는 분', 'required' => false],
            'field7' => ['label' => '효능 2', 'type' => 'input', 'default' => '✔ 정상적인 면역기능', 'required' => false],
            'field8' => ['label' => '효능 3', 'type' => 'input', 'default' => '✔ 정상적인 세포분열', 'required' => false],
            'field9' => ['label' => '효능 4', 'type' => 'input', 'default' => '✔ 활기찬 생활 원하는 분', 'required' => false],
            'field10' => ['label' => '약국명', 'type' => 'input', 'default' => '행복약국', 'required' => true]
        ],
        'color_schemes' => ['field1' => '#ffffff', 'field2' => '#FFD700'],
        'font_sizes' => ['field1' => 18, 'field2' => 58]
    ],
    'J' => [
        'popup' => false,
        'designs' => 5,
        'design_colors' => ['design1' => '#33748f', 'design2' => '#33748f', 'design3' => '#33748f', 'design4' => '#33748f', 'design5' => '#33748f'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'textarea', 'default' => "관절 및 연골\n건강을 위해", 'required' => true, 'height' => 80],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "건강하고 행복한 일상을 위해\n불편한 관절건강\n더 늦기전에 지금부터 관리하세요!", 'required' => true, 'height' => 80],
			'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#000000', 'field3' => 'design_color'],
        'font_sizes' => ['field1' => 50, 'field2' => 20, 'field3' => 32]
    ],
    'K' => [
        'popup' => false,
        'designs' => 1,
        'design_colors' => ['design1' => '#16456f', 'design2' => '#00a7db', 'design3' => '#ED008C', 'design4' => '#528BFF', 'design5' => '#fb4844'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 10, 'default' => '행복약국', 'required' => true],
            'field2' => ['label' => '샘플(B)', 'type' => 'hidden', 'default' => '-', 'required' => false],
            'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#000000', 'field3' => '#000000'],
        'font_sizes' => ['field1' => 60]
    ],
    'L' => [
        'popup' => false,
        'designs' => 4,
        'design_colors' => ['design1' => '#fc4f88', 'design2' => '#031c3b', 'design3' => '#ffffff', 'design4' => '#031c3c'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 10, 'default' => '행복약국', 'required' => true],
            'field2' => ['label' => '샘플(B)', 'type' => 'hidden', 'default' => '-', 'required' => false],
            'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#000000', 'field3' => '#000000'],
        'font_sizes' => ['field1' => 27]
    ],
	'M' => [
		'popup' => false,
		'designs' => 7,
		'design_colors' => ['design1' => '#ffffff', 'design2' => '#01b9ff', 'design3' => '#32a4c6', 'design4' => '#15479c', 'design5' => '#0a70a0', 'design6' => '#225195', 'design7' => '#ffffff'],
		'design_colors2' => ['design1' => '#ffffff', 'design2' => '#000000', 'design3' => '#031c3c', 'design4' => '#4795d5', 'design5' => '#000000', 'design6' => '#4dd0e1', 'design7' => '#ffffff'],
		'fields' => [
			'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 10, 'default' => '행복약국', 'required' => true],
			'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "· 병원처방전 조제 ·\n· 전  문  의  약  품 ·\n· 일  반  의  약  품 ·\n· 건 강 기 능 식 품 ·", 'required' => true, 'height' => 100],
			'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
		],
		'color_schemes' => ['field1' => 'design_color', 'field2' => 'design_color2', 'field3' => '#000000'],
		'font_sizes' => ['field1' => 60,  'field2' => 30]
	],
    'N' => [
        'popup' => false,
        'designs' => 1,
        'design_colors' => ['design1' => '#fc4f88', 'design2' => '#031c3b', 'design3' => '#ffffff', 'design4' => '#031c3c'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 10, 'default' => '행복약국', 'required' => true],
            'field2' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false],
            'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#000000', 'field3' => '#000000'],
        'font_sizes' =>  ['field1' => 31]
    ]
	
];

$config = $poster_configs[$current_type];
?>

<link rel="stylesheet" href="<?php echo G5_URL ?>/poster/<?php echo $current_file_base_name; ?>.css">

<script>
    const currentType = '<?php echo $current_type; ?>';
    const config = <?php echo json_encode($config); ?>;

    const designUrls = Object.fromEntries(
        Array.from({length: config.designs}, (_, i) => [`design${i+1}`, `images/pharm_post_${currentType}0${i+1}.jpg`])
    );

    const defaultColors = config.design_colors;
    let selectedDesign = '<?php echo $selected_design; ?>'; // PHP 변수를 JavaScript 변수에 할당

    function selectDesign(design) {
        selectedDesign = design;
        document.querySelectorAll('.designs img').forEach(img => img.classList.remove('selected'));
        document.querySelector(`[data-design="${design}"]`).classList.add('selected');
        updatePoster();
    }

function updateDesignType(design) {
    // 페이지 내 이동임을 표시
    sessionStorage.setItem(PAGE_KEY, 'poster_new_test.php');
    
    // 페이지 이동
    window.location.href = `<?php echo $current_file_base_name; ?>.php?type=${design}`;
}

function updatePoster() {
    const posterContainer = document.getElementById('posterContainer');
    if (!posterContainer) return;
	
    // Type I 특별 처리
    if (currentType === 'I') {
        updatePosterTypeI();
        return;
    }

    // 일반 포스터 업데이트
    const posterElements = {
        field1: document.getElementById('posterField1'),
        field2: document.getElementById('posterField2'),
        field3: document.getElementById('posterField3')
    };

    // 텍스트 업데이트
    Object.keys(config.fields).forEach(fieldKey => {
        const inputEl = document.getElementById(fieldKey);
        const posterEl = posterElements[fieldKey];
        if (inputEl && posterEl) {
            posterEl.textContent = inputEl.value || '';
        }
    });

    // 색상 적용
    Object.keys(posterElements).forEach(fieldKey => {
        const posterEl = posterElements[fieldKey];
        if (posterEl && config.color_schemes && config.color_schemes[fieldKey]) {
            let colorValue;
            if (config.color_schemes[fieldKey] === 'design_color') {
                colorValue = config.design_colors[selectedDesign];
            } else if (config.color_schemes[fieldKey] === 'design_color2') {
                // field2에 대한 색상 로직 추가
                colorValue = config.design_colors2[selectedDesign];
            } else {
                colorValue = config.color_schemes[fieldKey];
            }
            posterEl.style.color = colorValue;
        }
    });

    // 폰트 크기 적용
    Object.keys(posterElements).forEach(fieldKey => {
        const posterEl = posterElements[fieldKey];
        if (posterEl && config.font_sizes && config.font_sizes[fieldKey]) {
            posterEl.style.fontSize = `${config.font_sizes[fieldKey]}px`;
        }
    });

    // Type A의 동적 폰트 크기 조정
    if (currentType === 'A' && posterElements.field1) {
        const field1Input = document.getElementById('field1');
        const textValue = field1Input ? field1Input.value : '';
        const hoursFontSize = window.innerWidth <= 500 ? 12.8 : 16;
        const contactFontSize = window.innerWidth <= 500 ? 12.8 : 16;
        let nameFontSize = 60;
        const textLength = textValue.length;
        if (textLength > 5) nameFontSize = Math.max(16, nameFontSize / (textLength / 5));

        if (posterElements.field2) posterElements.field2.style.fontSize = `${hoursFontSize}px`;
        if (posterElements.field3) posterElements.field3.style.fontSize = `${contactFontSize}px`;
        posterElements.field1.style.fontSize = `${nameFontSize}px`;

        const topValue = textLength >= 6 ? 7 + (textLength - 5) : 7;
        posterElements.field1.style.top = `${topValue}%`;
    }

    // Type H의 동적 폰트 크기 조정
    if (currentType === 'H' && posterElements.field1) {
        const field1Input = document.getElementById('field1');
        const textValue = field1Input ? field1Input.value : '';
        const hoursFontSize = 30;
        let nameFontSize = 60;
        const textLength = textValue.length;
        if (textLength > 5) nameFontSize = Math.max(16, nameFontSize / (textLength / 5));

        if (posterElements.field2) posterElements.field2.style.fontSize = `${hoursFontSize}px`;
        posterElements.field1.style.fontSize = `${nameFontSize}px`;

        const topValue = textLength >= 6 ? 10 + (textLength - 5) : 10;
        posterElements.field1.style.top = `${topValue}%`;
    }

	// Type K의 동적 폰트 크기 조정
	if (currentType === 'K' && posterElements.field1) {
		const field1Input = document.getElementById('field1');
		const textValue = field1Input ? field1Input.value : '';
		let nameFontSize = 60;
		const textLength = textValue.length;
		if (textLength > 5) nameFontSize = Math.max(30, nameFontSize / (textLength / 5));

		posterElements.field1.style.fontSize = `${nameFontSize}px`;

		// 스마트폰 여부 체크 (태블릿 제외)
		const userAgent = navigator.userAgent;
		const isMobilePhone = /Mobi/i.test(userAgent) && !/iPad|Tablet|Android(?!.*Mobile)/i.test(userAgent);
		let baseTop = 14; // 데스크톱/태블릿 기본값

		// isMobilePhone이 true일 때만, 즉 스마트폰일 때만 baseTop 값을 14로 설정
		if (isMobilePhone) {
			baseTop = 14;
		} else {
			// 데스크톱 및 태블릿에서 브라우저별 구분
			if (userAgent.includes('Edg')) {
				// Microsoft Edge
				baseTop = 15;
			} else if (userAgent.includes('Chrome')) {
				// Google Chrome
				baseTop = 14;
			} else if (userAgent.includes('Firefox')) {
				// Firefox
				baseTop = 14.5;
			} else if (userAgent.includes('Safari') && !userAgent.includes('Chrome')) {
				// Safari
				baseTop = 14.2;
			}
		}
		
		// 타입 K는 기본 top이 14%이므로 14를 기준으로 조정
		const topValue = textLength >= 6 ? baseTop + (textLength - 5) * 0.7 : baseTop;
		posterElements.field1.style.top = `${topValue}%`;
	}

    // Type M의 동적 폰트 크기 조정
    if (currentType === 'M' && posterElements.field1) {
        const field1Input = document.getElementById('field1');
        const textValue = field1Input ? field1Input.value : '';
        const hoursFontSize = 30;
        let nameFontSize = 60;
        const textLength = textValue.length;
        if (textLength > 4) nameFontSize = Math.max(11, nameFontSize / (textLength / 4.7));

        if (posterElements.field2) posterElements.field2.style.fontSize = `${hoursFontSize}px`;
        posterElements.field1.style.fontSize = `${nameFontSize}px`;

        const topValue = textLength >= 6 ? 21 + (textLength - 5)*0.95 : 21;
        posterElements.field1.style.top = `${topValue}%`;
    }


    // 배경 이미지 적용
    posterContainer.style.backgroundImage = `url('${designUrls[selectedDesign]}')`;
    document.body.className = `design-type-${currentType}`;

    // Hidden 필드 업데이트
    const hiddenDesign = document.getElementById('hiddenDesign');
    if (hiddenDesign) hiddenDesign.value = selectedDesign;
}

function updatePosterTypeI() {
    const posterElements = {
        field1: document.getElementById('posterField1'),    // 상단 제목
        field2: document.getElementById('posterField2'),    // 메인 제목
        field3: document.getElementById('posterField3'),    // 가격
        field4: document.getElementById('posterField4'),    // 가격 상세
        field5: document.getElementById('posterField5'),    // 추천 문구
        field6: document.getElementById('posterField6'),    // 효능 1
        field7: document.getElementById('posterField7'),    // 효능 2
        field8: document.getElementById('posterField8'),    // 효능 3
        field9: document.getElementById('posterField9'),    // 효능 4
        field10: document.getElementById('posterField10')   // 약국명
    };

    // 텍스트 업데이트
    Object.keys(config.fields).forEach(fieldKey => {
        const inputEl = document.getElementById(fieldKey);
        const posterEl = posterElements[fieldKey];
        if (inputEl && posterEl) {
            posterEl.textContent = inputEl.value || '';
        }
    });

    // 메인 타이틀 크기 조정
    const field2Input = document.getElementById('field2');
    const mainTitle = field2Input ? field2Input.value : '';
    const posterField2 = posterElements.field2;
    if (posterField2) {
        let mainTitleSize = 58;
        let topPosition = 12;
        if (mainTitle.length > 6) {
            mainTitleSize = Math.max(30, mainTitleSize / (mainTitle.length / 6));
            topPosition = 12 + (58 - mainTitleSize) * 0.1;
        }
        posterField2.style.fontSize = `${mainTitleSize}px`;
        posterField2.style.top = `${topPosition}%`;
    }

    // 배경 이미지 적용
    const posterContainer = document.getElementById('posterContainer');
    if (posterContainer) {
        posterContainer.style.backgroundImage = `url('${designUrls[selectedDesign]}')`;
    }
    document.body.className = `design-type-${currentType}`;

    // Hidden 필드 업데이트
    const hiddenDesign = document.getElementById('hiddenDesign');
    if (hiddenDesign) hiddenDesign.value = selectedDesign;
}

function resetPoster() {
    // 초기화 기능 제거됨
}

// 세션 스토리지를 사용한 페이지 내 상태 관리
const STORAGE_KEY = 'poster_new_test_typeSelector_state';
const PAGE_KEY = 'poster_new_test_current_page';

// 타입 선택창 토글 함수
function toggleTypeSelector() {
    const panel = document.getElementById('typeSelector');
    const icon = document.getElementById('toggleIcon');
    const isClosed = panel.classList.contains('closed');
    
    if (isClosed) {
        // 열기
        panel.classList.remove('closed');
        icon.classList.remove('fa-angle-right');
        icon.classList.add('fa-angle-left');
        sessionStorage.setItem(STORAGE_KEY, 'open');
    } else {
        // 닫기
        panel.classList.add('closed');
        icon.classList.remove('fa-angle-left');
        icon.classList.add('fa-angle-right');
        sessionStorage.setItem(STORAGE_KEY, 'closed');
    }
}

// 타입 선택 함수 (패널 상태 완전 보존)
function selectDesignType(design) {
    // active 상태만 변경
    document.querySelectorAll('.type-item').forEach(item => {
        item.classList.remove('active');
    });
    
    const clickedItem = event.currentTarget;
    if (clickedItem) {
        clickedItem.classList.add('active');
    }
    
    // 현재 페이지 표시를 세션에 저장
    sessionStorage.setItem(PAGE_KEY, 'poster_new_test.php');
    
    // 페이지 이동
    updateDesignType(design);
}

// active 항목을 가운데로 스크롤하는 함수
function scrollToActiveItem() {
    const activeItem = document.querySelector('.type-item.active');
    const container = document.querySelector('.type-selector-content');
    
    if (activeItem && container) {
        const containerHeight = container.clientHeight;
        const itemOffsetTop = activeItem.offsetTop;
        const itemHeight = activeItem.clientHeight;
        
        // active 항목이 컨테이너 중앙에 오도록 스크롤 위치 계산
        const scrollTop = itemOffsetTop - (containerHeight / 2) + (itemHeight / 2);
        
        container.scrollTo({
            top: scrollTop,
            behavior: 'smooth'
        });
    }
}

// 페이지 로드시 타입 선택창 상태 복원 (수정된 버전)
function initTypeSelector() {
    const panel = document.getElementById('typeSelector');
    const icon = document.getElementById('toggleIcon');
    
    // 현재 페이지 확인
    const currentPage = window.location.pathname.split('/').pop();
    const lastPage = sessionStorage.getItem(PAGE_KEY);
    
    // poster_new_test.php가 아닌 페이지에서 온 경우 또는 처음 방문 시 열림 상태로 초기화
    if (currentPage === 'poster_new_test.php' && lastPage === 'poster_new_test.php') {
        // 같은 페이지 내에서 이동한 경우만 저장된 상태 사용
        const savedState = sessionStorage.getItem(STORAGE_KEY);
        
        if (savedState === 'closed') {
            panel.classList.add('closed');
            icon.classList.remove('fa-angle-left');
            icon.classList.add('fa-angle-right');
        } else {
            // 기본 열림 상태
            panel.classList.remove('closed');
            icon.classList.remove('fa-angle-right');
            icon.classList.add('fa-angle-left');
            sessionStorage.setItem(STORAGE_KEY, 'open');
        }
    } else {
        // 다른 페이지에서 온 경우 또는 처음 방문 시 열림 상태로 초기화
        panel.classList.remove('closed');
        icon.classList.remove('fa-angle-right');
        icon.classList.add('fa-angle-left');
        sessionStorage.setItem(STORAGE_KEY, 'open');
        sessionStorage.setItem(PAGE_KEY, 'poster_new_test.php');
    }
    
    // active 항목을 가운데로 스크롤 (약간의 딜레이 후 실행)
    setTimeout(scrollToActiveItem, 100);
}

// 페이지를 벗어날 때 상태 초기화
window.addEventListener('beforeunload', function() {
    const currentPage = window.location.pathname.split('/').pop();
    if (currentPage === 'poster_new_test.php') {
        // poster_new_test.php를 벗어나는 경우 페이지 키 제거
        sessionStorage.removeItem(PAGE_KEY);
        sessionStorage.removeItem(STORAGE_KEY);
    }
});

// 페이지 가시성 변경 시 (다른 탭으로 이동하거나 돌아올 때)
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        // 페이지가 다시 보일 때 상태 확인
        const currentPage = window.location.pathname.split('/').pop();
        if (currentPage === 'poster_new_test.php') {
            sessionStorage.setItem(PAGE_KEY, 'poster_new_test.php');
        }
    }
});

// PC용 타입 선택기 초기화 함수
function initDesktopTypeSelector() {
    const desktopTypeItems = document.querySelectorAll('#typeSelectorDesktop .type-item');
    desktopTypeItems.forEach(item => {
        item.addEventListener('click', function() {
            const design = this.querySelector('.type-label').textContent;
            selectDesignType(design);
        });
    });
    
    // active 항목을 가운데로 스크롤 (PC에서는 필요 없으므로 제거)
}

    window.onload = () => {
        // 타입 선택창 상태 초기화를 가장 먼저 실행
        setTimeout(initTypeSelector, 50);
        
        // 페이지 로드 시 URL의 design 파라미터에 맞는 디자인 선택
        const initialDesignElement = document.querySelector(`[data-design="${selectedDesign}"]`);
        if (initialDesignElement) {
            initialDesignElement.classList.add('selected');
        } else {
            // URL에 유효하지 않은 디자인 값이 있을 경우, 기본값인 design1 선택
            selectedDesign = 'design1';
            document.querySelector('[data-design="design1"]').classList.add('selected');
        }

        document.getElementById('hiddenDesign').value = selectedDesign;
        document.getElementById('hiddenDesignType').value = currentType;
        updatePoster();

// PC용 타입 선택기 초기화
    initDesktopTypeSelector();

    document.querySelectorAll('input[name="design_type"]').forEach(radio =>
        radio.addEventListener('change', () => updateDesignType(radio.value))
    );
    
    const form = document.getElementById('posterForm');
    if (form) {
        form.addEventListener('submit', updatePoster);
    }
    
    window.addEventListener('resize', updatePoster);
    
    // 페이지 로드 완료 후 다시 한번 스크롤 실행 (안전장치)
    setTimeout(scrollToActiveItem, 500);
};
</script>
<?php if ($config['popup']): ?>
<?php //include_once("poster-A_popup.php"); ?>
<?php endif; ?>

<!-- PC용 타입 선택기 - 모바일 스타일 유지 -->
<div id="typeSelectorDesktop" class="type-selector-panel desktop">
    <div class="type-selector-content">
        <div class="type-selector-list">
            <?php foreach ($design_master_config['layout_rows'] as $row_name => $designs): ?>
                <?php foreach ($designs as $design): ?>
                    <div class="type-item <?php echo $current_type === $design ? 'active' : ''; ?>" 
                         onclick="selectDesignType('<?php echo $design; ?>')">
                        <span class="type-label"><?php echo $design; ?></span>
                        <?php if (in_array($design, $design_master_config['new_badges'])): ?>
                            <span class="type-new-badge">N</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- 모바일용 오른쪽 고정 타입 선택창 -->
<div id="typeSelector" class="type-selector-panel">
    <div class="type-selector-toggle" onclick="toggleTypeSelector()">
        <i class="fas fa-angle-left" id="toggleIcon"></i>
    </div>
    <div class="type-selector-content">
        <div class="type-selector-list">
            <?php foreach ($design_master_config['layout_rows'] as $row_name => $designs): ?>
                <?php foreach ($designs as $design): ?>
                    <div class="type-item <?php echo $current_type === $design ? 'active' : ''; ?>" 
                         onclick="selectDesignType('<?php echo $design; ?>')">
                        <span class="type-label"><?php echo $design; ?></span>
                        <?php if (in_array($design, $design_master_config['new_badges'])): ?>
                            <span class="type-new-badge">N</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="wrapper">
    <div class="container">
		<section class="design-selector">
			<div class="designs">
			<h3 style="min-width:80px;">Type <span style="color:#0057ff;font-size:26px;"><?php echo $current_type; ?><?php if(in_array($current_type, $design_master_config['new_badges'])) { ?><?php } ?></span></h3>
				<?php for ($i = 1; $i <= $config['designs']; $i++): ?>
					<img src="images/pharm_post_<?php echo $current_type; ?>0<?php echo $i; ?>.jpg" alt="디자인 <?php echo $i; ?>" data-design="design<?php echo $i; ?>" onclick="selectDesign('design<?php echo $i; ?>')">
				<?php endfor; ?>
			</div>
		</section>

        <section class="info-input">
            <?php if ($current_type !== 'I'): ?>
                <?php endif; ?>
            <form id="posterForm" method="post" action="poster_save_new.php">
                <input type="hidden" name="design" id="hiddenDesign" value="design1">
                <input type="hidden" name="design_type" id="hiddenDesignType" value="<?php echo htmlspecialchars($current_type); ?>">
                
                <table>
                    <?php foreach ($config['fields'] as $fieldKey => $fieldConfig): ?>
                        <?php if ($fieldConfig['type'] !== 'hidden'): ?>
                            <tr>
                                <td><label for="<?php echo $fieldKey; ?>"><?php echo $fieldConfig['label']; ?>:<?php echo $fieldConfig['required'] ? '<span style="color:red">*</span>' : ''; ?></label></td>
                                <td>
                                    <?php if ($fieldConfig['type'] === 'textarea'): ?>
                                        <textarea id="<?php echo $fieldKey; ?>" name="<?php echo $fieldKey; ?>" oninput="updatePoster()" <?php echo $fieldConfig['required'] ? 'required' : ''; ?> style="height: <?php echo isset($fieldConfig['height']) ? $fieldConfig['height'] . 'px' : '80px'; ?>;"><?php echo htmlspecialchars($fieldConfig['default']); ?></textarea>
                                    <?php else: ?>
                                        <input type="<?php echo $fieldConfig['type']; ?>" 
                                               id="<?php echo $fieldKey; ?>" 
                                               name="<?php echo $fieldKey; ?>" 
                                               oninput="updatePoster()" 
                                               <?php echo $fieldConfig['required'] ? 'required' : ''; ?>
                                               <?php echo isset($fieldConfig['maxlength']) ? 'maxlength="' . $fieldConfig['maxlength'] . '"' : ''; ?>
                                               value="<?php echo htmlspecialchars($fieldConfig['default']); ?>"
                                               placeholder="<?php echo isset($fieldConfig['maxlength']) ? '최대 ' . $fieldConfig['maxlength'] . '글자' : ''; ?>">
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <input type="hidden" name="<?php echo $fieldKey; ?>" value="<?php echo htmlspecialchars($fieldConfig['default']); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </table>
                
                <div class="button-group">
                    <button type="submit" class="save">저장하기</button>
                </div>
            </form>
        </section>
    </div>

    <section class="poster-preview">
        <p class="preview-notice" style="text-align: center;">*미리보기는 참조용이며 실제와 동일하지 않습니다.</p>
        <div class="monitor-frame">
            <div id="posterContainer" class="poster">
                <?php if ($current_type === 'I'): ?>
                    <div id="posterText">
                        <p id="posterField1"><?php echo htmlspecialchars($config['fields']['field1']['default']); ?></p>
                        <h1 id="posterField2"><?php echo htmlspecialchars($config['fields']['field2']['default']); ?></h1>
                        <div id="posterPriceArea">
                            <p id="posterField3"><?php echo htmlspecialchars($config['fields']['field3']['default']); ?></p>
                            <p id="posterField4"><?php echo htmlspecialchars($config['fields']['field4']['default']); ?></p>
                        </div>
                        <div id="posterRecommendArea">
                            <p id="posterField5"><?php echo htmlspecialchars($config['fields']['field5']['default']); ?></p>
                            <div id="posterBenefits">
                                <p id="posterField6"><?php echo htmlspecialchars($config['fields']['field6']['default']); ?></p>
                                <p id="posterField7"><?php echo htmlspecialchars($config['fields']['field7']['default']); ?></p>
                                <p id="posterField8"><?php echo htmlspecialchars($config['fields']['field8']['default']); ?></p>
								<p id="posterField9"><?php echo htmlspecialchars($config['fields']['field9']['default']); ?></p>
                            </div>
                        </div>
                        <p id="posterField10"><?php echo htmlspecialchars($config['fields']['field10']['default']); ?></p>
                    </div>
                <?php else: ?>
                    <div id="posterText">
                        <?php if (isset($config['fields']['field1'])): ?>
                            <h2 id="posterField1"><?php echo nl2br(htmlspecialchars($config['fields']['field1']['default'])); ?></h2>
                        <?php endif; ?>
                        <?php if (isset($config['fields']['field2'])): ?>
                            <p id="posterField2"><?php echo nl2br(htmlspecialchars($config['fields']['field2']['default'])); ?></p>
                        <?php endif; ?>
                        <?php if (isset($config['fields']['field3']) && $config['fields']['field3']['type'] !== 'hidden'): ?>
                            <p id="posterField3"><?php echo nl2br(htmlspecialchars($config['fields']['field3']['default'])); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php if ($current_type === 'I'): ?>
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

    #posterField1 { 
        position: absolute;
        top: 9%;
        left: 50%;
        transform: translateX(-50%); 
        color: white; 
        font-weight: 500;
        text-align: center;
        width: 90%;
        font-size: 18px;
    }

    #posterField2 { 
        position: absolute;
        top: 13%;
        left: 50%;
        transform: translateX(-50%); 
        color: #FFD700; 
        font-weight: 900;
        text-align: center;
        width: 90%;
        margin: 0;
        font-size: 58px;
    }

    #posterPriceArea {
        position: absolute;
        top: 26%;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        width: 90%;
    }

    #posterRecommendArea {
        position: absolute;
        top: 40%;
        left: 50%;
        transform: translateX(-50%);
        width: 85%;
        border-radius: 20px;
        padding: 20px;
        box-sizing: border-box;
    }

    #posterField10 { 
        position: absolute;
        bottom: 4%;
        left: 50%;
        transform: translateX(-50%); 
        color: white; 
        font-weight: 700;
        text-align: center;
        width: 90%;
        margin: 0;
        font-size: 24px;
    }

    #posterField3 { 
        color: white; 
        font-weight: 900;
        display: inline-block;
        margin: -34px 0px 0px -10px;
        font-size: 32px;
    }

    #posterField4 { 
        color: white; 
        font-weight: 400;
        margin-top: 40px;
        font-size: 12px;
    }

    #posterField5 { 
        color: black; 
        font-weight: 700;
        text-align: center;
        margin: 0 0 15px 0;
        background: #FFD700;
        padding: 8px;
        border-radius: 10px;
        font-size: 20px;
    }

    #posterBenefits {
        text-align: left;
    }

    #posterField6, #posterField7, #posterField8, #posterField9 { 
        color: #E91E63; 
        font-weight: 600;
        margin: 8px 0;
        line-height: 1.3;
        font-size: 24px;
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
        .container {
            order: 0;
        }
    }
    </style>
<?php endif; ?>

<?php
$user_ip = $_SERVER['REMOTE_ADDR'];

if ($user_ip == '59.22.76.67') {
    include_once("tail2.php");
} else {
    include_once("tail.php");
}
?>
}