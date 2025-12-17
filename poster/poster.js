let selectedDesign = 'design1'; // 기본 디자인

// 로딩 스피너 표시/숨김 함수
function showLoader() {
    document.getElementById('loader').style.display = 'flex';
}

function hideLoader() {
    document.getElementById('loader').style.display = 'none';
}

// 디자인 선택 함수
function selectDesign(design) {
    selectedDesign = design;
    const images = document.querySelectorAll('.designs img');
    images.forEach(img => img.classList.remove('selected'));
    document.querySelector(`[data-design="${design}"]`).classList.add('selected');
    updatePoster();
}

// 포스터 업데이트 함수
function updatePoster() {
    const name = document.getElementById('name').value || '약국 이름';
    const hours = document.getElementById('hours').value || '월~금 : 오전 9:00 ~ 오후 7:00\n토 : 오전 9:00 ~ 오후 1:00\n일요일,공휴일 : 휴무';
    const contact = document.getElementById('contact').value || '연락처';
    const nameColor = document.getElementById('nameColor').value;
    const nameSizeBase = parseInt(document.getElementById('nameSize').value) || 60; // 기본값 60px
    
    // 포스터 미리보기 업데이트
    const posterName = document.getElementById('posterName');
    posterName.textContent = name;
    posterName.style.color = nameColor;
    
    // 글자 수에 따라 폰트 크기 조절 (최대 280px 너비 기준)
    const textLength = name.length;
    let fontSize;
    if (textLength <= 5) {
        fontSize = Math.min(nameSizeBase, 60); // 짧은 이름은 최대 60px
    } else {
        fontSize = Math.max(16, Math.min(nameSizeBase, 320 / textLength)); // 글자 수에 따라 조절
    }
    posterName.style.fontSize = `${fontSize}px`;
    
    const posterHours = document.getElementById('posterHours');
    posterHours.textContent = hours;
    posterHours.style.fontSize = '18px'; // posterHours의 기본 폰트 사이즈 20px로 추가
    
    const posterContact = document.getElementById('posterContact'); // posterContact 요소 가져오기 추가
    posterContact.textContent = contact;
    posterContact.style.fontSize = '20px'; // posterContact의 기본 폰트 사이즈 20px로 추가

    const poster = document.getElementById('posterContainer');
    const designUrls = {
        'design1': 'images/pharm_post_blank.jpg',
        'design2': 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&h=400&q=80',
        'design3': 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&h=400&q=80',
        'design4': 'https://images.pexels.com/photos/3683064/pexels-photo-3683064.jpeg?auto=compress&cs=tinysrgb&w=300&h=400&dpr=1',
        'design5': 'https://images.pexels.com/photos/3551209/pexels-photo-3551209.jpeg?auto=compress&cs=tinysrgb&w=300&h=400&dpr=1',
        'design6': 'https://images.pexels.com/photos/3683056/pexels-photo-3683056.jpeg?auto=compress&cs=tinysrgb&w=300&h=400&dpr=1',
        'design7': 'https://images.pexels.com/photos/3985177/pexels-photo-3985177.jpeg?auto=compress&cs=tinysrgb&w=300&h=400&dpr=1',
        'design8': 'https://images.unsplash.com/photo-1603398938378-e54eab446dde?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&h=400&q=80',
        'design9': 'https://images.pexels.com/photos/3683098/pexels-photo-3683098.jpeg?auto=compress&cs=tinysrgb&w=300&h=400&dpr=1',
        'design10': 'https://images.pexels.com/photos/3683041/pexels-photo-3683041.jpeg?auto=compress&cs=tinysrgb&w=300&h=400&dpr=1'
    };
    poster.style.backgroundImage = `url('${designUrls[selectedDesign]}')`;

    // 모달 업데이트
    const modalName = document.getElementById('modalName');
    modalName.textContent = name;
    modalName.style.color = nameColor;
    modalName.style.fontSize = `${fontSize * 2}px`; // 모달에서는 2배 크기
    document.getElementById('modalHours').textContent = hours;
    document.getElementById('modalContact').textContent = contact;
    document.getElementById('modalPoster').style.backgroundImage = `url('${designUrls[selectedDesign]}')`;
}

// 포스터 저장 (PHP로 데이터 전송 예시)
function savePoster() {
    const name = document.getElementById('name').value;
    const hours = document.getElementById('hours').value;
    const contact = document.getElementById('contact').value;
    const nameColor = document.getElementById('nameColor').value;
    const nameSize = document.getElementById('nameSize').value;

    if (!name || !hours || !contact) {
        alert('모든 정보를 입력해주세요!');
        return;
    }

    showLoader();
    fetch('save_poster.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `name=${encodeURIComponent(name)}&hours=${encodeURIComponent(hours)}&contact=${encodeURIComponent(contact)}&design=${selectedDesign}&nameColor=${encodeURIComponent(nameColor)}&nameSize=${encodeURIComponent(nameSize)}`
    })
    .then(response => response.text())
    .then(data => {
        hideLoader();
        alert(data);
    })
    .catch(error => {
        hideLoader();
        console.error('Error:', error);
        alert('저장 중 오류가 발생했습니다.');
    });
}

// 포스터 다운로드
function downloadPoster() {
    showLoader();
    const poster = document.getElementById('posterContainer');
    html2canvas(poster).then(canvas => {
        const link = document.createElement('a');
        link.download = 'pharmacy_poster.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        hideLoader();
    }).catch(error => {
        hideLoader();
        console.error('Error:', error);
        alert('다운로드 중 오류가 발생했습니다.');
    });
}

// 포스터 초기화
function resetPoster() {
    document.getElementById('name').value = '';
    document.getElementById('hours').value = '';
    document.getElementById('contact').value = '';
    document.getElementById('nameColor').value = '#ffffff';
    document.getElementById('nameSize').value = '24';
    selectedDesign = 'design1';
    const images = document.querySelectorAll('.designs img');
    images.forEach(img => img.classList.remove('selected'));
    document.querySelector(`[data-design="design1"]`).classList.add('selected');
    updatePoster();
}

// 모달 표시
function showModal() {
    document.getElementById('posterModal').style.display = 'flex';
}

// 모달 숨기기
function hideModal() {
    document.getElementById('posterModal').style.display = 'none';
}

// 페이지 로드 시 로딩 상태 표시 후 숨기기
window.onload = function() {
    showLoader();
    setTimeout(() => {
        updatePoster();
        hideLoader();
    }, 1000);
};