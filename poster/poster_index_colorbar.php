<?php
include_once("head.php");

if (!$is_member || !isset($member['mb_id'])) {
    alert("회원전용입니다.", "./index.php");
    exit;
}
?>
<style>
/* CSS resets and base styles */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: sans-serif;
        background: rgba(155, 155, 155, 0.5);
        color: #1a202c;
        line-height: 1.7;
        overflow-x: hidden;
        padding-bottom: 80px;
    }

    /* Gallery header styles */
    .gallery-header {
        text-align: center;
        background: #fff;
        margin-bottom: 10px;
    }

    .gallery-title {
        color: #0057ff;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    /* Main gallery container and grid styles */
    .gallery-container {
		margin: 0 auto;
        max-width: 1200px;
        padding: 0 3px;
        position: relative;
    }
    
    /* New CSS for the poster sets */
    .gallery-grid {
        display: grid;
        gap: 15px;
        padding: 10px;
        /* On mobile, each set is a single column. */
        grid-template-columns: 1fr;
    }
    
/* 기존 poster-set 관련 CSS 삭제하고 새로운 CSS로 교체 */
.poster-set {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 30px;
}

.main-poster {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.main-poster:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.color-selector-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 10px;
}

.sample-label {
    font-size: 12px;
    font-weight: 500;
    color: #666;
    white-space: nowrap;
}

.color-selector {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.color-dot {
    width: 19px;
    height: 19px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
}

.color-dot:hover {
    transform: scale(1.2);
    border-color: #333;
}

.color-dot.active {
    border-color: #0057ff;
    transform: scale(1.3);
}

/* 기존 left-poster, right-grid 관련 CSS 삭제 */

/* 기존 right-top-poster, right-bottom-poster 클래스는 삭제하고 위 4개로 대체 */

    .poster-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .poster-item:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        z-index: 10;
    }

    .left-poster {
        grid-column: 1;
        grid-row: 1 / span 2;
    }
    
    .right-top-poster {
        grid-column: 2;
        grid-row: 1;
    }
    
    .right-bottom-poster {
        grid-column: 2;
        grid-row: 2;
    }

    .poster-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
   

    .poster-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: #000000;
        color: white;
        padding: 3px 5px;
        transform: translateY(0);
        opacity: 0.5;
    }

    .poster-item:hover .poster-overlay {
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.85));
        opacity: 1;
    }

    .poster-type {
        font-size: 20px;
        font-weight: 900;
        margin-bottom: 5px;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 1);
    }

    .poster-description {
        font-size: 12px;
        opacity: 0.9;
        line-height: 1.4;
    }

    .new-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #ff4444;
        color: white;
        font-size: 10px;
        padding: 3px 6px;
        border-radius: 12px;
        font-weight: 700;
        z-index: 5;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

.color-selector-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 10px;
}

.color-selector {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    align-items: center;
}

/* 샘플 아이콘에도 동일한 크기 적용 */
.sample-icon {
    width: 19px;  /* 추가 */
    height: 19px; /* 추가 */
    background: #f5f5f5 !important;
    border: 2px solid #999 !important;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: bold;
    color: #666;
    box-sizing: border-box;
}

.sample-icon:hover {
    transform: scale(1.2);
    border-color: #333 !important;
    background: #e9e9e9 !important;
}

.sample-icon.active {
    border-color: #0057ff !important;
    background: #f0f8ff !important;
    color: #0057ff;
}

    /* Responsive styles for tablets and desktops */
    @media (min-width: 768px) {
        .gallery-grid {
            grid-template-columns: repeat(2, 1fr); /* Two sets per row */
        }

        .gallery-title {
            font-size: 36px;
        }
    }

    @media (min-width: 1024px) {
        .gallery-grid {
            grid-template-columns: repeat(2, 1fr);
            max-width: 1200px;
            margin: 0 auto;
            /* Add a larger gap for desktop views */
            gap: 30px; 
        }

        .gallery-container {
            padding: 0 20px;
        }


    }

    /* PC 화면에서는 기존 레이아웃을 유지합니다. 
       모바일에만 적용할 CSS는 아래의 @media 쿼리 안에 넣어야 합니다. */

/* 반응형 스타일 수정 */
@media (max-width: 767px) {

    .gallery-container {
        margin: 0 auto;
    }
	
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr); /* 모바일에서 한줄에 2개 */
        gap: 15px;
    }
    
    .color-selector {
        justify-content: center;
    }
}

@media (min-width: 768px) and (max-width: 1023px) {
    .gallery-grid {
        grid-template-columns: repeat(3, 1fr); /* 태블릿에서 한줄에 3개 */
        gap: 20px;
    }
}

@media (min-width: 1024px) {
    .gallery-grid {
        grid-template-columns: repeat(4, 1fr); /* PC에서 한줄에 4개 */
        gap: 25px;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .gallery-container {
        padding: 0 20px;
    }
}

    /* Scrollbar and loading styles remain the same */
    .gallery-container::-webkit-scrollbar {
        display: none;
    }

    .gallery-container {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .poster-item.loading {
        background: linear-gradient(90deg, #f0f0f0 25%, transparent 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }
</style>
</head>
<body>

<div class="gallery-header">
	<h1 class="gallery-title">WAVEDREAM 포스터 선택</h1>
</div>

<div class="gallery-container" id="galleryContainer">
	<div class="gallery-grid" id="galleryGrid">
	</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    // Data for posters
    const posterTypes = [
        { type: 'A', description: '약국 기본 정보 포스터', isNew: false },
        { type: 'B', description: '약사추천 필수영양제', isNew: false },
        { type: 'C', description: '동물의약품 안내', isNew: false },
        { type: 'D', description: '피로회복제 세트', isNew: false },
        { type: 'E', description: '코엔자임큐텐 안내', isNew: false },
        { type: 'F', description: '당뇨관리 서비스', isNew: false },
        { type: 'G', 'description': '방광염예방 안내', isNew: false },
        { type: 'H', description: '약국 종합 서비스', isNew: false },
        { type: 'I', description: '글루콘산 아연 상품', isNew: true },
        { type: 'J', description: '관절 및 연골건강', isNew: true },
        { type: 'K', description: '약국 브랜딩', isNew: true },
        { type: 'L', description: '심플 약국 로고', isNew: true },
        { type: 'M', description: '프리미엄 약국 서비스', isNew: true },
        { type: 'N', description: '민생회복 소비쿠폰', isNew: true },
        { type: 'O', description: '주차안내', isNew: true }
		
    ];

    $(document).ready(function() {
        loadPosterSets();
    });

    // Intersection Observer를 사용하여 Lazy Loading 구현
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const posterItem = entry.target;
                const img = posterItem.querySelector('.poster-image');
                const src = img.dataset.src; // data-src 속성에서 실제 이미지 URL 가져오기

                // 이미지가 아직 로드되지 않았다면 로드 시작
                if (src) {
                    img.src = src;
                    img.onload = () => {
                        posterItem.classList.remove('loading');
                        img.removeAttribute('data-src'); // 로드 후 속성 제거
                    };
                    img.onerror = () => {
                        console.error('이미지 로드 실패:', src);
                        posterItem.style.display = 'none'; // 혹은 remove()
                    };
                }
                observer.unobserve(posterItem); // 로드 완료 후 관찰 중단
            }
        });
    }, {
        rootMargin: '100px' // 뷰포트 하단에서 100px 미리 로드 시작
    });

function createPosterSet(poster, index) {
    const posterSet = document.createElement('div');
    posterSet.className = 'poster-set';
    
    // 메인 포스터 생성
    const mainPoster = document.createElement('div');
    mainPoster.className = 'poster-item main-poster loading';
    
    // 초기 로딩시 00.jpg 파일 사용
    const imageFileName = `pharm_post_${poster.type}00.jpg`;
    const imageUrl = `/poster/images/${imageFileName}`;
    
	mainPoster.innerHTML = `
		<div class="poster-content" data-type="${poster.type}">
			<img class="poster-image" data-src="${imageUrl}" alt="Type ${poster.type} 디자인">
			<div class="poster-overlay">
				<div class="poster-type">Type ${poster.type} <sup style="font-size:10px;font-weight:normal">(sample)</sup></div>
				<div class="poster-description">${poster.description}</div>
			</div>
			${poster.isNew ? '<div class="new-badge">NEW</div>' : ''}
		</div>
	`;
    
    // 메인 포스터 클릭 이벤트 - 항상 design1(01.jpg)로 연결
    let currentSelectedIndex = -1; // -1은 샘플(00.jpg), 0~n은 컬러 인덱스
    
    mainPoster.addEventListener('click', function() {
        let redirectUrl = `poster_new.php?type=${poster.type}`;
        if (currentSelectedIndex === -1) {
            redirectUrl += `&design=design1`; // 샘플은 design1로
        } else {
            const designMap = {
                0: 'design1',
                1: 'design2', 
                2: 'design3',
                3: 'design4'
            };
            if (designMap[currentSelectedIndex]) {
                redirectUrl += `&design=${designMap[currentSelectedIndex]}`;
            }
        }
        window.location.href = redirectUrl;
    });
    
    // 컬러 선택기 컨테이너 생성
    const colorSelectorWrapper = document.createElement('div');
    colorSelectorWrapper.className = 'color-selector-wrapper';

	// 샘플 아이콘 생성 (초기 선택 상태)
	const sampleIcon = document.createElement('div');
	sampleIcon.className = 'color-dot sample-icon active';
	sampleIcon.innerHTML = 'S';
	sampleIcon.title = '샘플이미지'; // hover 툴팁 추가

	sampleIcon.addEventListener('click', function(e) {
		e.stopPropagation();
		
		// 모든 활성 상태 제거
		colorSelectorWrapper.querySelectorAll('.color-dot').forEach(dot => {
			dot.classList.remove('active');
			// 크기도 원래대로 (active 클래스 제거시)
			dot.style.transform = 'scale(1)';
		});
		
		this.classList.add('active');
		// 샘플 아이콘도 동일한 크기 변화 적용
		this.style.transform = 'scale(1.3)';
		
		// 샘플 이미지로 변경 (00.jpg)
		const sampleImageFileName = `pharm_post_${poster.type}00.jpg`;
		const sampleImageUrl = `/poster/images/${sampleImageFileName}`;
		const img = mainPoster.querySelector('.poster-image');
		img.src = sampleImageUrl;
		
		currentSelectedIndex = -1; // 샘플 선택 상태
		
		// Type 텍스트에 (sample) 표시
		const posterType = mainPoster.querySelector('.poster-type');
		if (posterType) {
			posterType.innerHTML = `Type ${poster.type} <sup style="font-size:10px">(sample)</sup>`;
		}
	});

    // 컬러 선택기 생성
    const colorSelector = document.createElement('div');
    colorSelector.className = 'color-selector';

    // 샘플 아이콘을 먼저 추가
    colorSelector.appendChild(sampleIcon);

    // poster_new_test.php의 설정에서 컬러 정보 가져오기
    const posterColors = getPosterColors(poster.type);

posterColors.forEach((color, colorIndex) => {
    const colorDot = document.createElement('div');
    colorDot.className = 'color-dot';
    colorDot.style.backgroundColor = color;
    
	colorDot.addEventListener('click', function(e) {
		e.stopPropagation();
		
		// 모든 활성 상태 제거 및 크기 초기화
		colorSelectorWrapper.querySelectorAll('.color-dot').forEach(dot => {
			dot.classList.remove('active');
			dot.style.transform = 'scale(1)';
		});
		
		this.classList.add('active');
		this.style.transform = 'scale(1.3)'; // 동일한 크기 변화 적용
		
		// 이미지 변경
		const newImageFileName = `pharm_post_${poster.type}0${colorIndex + 1}.jpg`;
		const newImageUrl = `/poster/images/${newImageFileName}`;
		const img = mainPoster.querySelector('.poster-image');
		img.src = newImageUrl;
		
		currentSelectedIndex = colorIndex;
		
		// Type 텍스트에 컬러 번호 표시 (A-1, B-2 형식)
		const posterType = mainPoster.querySelector('.poster-type');
		if (posterType) {
			posterType.textContent = `Type ${poster.type}-${colorIndex + 1}`;
		}
	});
    
    colorSelector.appendChild(colorDot);
});

    colorSelectorWrapper.appendChild(colorSelector);
    posterSet.appendChild(mainPoster);
    posterSet.appendChild(colorSelectorWrapper);
    
    // Intersection Observer에 등록
    observer.observe(mainPoster);
    
    return posterSet;
}

// 포스터 타입별 컬러 정보를 반환하는 함수 추가
function getPosterColors(type) {
    const colorConfig = {
        'A': ['#00a7db', '#ED008C', '#528BFF', '#fb4844', '#9c57ff'],
        'B': ['#00BA88', '#3068d0', '#ffc132', '#d633fe', '#f33e31'],
        'C': ['#a499ff', '#ff99b6', '#008cbd', '#ff7062', '#4cbf64'],
        'D': ['#2858d6', '#fe49f5', '#5adcfe', '#a733e5', '#f85c66'],
        'E': ['#7b0ea9', '#1e71ff', '#de339d', '#ffc04f', '#97d6a7'],
        'F': ['#9a88ff', '#ff99b6', '#0eced3', '#ffae4a', '#25ccff'],
        'G': ['#9a88ff', '#ff99b6', '#0eced3', '#ffae4a', '#25ccff'],
        'H': ['#32a4c6', '#3bbbff', '#000000', '#27a1dc', '#ffffff'],
        'I': ['#FFD700', '#FFD700', '#FFD700', '#FFD700', '#FFD700', '#FFD700'],
        'J': ['#33748f', '#33748f', '#33748f', '#33748f', '#33748f'],
        'K': ['#16456f'],
        'L': ['#fc4f88', '#031c3b', '#ffffff', '#031c3c'],
        'M': ['#ffffff', '#01b9ff', '#32a4c6', '#15479c', '#0a70a0', '#225195', '#ffffff'],
        'N': ['#fc4f88']
    };
    
    return colorConfig[type] || ['#0057ff'];
}

function loadPosterSets() {
    const grid = document.getElementById('galleryGrid');
    grid.innerHTML = '';

    posterTypes.forEach((poster, index) => {
        const posterSet = createPosterSet(poster, index);
        grid.appendChild(posterSet);
    });
}
</script>
<?php
$user_ip = $_SERVER['REMOTE_ADDR'];

if ($user_ip == '59.22.76.67') {
    include_once("tail2.php");
} else {
    include_once("tail.php");
}
?>