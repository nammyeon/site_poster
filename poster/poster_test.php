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
        echo "<span style='position:fixed; top:10px; left:10px; color:red; background:white; padding:5px; border:1px solid red; z-index:9999;'>테스트 파일</span>";
    } else {
        // 현재 실적용 파일에 접속 중입니다.
        echo "<span style='position:fixed; top:10px; left:10px; color:red; background:white; padding:5px; border:1px solid red; z-index:9999;'>운영중 파일</span>";
    }
}

// 통합 디자인 설정 - 새로운 디자인을 추가할 때 여기만 수정하면 됩니다.
	$design_master_config = [
		'valid_types' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X'],
		'default_background_color' => '#0057ff',
		'new_badges' => ['A', 'T', 'U', 'V', 'W', 'X'],
		'layout_rows' => [
			'row1' => ['A', 'B', 'C', 'D', 'E', 'F', 'G','H'],
			'row2' => [ 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'],
			'row3' => [ 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X'],
		]
	];

  // 기존 변수들을 새로운 설정에서 가져오기
  $valid_types = $design_master_config['valid_types'];
  $background_color = $design_master_config['default_background_color'];


// 배열의 마지막 항목을 동적으로 가져옵니다.
$last_item = end($valid_types);

// URL 파라미터가 없거나 유효하지 않으면 마지막 항목을 기본값으로 설정합니다.
$current_type = isset($_GET['type']) ? trim($_GET['type']) : $last_item;
// design 파라미터를 읽어오고 없으면 design1을 기본값으로 설정
$selected_design = isset($_GET['design']) ? trim($_GET['design']) : 'design1';


// URL 파라미터 값이 유효성 목록에 없는 경우, 다시 마지막 항목으로 설정합니다.
if (!in_array($current_type, $valid_types)) {
    $current_type = $last_item;
}

// 포스터 타입별 설정 배열
$poster_configs = [
    'A' => [
        'popup' => false,
        'designs' => 5,
        'design_colors' => ['design1' => '#3a7094', 'design2' => '#124f64', 'design3' => '#01936c', 'design4' => '#046da6', 'design5' => '#2cb79a'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 12, 'default' => '행복 약국', 'required' => true],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "· 병원처방전 조제 ·\n· 전  문  의  약  품 ·\n· 일  반  의  약  품 ·\n· 건 강 기 능 식 품 ·", 'required' => true, 'height' => 80],
            'field3' => ['label' => '샘플(C)', 'type' => 'textarea', 'default' => "· 영업 시간 | 월 ~ 금 | 오전 8:00 ~ 오후 7:00\n                 | 토요일 | 오전 8:00 ~ 오후 7:00\n                 | 일요일 | 휴무", 'required' => true, 'height' => 80]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => 'design_color', 'field3' => 'design_color'],
        'font_sizes' => ['field1' => 50, 'field2' => 30, 'field3' => 14]
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
        'design_colors' => ['design1' => '#01a68c', 'design2' => '#031c3b', 'design3' => '#ffffff', 'design4' => '#031c3c'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 10, 'default' => '행복약국', 'required' => true],
            'field2' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false],
            'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#000000', 'field3' => '#000000'],
        'font_sizes' =>  ['field1' => 31]
    ],
    'O' => [
        'popup' => false,
        'designs' => 1,
        'design_colors' => ['design1' => '#194ce5'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 10, 'default' => '행복약국', 'required' => true],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "맞은편\n행복주차장 이용", 'required' => true, 'height' => 80],
            'field3' => ['label' => '샘플(C)', 'type' => 'input', 'maxlength' => 25, 'default' => '주차권 받아오시면 도장 찍어드립니다~', 'required' => true],        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#000', 'field3' => '#fff'],
        'font_sizes' => ['field1' => 60, 'field2' => 40, 'field3' => 16]
    ],
	'P' => [
        'popup' => false,
        'designs' => 1,
        'design_colors' => ['design1' => '#2b0e50', 'design2' => '#031c3b', 'design3' => '#ffffff', 'design4' => '#031c3c'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 10, 'default' => '행복약국', 'required' => true],
            'field2' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false],
            'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
        ],
        'color_schemes' => ['field1' => 'design_color', 'field2' => '#000000', 'field3' => '#000000'],
        'font_sizes' =>  ['field1' => 31]
    ],
	'Q' => [
        'popup' => false,
        'designs' => 1,
        'design_colors' => ['design1' => '#2b0e50', 'design2' => '#031c3b', 'design3' => '#ffffff', 'design4' => '#031c3c'],
        'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'input', 'maxlength' => 12, 'default' => '행복약국', 'required' => true],
            'field2' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false],
            'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
        ],
        'color_schemes' => ['field1' => '#000000', 'field2' => '#000000', 'field3' => '#000000'],
        'font_sizes' =>  ['field1' => 26]
    ],
	'R' => [
		'popup' => true,
		'designs' => 5,
		'design_colors' => ['design1' => '#d83382', 'design2' => '#000000', 'design3' => '#3f4553', 'design4' => '#1a4555', 'design5' => '#ff66a6'],
		'design_colors_field2' => ['design1' => '#000000', 'design2' => '#000000', 'design3' => '#000000', 'design4' => '#000000', 'design5' => '#ffffff'],
		'design_colors_field3' => ['design1' => '#3f4553', 'design2' => '#3f4553', 'design3' => '#ffffff', 'design4' => '#ffffff', 'design5' => '#ffffff'],
		'fields' => [
			'field1' => ['label' => '샘플(A)', 'type' => 'textarea', 'default' => "명절선물\n건강을 선물하세요!", 'required' => true],
			'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "약국 BEST 상품\n가격대별 추천 영양제\n연령별 추천 영양제", 'required' => true],
			'field3' => ['label' => '샘플(C)', 'type' => 'input', 'maxlength' => 10, 'default' => '행복약국', 'required' => true]
		],
		'color_schemes' => ['field1' => 'design_color', 'field2' => 'design_color_field2', 'field3' => 'design_color_field3'],
		'font_sizes' => ['field1' => 35, 'field2' => 29, 'field3' => 30]
	],
    'S' => [
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
	'T' => [
		'popup' => true,
		'designs' => 1,
		'design_colors' => ['design1' => '#FFD700'],
		'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'textarea', 'default' => "행복약국\n비타민 EVENT", 'required' => true],
			'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "약국제품\n10만원 이상 구매고객", 'required' => true],
			'field3' => ['label' => '샘플(C)', 'type' => 'textarea', 'default' => "3만원 상당 제품증정 택1\n· 콘드로이친  · 치과영양제\n· 종합비타민  · 유기농 레몬즙\n※ EVENT 기간 : 11.01 ~  11.30", 'required' => true]
        ],
        'font_sizes' => ['field1' => 33, 'field2' => 21, 'field3' => 17]
	],
    'U' => [
		'popup' => true,
		'designs' => 1,
		'design_colors' => ['design1' => '#FFD700'],
		'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'textarea', 'default' => "오메가3 지방산\n→ 학습능력 향상과 두뇌 건강에 탁월한 도움\n\n칼슘과 비타민D\n→ 성장기 튼튼한 골격 형성 위한 필수 영양소\n\n철분\n→ 빈혈 예방과 피로 회복에 중요한 영양소\n\n프로바이오틱스\n→ 장 건강을 개선해 면역력을 강화하는 영양소", 'required' => true, 'height' => 120],
            'field2' => ['label' => '샘플(B)', 'type' => 'input', 'maxlength' => 20, 'default' => '행복약국', 'required' => true],
            'field3' => ['label' => '샘플(C)', 'type' => 'hidden', 'default' => '-', 'required' => false]
        ],
        'font_sizes' => ['field1' => 14, 'field2' => 22]
	],
    'V' => [
		'popup' => true,
		'designs' => 1,
		'design_colors' => ['design1' => '#FFD700'],
		'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'textarea', 'default' => "지친 일상, 활력 충전과\n면역력 강화!", 'required' => true,'maxlength' => 40],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "✓ 멀티비타민\n✓ 밀크씨슬\n✓ 마그네슘\n✓ 유산균", 'required' => true, 'height' => 120,'maxlength' => 150],
            'field3' => ['label' => '샘플(C)', 'type' => 'input', 'maxlength' => 20, 'default' => '행복약국', 'required' => true]
        ],
        'font_sizes' => ['field1' => 26, 'field2' => 31, 'field3' => 24]
	],
    'W' => [
		'popup' => true,
		'designs' => 1,
		'design_colors' => ['design1' => '#FFD700'],
		'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'textarea', 'default' => "심혈관과 뼈 건강\n골격 건강 유지와 면역력 보강", 'required' => true, 'maxlength' => 40],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "✓ 코엔자임큐텐\n✓ 마그네슘\n✓ 오메가3\n✓ 칼슘\n✓ 비타민D\n✓ 콜라겐", 'required' => true, 'height' => 120,'maxlength' => 150],
            'field3' => ['label' => '샘플(C)', 'type' => 'input', 'maxlength' => 20, 'default' => '행복약국', 'required' => true]
        ],
        'font_sizes' => ['field1' => 24, 'field2' => 26, 'field3' => 24]
	],
    'X' => [
		'popup' => true,
		'designs' => 1,
		'design_colors' => ['design1' => '#FFD700'],
		'fields' => [
            'field1' => ['label' => '샘플(A)', 'type' => 'textarea', 'default' => "기억력 향상과\n관절 건강 영양제", 'required' => true, 'maxlength' => 40],
            'field2' => ['label' => '샘플(B)', 'type' => 'textarea', 'default' => "비타민 D\n칼 슘\n오메가 3\n비타민 B\n유 산 균\n루 테 인", 'required' => true, 'height' => 120, 'maxlength' => 150],
            'field3' => ['label' => '샘플(C)', 'type' => 'input', 'maxlength' => 20, 'default' => '행복약국', 'required' => true]
        ],
        'font_sizes' => ['field1' => 24, 'field2' => 26, 'field3' => 24]
	],
    
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
    // Use the dynamically determined script name here
    window.location.href = `<?php echo $current_file_base_name; ?>.php?type=${design}`;
}


// 동적 폰트 크기 조정 함수(kny update)
function applyDynamicFontScaling(fields, maxHeights = {}) {
    fields.forEach(field => {
        if (field.element && field.input) {
            const textValue = field.input.value || '';
            
            // 먼저 기본 폰트 크기로 설정
            field.element.style.fontSize = `${field.baseFontSize}px`;
            field.element.textContent = textValue;
            
            // 기본 폰트 크기 상태에서 높이 측정 (브라우저가 렌더링할 시간을 위해)
            const currentHeight = field.element.offsetHeight;
            const currentWidth = field.element.offsetWidth;
            
            // 실제 높이 기반 계산
            const lineHeight = parseFloat(getComputedStyle(field.element).lineHeight) || field.baseFontSize * 1.2;
            const totalLines = Math.ceil(currentHeight / lineHeight);
            
            // 높이 제한이 있는 경우
            let nameFontSize;
            const fieldId = field.element.id;
            if (maxHeights[fieldId]) {
                const maxHeight = maxHeights[fieldId];
                const lineHeightValue = 1.2;
                
                // adjustStartLine 이하에서는 기본 폰트 크기 유지
                if (totalLines <= field.adjustStartLine) {
                    nameFontSize = field.baseFontSize;
                } else {
                    // 높이 제한을 우선 적용
                    const maxFontSizeForHeight = (maxHeight * 0.95) / (totalLines * lineHeightValue);
                    const heightLimitedFontSize = Math.min(field.maxFontSize, maxFontSizeForHeight);
                    
                    // 줄 수에 따른 스케일링
                    const lineCountFactor = Math.max(0.7, 1 - (totalLines - field.adjustStartLine + 1) * 0.08);
                    const scaledFontSize = field.baseFontSize * lineCountFactor;
                    
                    // 높이 제한을 절대 우선으로 적용
                    nameFontSize = Math.max(field.minFontSize, Math.min(heightLimitedFontSize, scaledFontSize));
                }
            } else {
                // 높이 제한이 없는 경우 - 줄 수에 따른 스케일링
                const lineCountFactor = Math.max(0.75, 1 - (totalLines - field.adjustStartLine + 1) * 0.06);
                nameFontSize = Math.min(field.maxFontSize, Math.max(field.minFontSize, field.baseFontSize * lineCountFactor));
            }

            // CSS 스타일 적용
            field.element.style.fontSize = `${nameFontSize}px`;
            field.element.style.wordWrap = 'break-word';

            // 위치 조정
            const topAdjustment = Math.max(0, (totalLines - field.adjustStartLine + 1) * 2);
            const topValue = Math.max(field.minTop, field.baseTop - topAdjustment);
            field.element.style.top = `${topValue}%`;
        }
    });
}

    function updatePoster() {
    const posterContainer = document.getElementById('posterContainer');
    if (!posterContainer) return;
	
	// Type I, X 특별 처리
	if (currentType === 'I' || currentType === 'X') {
		updatePosterTypeSpecial();
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
				colorValue = config.design_colors2[selectedDesign];
			} else if (config.color_schemes[fieldKey] === 'design_color_field2') {
				colorValue = config.design_colors_field2[selectedDesign];
			} else if (config.color_schemes[fieldKey] === 'design_color_field3') {
				colorValue = config.design_colors_field3[selectedDesign];
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
        let nameFontSize = 50;
        const textLength = textValue.length;
        let topValue = 20
        if (textLength > 6){
            nameFontSize = Math.max(30, nameFontSize / (textLength / 6));
            topValue = 20 + (50 - nameFontSize) * 0.1;

            
        } else {
            nameFontSize = 50
            topValue = 20
        }
        
        posterElements.field1.style.top = `${topValue}%`;
        posterElements.field1.style.fontSize = `${nameFontSize}px`;


        const hoursFontSize = window.innerWidth <= 500 ? 25 : 28;
        const contactFontSize = window.innerWidth <= 500 ? 13 : 14;

        if (posterElements.field2) posterElements.field2.style.fontSize = `${hoursFontSize}px`;
        if (posterElements.field3) posterElements.field3.style.fontSize = `${contactFontSize}px`;

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

    // Type S의 동적 폰트 크기 조정
    if (currentType === 'S' && posterElements.field1) {
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



    // Type V의 동적 폰트 크기 조정
    if (currentType === 'V') {
        const fields = [
            { 
                element: posterElements.field1, 
                input: document.getElementById('field1'), 
                baseFontSize: 26, 
                maxFontSize: 26,
                minFontSize: 14,
                baseTop: 18.5, 
                minTop: 18.5,
                adjustStartLine: 2 
            },
            { 
                element: posterElements.field2, 
                input: document.getElementById('field2'), 
                baseFontSize: 31, 
                maxFontSize: 31,
                minFontSize: 10,
                baseTop: 62, 
                minTop: 62,
                adjustStartLine: 5 
            }
        ];

        const maxHeights = {
            'posterField2': 278
        };

        applyDynamicFontScaling(fields, maxHeights);
    } else if (currentType === 'W') {
        const fields = [
            { 
                element: posterElements.field1, 
                input: document.getElementById('field1'), 
                baseFontSize: 24, 
                maxFontSize: 24,
                minFontSize: 16,
                baseTop: 18.5, 
                minTop: 18.5,
                adjustStartLine: 3
            },
            { 
                element: posterElements.field2, 
                input: document.getElementById('field2'), 
                baseFontSize: 26, 
                maxFontSize: 26,
                minFontSize: 13,
                baseTop: 63, 
                minTop: 63,
                adjustStartLine: 6 
            }
        ];

        const maxHeights = {
            'posterField1': 75,
            'posterField2': 270
        };

        applyDynamicFontScaling(fields, maxHeights);
    }


    // 배경 이미지 적용
    posterContainer.style.backgroundImage = `url('${designUrls[selectedDesign]}')`;
    document.body.className = `design-type-${currentType}`;

    // 타입 내 디자인 선택시 클래스 추가 (251014 kny update)
    document.body.classList.add(`select-${selectedDesign}`);

    // Hidden 필드 업데이트
    const hiddenDesign = document.getElementById('hiddenDesign');
    if (hiddenDesign) hiddenDesign.value = selectedDesign;
}

function updatePosterTypeSpecial() {
    let fieldCount = 3;
     // Type별 필드 개수 설정
    if(currentType === "I"){
        fieldCount = 10;
    } 

    
    const posterElements = {};
    for (let i = 1; i <= fieldCount; i++) {
        posterElements[`field${i}`] = document.getElementById(`posterField${i}`);
    }

    // 텍스트 업데이트
    Object.keys(config.fields).forEach(fieldKey => {
        const inputEl = document.getElementById(fieldKey);
        const posterEl = posterElements[fieldKey];
        if (inputEl && posterEl) {
            // Type X의 field2는 특별 처리 - 엔터 기준으로 p 태그 생성
            if (currentType === 'X' && fieldKey === 'field2') {
                const text = inputEl.value || '';
                const lines = text.split('\n').filter(line => line.trim() !== '');
                
                // 기존 내용 제거
                posterEl.innerHTML = '';
                
                // 각 줄을 p 태그로 감싸기
                lines.forEach(line => {
                    const p = document.createElement('p');
                    p.textContent = line.trim();
                    
                    // 한 줄의 글자수가 6자를 초과하면 작은 폰트 적용
                    if (line.trim().length > 9) {
                        p.style.fontSize = '12px';
                    }
                    
                    posterEl.appendChild(p);
                });
            } else {
                // Type S의 textarea 필드들은 줄바꿈 처리
                // if (currentType === 'T' && ['field1', 'field6'].includes(fieldKey)) {
                //     posterEl.innerHTML = (inputEl.value || '').replace(/\n/g, '<br>');
                // } else {
                //     posterEl.textContent = inputEl.value || '';
                // }
                
                posterEl.textContent = inputEl.value || '';
            }
        }
    });

    // Type I만 메인 타이틀 크기 조정
    if (currentType === 'I') {
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
    } 

    if (currentType === 'X') {
        const fields = [
            { 
                element: posterElements.field1, 
                input: document.getElementById('field1'), 
                baseFontSize: 25, 
                maxFontSize: 25,
                minFontSize: 16,
                baseTop: 18.5, 
                minTop: 18.5,
                adjustStartLine: 2
            },
        ];

        const maxHeights = {
            'posterField1': 70,
        };

        applyDynamicFontScaling(fields, maxHeights);
    }

    // 배경 이미지 적용
    const posterContainer = document.getElementById('posterContainer');
    if (posterContainer) {
        posterContainer.style.backgroundImage = `url('${designUrls[selectedDesign]}')`;
    }
    document.body.className = `design-type-${currentType}`;

    // 동적 폰트 크기 조정 적용 - 정의된 필드들에만 적용
    const fields = [];
    const maxHeights = {};
    
    // field1, field2, field3 등 정의된 필드들에 대해 설정
    for (let i = 1; i <= fieldCount; i++) {
        const fieldKey = `field${i}`;
        const inputEl = document.getElementById(fieldKey);
        const posterEl = posterElements[fieldKey];
        
        if (inputEl && posterEl) {
            // 기본 폰트 설정값 가져오기
            const baseFontSize = config.font_sizes && config.font_sizes[fieldKey] ? config.font_sizes[fieldKey] : 24;
            const minFontSize = Math.max(8, baseFontSize * 0.4);
            const maxFontSize = baseFontSize * 1.2;
            
            // 기본 위치 설정값
            const baseTop = 25;
            const minTop = 15;
            
            fields.push({
                element: posterEl,
                input: inputEl,
                baseFontSize: baseFontSize,
                minFontSize: minFontSize,
                maxFontSize: maxFontSize,
                baseTop: baseTop,
                minTop: minTop,
                adjustStartLine: 2
            });
            
            // Type X의 field2는 특별히 높이 제한 설정
            if (currentType === 'X' && fieldKey === 'field2') {
                maxHeights[posterEl.id] = 120; // 최대 높이 (픽셀)
            }
        }
    }

    // Hidden 필드 업데이트
    const hiddenDesign = document.getElementById('hiddenDesign');
    if (hiddenDesign) hiddenDesign.value = selectedDesign;
}


function resetPoster() {
    // 초기화 기능 제거됨
}

function limitNewlines(textarea, maxNewlines) {
    // 최대 엔터 수 제한
    const text = textarea.value;
    const newlineCount = (text.match(/\n/g) || []).length;
    
    if (newlineCount > maxNewlines) {
        // 최대 엔터 수를 초과하면 마지막 엔터를 제거
        const lines = text.split('\n');
        textarea.value = lines.slice(0, maxNewlines + 1).join('\n');
    }
}

window.onload = () => {
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

    document.querySelectorAll('input[name="design_type"]').forEach(radio =>
        radio.addEventListener('change', () => updateDesignType(radio.value))
    );
    
    const form = document.getElementById('posterForm');
    if (form) {
        form.addEventListener('submit', updatePoster);
    }
    
    window.addEventListener('resize', updatePoster);
};
</script>
<?php if ($config['popup']): ?>
<?php include_once("poster-A_popup.php"); ?>
<?php endif; ?>

<div class="design-selector-wrapper">
    <div class="design-selector-group">
        <form method="POST" id="designForm">
            <?php foreach ($design_master_config['layout_rows'] as $row_name => $designs): ?>
                <div <?php echo $row_name === 'row2' || 'row3' ? 'style="margin-top: 10px; text-align: left"' : ''; ?>>
                    <?php foreach ($designs as $design): ?>
                        <div style="position: relative; display: inline-block;">
                            <input type="radio" 
                                   name="design_type" 
                                   id="design_<?php echo strtolower($design); ?>" 
                                   value="<?php echo $design; ?>" 
                                   onchange="updateDesignType('<?php echo $design; ?>')" 
                                   class="design-radio" 
                                   <?php echo $current_type === $design ? 'checked' : ''; ?>>
                            <label for="design_<?php echo strtolower($design); ?>" 
                                   class="design-label" 
                                   style="background: <?php echo $current_type === $design ? $design_master_config['default_background_color'] : '#999'; ?>;">
                                <strong id="design_select"><?php echo $design; ?></strong>
                                <?php if (in_array($design, $design_master_config['new_badges'])): ?>
                                    <span class="new-badge" style="position: absolute; top: 0px; right: 0px; background: #ff4444; color: white; font-size: 10px; padding: 2px; line-height: 1;">N</span>
                                <?php endif; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </form>
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
                                        <?php if ($current_type === 'X' && $fieldKey === 'field2'): ?>
                                            <span style="color: #666; font-size: 12px; display: block; margin-top: 5px;">최대 6개의 내용 작성이 가능합니다.</span>
                                        <?php endif; ?>
                                        <textarea id="<?php echo $fieldKey; ?>" name="<?php echo $fieldKey; ?>" oninput="<?php echo ($current_type === 'X' && $fieldKey === 'field2') ? 'limitNewlines(this, 5); updatePoster()' : 'updatePoster()'; ?>" <?php echo $fieldConfig['required'] ? 'required' : ''; ?> <?php echo isset($fieldConfig['maxlength']) ? 'maxlength="' . $fieldConfig['maxlength'] . '"' : ''; ?> style="height: <?php echo isset($fieldConfig['height']) ? $fieldConfig['height'] . 'px' : '80px'; ?>;"><?php echo htmlspecialchars($fieldConfig['default']); ?></textarea>
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

<?php
$user_ip = $_SERVER['REMOTE_ADDR'];

if ($user_ip == '59.22.76.67') {
    include_once("tail2.php");
} else {
    include_once("tail.php");
}
?>