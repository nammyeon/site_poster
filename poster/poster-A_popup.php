<?php
// Prevent direct access
if (!defined('G5_URL')) exit;

$enable_popup = true;
if (!isset($enable_popup) || !$enable_popup) return;
?>

<style>
/* Popup styles */
:root {
    --primary: #0057ff;
    --hover: #39c2ff;
    --text: #1a202c;
    --bg: rgba(255, 255, 255, 0.95);
    --alert: #fff3cd;
    --alert-text: #e53e3e;
}

.popup-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.popup-content {
    background: var(--bg);
    backdrop-filter: blur(5px);
    padding: 30px;
    border-radius: 10px;
    border: 2px solid var(--hover);
    max-width: 420px;
    width: 90%;
    text-align: center;
    animation: popupIn 0.3s ease-out forwards;
}

.popup-content p {
    font-size: 18px;
    color: var(--text);
    background: var(--alert);
    padding: 8px;
    border-radius: 5px;
    margin-bottom: 15px;
    line-height: 2;
    font-weight: 600;
}

.alert-caution::before {
    content: '공지';
    color: var(--alert-text);
    font-weight: 700;
}

.popup-checkbox {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-bottom: 15px;
}

.popup-checkbox input {
    appearance: none;
    width: 18px;
    height: 18px;
    border: 2px solid var(--primary);
    border-radius: 3px;
    cursor: pointer;
	position: relative;
    transition: all 0.2s ease;
}

.popup-checkbox input:checked {
    background: var(--primary);
    border-color: var(--primary);
}

.popup-checkbox input:checked::after {
    content: '✔';
    color: white;
    font-size: 14px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.popup-checkbox label {
    font-size: 15px;
    color: var(--text);
    cursor: pointer;
    font-weight: 500;
}

.popup-close {
    padding: 10px 18px;
    background: linear-gradient(90deg, var(--primary), var(--hover));
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 500;
    transition: transform 0.2s, box-shadow 0.2s;
}

.popup-close:hover {
    transform: scale(1.03);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

@keyframes popupIn {
    from { opacity: 0; transform: scale(0.85); }
    to { opacity: 1; transform: scale(1); }
}

@media (max-width: 500px) {
    .popup-content {
        padding: 20px;
        max-width: 300px;
    }

    .popup-content p {
        font-size: 16px;
        padding: 6px;
        margin-bottom: 12px;
    }

    .popup-checkbox input {
        width: 16px;
        height: 16px;
    }

    .popup-checkbox label {
        font-size: 14px;
    }

    .popup-close {
        padding: 8px 16px;
        font-size: 14px;
    }
}
</style>

<!-- Popup HTML -->
<div id="noticePopup" class="popup-overlay">
    <div class="popup-content">
        <p class="alert-caution"><br>미리보기는 참조용이며<br> 실제와 동일하지 않습니다.</p>
        <div class="popup-checkbox">
            <input type="checkbox" id="dontShowToday">
            <label for="dontShowToday">오늘 하루 안 보이기</label>
        </div>
        <button class="popup-close" onclick="closePopup()">닫기</button>
    </div>
</div>

<script>
function handlePopup() {
    const popup = document.getElementById('noticePopup');
    if (!popup) {
        console.error('Popup element not found');
        return;
    }

    const hideUntil = localStorage.getItem('hidePopupUntil');
    if (!hideUntil || Date.now() >= parseInt(hideUntil)) {
        popup.style.display = 'flex';
        console.log('Popup displayed');
    } else {
        console.log('Popup suppressed until:', new Date(parseInt(hideUntil)));
    }
}

function closePopup() {
    const popup = document.getElementById('noticePopup');
    const dontShow = document.getElementById('dontShowToday');

    if (dontShow.checked) {
        const now = new Date();
        const midnight = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 0, 0, 0);
        localStorage.setItem('hidePopupUntil', midnight.getTime());
        console.log('Popup hidden until:', midnight);
    }
    popup.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', handlePopup);
</script>