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
        max-width: 95vw;
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
    
.poster-set {
    display: grid;
    grid-template-columns: 50% 50%;
    grid-template-rows: 1fr;
    gap: 15px;
}

/* right 영역을 별도의 2x2 그리드로 구성 */
.right-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
    gap: 8px;
    grid-column: 2;
    grid-row: 1;
}

.right-poster-1 {
    grid-column: 1;
    grid-row: 1;
}

.right-poster-2 {
    grid-column: 2;
    grid-row: 1;
}

.right-poster-3 {
    grid-column: 1;
    grid-row: 2;
}

.right-poster-4 {
    grid-column: 2;
    grid-row: 2;
}

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

@media (max-width: 767px) {
    /* 모바일에서 홀수 번째 포스터 세트 (기본 레이아웃) */
    .poster-set:nth-child(odd) {
        grid-template-columns: 50% 50%;
        grid-template-rows: 1fr;
    }

    .poster-set:nth-child(odd) .left-poster {
        grid-column: 1;
        grid-row: 1;
    }

    .poster-set:nth-child(odd) .right-grid {
        grid-column: 2;
        grid-row: 1;
    }

    /* 모바일에서 짝수 번째 포스터 세트 (좌우 반전 레이아웃) */
    .poster-set:nth-child(even) {
        grid-template-columns: 50% 50%;
        grid-template-rows: 1fr;
    }

    .poster-set:nth-child(even) .left-poster {
        grid-column: 2;
        grid-row: 1;
    }

    .poster-set:nth-child(even) .right-grid {
        grid-column: 1;
        grid-row: 1;
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

    function createPosterItem(poster, fileIndex, isRightGridItem = false) {
        const posterItem = document.createElement('div');
        posterItem.className = `poster-item loading ${isRightGridItem ? `right-poster-${fileIndex}` : 'left-poster'}`;
        
        const imageFileName = `pharm_post_${poster.type}0${fileIndex}.jpg`;
        const imageUrl = `/poster/images/${imageFileName}`;
        
        // Lazy Loading을 위해 src 대신 data-src 사용
        const imgSrcAttribute = `data-src="${imageUrl}"`;

        let overlayContent;
        if (fileIndex === 0) {
            overlayContent = `
                <div class="poster-type">Type ${poster.type}</div>
                <div class="poster-description">${poster.description}</div>
            `;
        } else {
            overlayContent = `
                <div class="poster-type">${poster.type}${fileIndex}</div>
            `;
        }

        posterItem.innerHTML = `
            <div class="poster-content" data-type="${poster.type}">
                <img class="poster-image" ${imgSrcAttribute} alt="Type ${poster.type} 디자인">
                <div class="poster-overlay">
                    ${overlayContent}
                </div>
                ${poster.isNew && fileIndex === 0 ? '<div class="new-badge">NEW</div>' : ''}
            </div>
        `;
        
        // 포스터에 클릭 이벤트 부여
        posterItem.addEventListener('click', function() {
            const designMap = {
                0: 'left',
                1: 'design1',
                2: 'design2',
                3: 'design3',
                4: 'design4'
            };

            let redirectUrl = `poster_new_test.php?type=${poster.type}`;
            
            if (designMap[fileIndex]) {
                redirectUrl += `&design=${designMap[fileIndex]}`;
            }
            
            window.location.href = redirectUrl;
        });

        // Intersection Observer에 등록
        observer.observe(posterItem);

        return posterItem;
    }

    function loadPosterSets() {
        const grid = document.getElementById('galleryGrid');
        grid.innerHTML = ''; // 기존 요소 초기화

        posterTypes.forEach((poster, index) => {
            const posterSet = document.createElement('div');
            posterSet.className = 'poster-set';
            
            // 좌우 반전 레이아웃을 위한 클래스 추가
            if (index % 2 !== 0) {
                posterSet.classList.add('reverse-layout');
            }
            
            // 좌측 포스터 생성 및 추가
            const leftPoster = createPosterItem(poster, 0); 
            posterSet.appendChild(leftPoster);
            
            // 우측 포스터 영역을 위한 컨테이너 생성
            const rightGrid = document.createElement('div');
            rightGrid.className = 'right-grid';
            
            // 우측 포스터 4개 생성 및 추가
            for (let i = 1; i <= 4; i++) {
                const rightPoster = createPosterItem(poster, i, true);
                rightGrid.appendChild(rightPoster);
            }
            
            posterSet.appendChild(rightGrid);
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