<?php
include_once('./_common.php');

// mb_level 9만 접근 가능
if (!$member['mb_id'] || $member['mb_level'] < 9) {
    alert('접근 권한이 없습니다.');
}

// 페이지 설정
$bo_table = 'poster_save';
$page_rows = isset($_GET['rows']) ? (int)$_GET['rows'] : 10;

// 검색 조건
$sca = isset($_GET['sca']) ? clean_xss_tags($_GET['sca']) : '';
$sfl = isset($_GET['sfl']) ? clean_xss_tags($_GET['sfl']) : '';
$stx = isset($_GET['stx']) ? clean_xss_tags($_GET['stx']) : '';

// 필터 조건
$filter_design = isset($_GET['filter_design']) ? clean_xss_tags($_GET['filter_design']) : '';
$filter_status = isset($_GET['filter_status']) ? clean_xss_tags($_GET['filter_status']) : '';
$filter_name = isset($_GET['filter_name']) ? clean_xss_tags($_GET['filter_name']) : '';
$filter_start_date = isset($_GET['filter_start_date']) ? clean_xss_tags($_GET['filter_start_date']) : '';
$filter_end_date = isset($_GET['filter_end_date']) ? clean_xss_tags($_GET['filter_end_date']) : '';

// WHERE 조건 구성
$sql_search = '';
if ($sca) {
    $sql_search .= " AND ca_name = '{$sca}' ";
}
if ($stx && $sfl) {
    switch ($sfl) {
        case 'wr_subject':
            $sql_search .= " AND wr_subject LIKE '%{$stx}%' ";
            break;
        case 'wr_content':
            $sql_search .= " AND wr_content LIKE '%{$stx}%' ";
            break;
        case 'mb_id':
            $sql_search .= " AND mb_id LIKE '%{$stx}%' ";
            break;
        case 'wr_name':
            $sql_search .= " AND wr_name LIKE '%{$stx}%' ";
            break;
    }
}

// 필터 조건 추가
if ($filter_design) {
    $sql_search .= " AND wr_7 = '{$filter_design}' ";
}
if ($filter_status) {
    if ($filter_status === '접수') {
        $sql_search .= " AND (wr_10 IS NULL OR wr_10 = '' OR wr_10 = '접수') ";
    } else {
        $sql_search .= " AND wr_10 = '{$filter_status}' ";
    }
}
if ($filter_name) {
    $sql_search .= " AND wr_name LIKE '%{$filter_name}%' ";
}
if ($filter_start_date && $filter_end_date) {
    $sql_search .= " AND DATE(wr_datetime) BETWEEN '{$filter_start_date}' AND '{$filter_end_date}' ";
}

// 데이터 조회
$sql = "SELECT * FROM {$g5['write_prefix']}poster_save 
        WHERE 1 {$sql_search}
        ORDER BY wr_datetime DESC, wr_id DESC";
$result = sql_query($sql);
$counter = sql_num_rows($result);
?>

<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>포스터 신청 관리 시스템 엑셀 출력</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
            margin: 0;
            padding: 20px;
        }
        #wrap {
            width: 95%;
            min-width: 1400px; 
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border-radius: 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .controls {
            text-align: center;
            margin-bottom: 20px;
        }
        #wrap button {
            font-size: 14px;
            margin-bottom: 10px;
            padding: 12px 24px;
            background: #059669;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        #wrap button:hover {
            background: #047857;
            transform: translateY(-1px);
        }
        .stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 6px;
        }
        .stat-item {
            text-align: center;
            padding: 10px 15px;
            background: white;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }
        .stat-label {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
        }
        #another_table {
            border-collapse: collapse;
            width: 100%;
            margin: 0 auto;
            text-align: center;
            font-size: 12px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        #another_table th, #another_table td {
            border: 1px solid #e2e8f0;
            padding: 8px 6px;
        }
        #another_table th {
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            color: #1e293b;
            height: 35px;
            font-weight: 600;
            font-size: 11px;
        }
        #another_table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        #another_table tbody tr:hover {
            background: #e2e8f0;
        }
        .caption {
            margin-bottom: 15px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }
        .preview-controls {
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            background: #f8fafc;
            border-radius: 6px;
        }
        .preview-controls button {
            font-size: 12px !important;
            padding: 8px 16px !important;
            background: #3b82f6 !important;
            margin: 0 5px;
        }
        .preview-controls button:hover {
            background: #2563eb !important;
        }
        .preview-info {
            color: #64748b;
            font-size: 12px;
            margin: 5px 0;
        }
        .design-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            color: white;
        }
        .design-color-1 { background: #1e40af; }
        .design-color-2 { background: #059669; }  
        .design-color-3 { background: #d97706; }
        .design-color-4 { background: #dc2626; }
        .design-color-5 { background: #7c2d12; }
        .design-color-6 { background: #581c87; }
        .design-color-7 { background: #0f766e; }
        .design-color-8 { background: #365314; }
        .design-color-9 { background: #9a3412; }
        .design-color-10 { background: #374151; }
    </style>
</head>
<body>
    <div id="wrap">
        <div class="header">
            <h1><i class="fas fa-image"></i> 포스터 신청 관리 시스템 - 엑셀 출력</h1>
        </div>
        
        <div class="stats">
            <div class="stat-item">
                <div class="stat-label">전체 건수</div>
                <div class="stat-value"><?php echo number_format($counter); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-label">출력 일시</div>
                <div class="stat-value"><?php echo date('Y-m-d H:i'); ?></div>
            </div>
        </div>
        
        <div class="controls">
            <button onclick='downloadExcel()'>
                <i class='fas fa-file-excel'></i> 엑셀 다운로드
            </button>
        </div>
        
        <div class="preview-controls" style="display: none;">
            <button onclick="showAll()">
                <i class="fas fa-eye"></i> 모두보기 (<?php echo $counter; ?>개)
            </button>
            <button onclick="showPreview()">
                <i class="fas fa-eye-slash"></i> 미리보기 (30개)
            </button>
            <div class="preview-info">
                <i class="fas fa-info-circle"></i> 
                미리보기 모드: 처음 30개 항목만 표시됩니다. (엑셀 다운로드는 전체 데이터가 포함됩니다)
            </div>
        </div>
        
        <table id="another_table">
            <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col">신청일시</th>
                    <th scope="col">디자인</th>
                    <th scope="col">제목</th>
                    <th scope="col">ID</th>
                    <th scope="col">신청자</th>
                    <th scope="col">약국명</th>
                    <th scope="col">상태</th>
                    <th scope="col">내용</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $row_num = $counter;
                while ($row = sql_fetch_array($result)) {
                    // 디자인 정보 처리
                    $design_letter = !empty($row['wr_7']) ? strtoupper($row['wr_7']) : 'A';
                    $design_number = !empty($row['wr_1']) ? str_replace('design', '', $row['wr_1']) : '1';
                    $design_display = $design_letter . "-" . $design_number;
                    
                    // 색상 클래스 결정
                    $color_index = ((ord($design_letter) - ord('A')) % 10) + 1;
                    $design_color_class = 'design-color-' . $color_index;
                    
                    // 상태 처리
                    $status = empty($row['wr_10']) ? '접수' : $row['wr_10'];
                    
                    // 회원 정보 조회
                    $member_info = get_member($row['mb_id']);
                    $mb_name = $member_info['mb_name'];
                    
                    // HTML 디코딩
                    $subject = html_entity_decode(stripslashes($row['wr_subject']), ENT_QUOTES, 'UTF-8');
                    $content = html_entity_decode(stripslashes($row['wr_content']), ENT_QUOTES, 'UTF-8');
                    $pharm_name = html_entity_decode(stripslashes($row['wr_name']), ENT_QUOTES, 'UTF-8');
            ?>
            <tr>
                <td><?=$row_num?></td>
                <td><?=$row['wr_datetime']?></td>
                <td>
                    <span class="design-badge <?=$design_color_class?>">
                        <?=$design_display?>
                    </span>
                </td>
                <td style="text-align:left;"><?=$subject?></td>
                <td><?=$row['mb_id']?></td>
                <td><?=$mb_name?></td>
                <td style="text-align:left;"><?=$pharm_name?></td>
                <td><?=$status?></td>
                <td style="text-align:left;"><?=$content?></td>
            </tr>
            <?php
                $row_num--;
                }
            ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
async function downloadExcel() {
    const table = document.getElementById('another_table');
    const data = [];
    const header = [];

    // 테이블 헤더 가져오기
    const headerRow = table.tHead.rows[0];
    for (let j = 0; j < headerRow.cells.length; j++) {
        header.push(headerRow.cells[j].innerText);
    }

// 강력한 데이터 정제 함수
function sanitizeForExcel(text) {
    if (text === null || text === undefined || text === '') {
        return '';
    }
    
    // 문자열로 변환
    text = String(text);
    
    // HTML 태그 제거
    text = text.replace(/<[^>]*>/g, '');
    
    // HTML 엔티티 디코딩
    const entities = {
        '&amp;': '&',
        '&lt;': '<',
        '&gt;': '>',
        '&quot;': '"',
        '&#39;': "'",
        '&apos;': "'",
        '&nbsp;': ' '
    };
    
    for (const entity in entities) {
        text = text.replace(new RegExp(entity, 'g'), entities[entity]);
    }
    
    // 제어 문자 완전 제거 (0x00-0x1F, 0x7F-0x9F)
    text = text.replace(/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/g, '');
    
    // 특수 유니코드 문자 제거 (이모지, 특수기호 등)
    text = text.replace(/[\u{1F600}-\u{1F64F}]/gu, '') // 이모티콘
               .replace(/[\u{1F300}-\u{1F5FF}]/gu, '') // 기타 기호
               .replace(/[\u{1F680}-\u{1F6FF}]/gu, '') // 교통 기호
               .replace(/[\u{1F1E0}-\u{1F1FF}]/gu, '') // 국기
               .replace(/[\u{2600}-\u{26FF}]/gu, '')   // 기타 기호
               .replace(/[\u{2700}-\u{27BF}]/gu, '');  // 딩뱃
    
    // XML에서 문제될 수 있는 문자들 처리
    text = text.replace(/&/g, 'and')
               .replace(/</g, '')
               .replace(/>/g, '')
               .replace(/"/g, '')
               .replace(/'/g, '');
    
    // 연속된 공백을 하나로 통일
    text = text.replace(/\s+/g, ' ');
    
    // 앞뒤 공백 제거
    text = text.trim();
    
    // 길이 제한 (Excel 셀 최대 길이: 32767)
    if (text.length > 1000) {
        text = text.substring(0, 1000) + '...';
    }
    
    return text;
}

// 테이블 본문 데이터 가져오기
for (let i = 0; i < table.tBodies[0].rows.length; i++) {
    const row = table.tBodies[0].rows[i];
    const rowData = [];
    for (let j = 0; j < row.cells.length; j++) {
        let cellValue = '';
        
        try {
            if (j === 2) { // 디자인 컬럼
                const badge = row.cells[j].querySelector('.design-badge');
                cellValue = badge ? badge.textContent || badge.innerText || '' : '';
            } else {
                cellValue = row.cells[j].textContent || row.cells[j].innerText || '';
            }
            
            // 강력한 정제 적용
            cellValue = sanitizeForExcel(cellValue);
            
        } catch (error) {
            console.warn('Cell processing error:', error);
            cellValue = '';
        }
        
        rowData.push(cellValue);
    }
    data.push(rowData);
}

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('포스터신청');

    // 헤더 추가
    worksheet.columns = header.map((h, i) => ({
        header: h,
        key: h,
        width: getColumnWidth(i)
    }));

// 데이터 추가 (안전한 방식으로 변경)
data.forEach((row, rowIndex) => {
    try {
        const safeRow = row.map(cell => {
            // 각 셀을 다시 한번 정제
            let safeCell = sanitizeForExcel(cell);
            
            // 숫자인지 확인하고 적절히 처리
            if (/^\d+$/.test(safeCell)) {
                return parseInt(safeCell, 10);
            }
            
            return safeCell || ''; // null/undefined 방지
        });
        
        worksheet.addRow(safeRow);
    } catch (error) {
        console.warn(`Row ${rowIndex} processing error:`, error);
        // 오류 발생 시 빈 행 추가
        worksheet.addRow(new Array(header.length).fill(''));
    }
});

    // 헤더 스타일 설정
    worksheet.getRow(1).eachCell(cell => {
        cell.fill = {
            type: 'pattern',
            pattern: 'solid',
            fgColor: { argb: 'FFE2E8F0' }
        };
        cell.font = {
            color: { argb: 'FF1E293B' },
            bold: true,
            name: '맑은 고딕',
            size: 11
        };
        cell.alignment = {
            vertical: 'middle',
            horizontal: 'center'
        };
        cell.border = {
            top: {style:'thin'},
            left: {style:'thin'},
            bottom: {style:'thin'},
            right: {style:'thin'}
        };
    });

    // 데이터 행 스타일 설정
    worksheet.eachRow((row, rowNumber) => {
        if (rowNumber > 1) {
            row.eachCell((cell, colNumber) => {
                cell.font = {
                    name: '맑은 고딕',
                    size: 10
                };
                cell.border = {
                    top: {style:'thin'},
                    left: {style:'thin'},
                    bottom: {style:'thin'},
                    right: {style:'thin'}
                };

                // 텍스트 정렬 설정 (제목, 약국명, 내용은 좌측 정렬)
                if ([4, 7, 9].includes(colNumber)) { // 제목, 약국명, 내용
                    cell.alignment = { vertical: 'middle', horizontal: 'left' };
                } else {
                    cell.alignment = { vertical: 'middle', horizontal: 'center' };
                }

                // 짝수 행 배경색
                if (rowNumber % 2 === 0) {
                    cell.fill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: { argb: 'FFF8FAFC' }
                    };
                }
            });
        }
    });

    // 컬럼 너비 조정 함수
    function getColumnWidth(colIndex) {
        const widths = [6, 18, 8, 25, 12, 12, 20, 10, 30];
        return widths[colIndex] || 12;
    }

    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    
    // 파일명 생성
    const now = new Date();
    const dateStr = now.getFullYear() + 
                   String(now.getMonth() + 1).padStart(2, '0') + 
                   String(now.getDate()).padStart(2, '0');
    const filename = `포스터신청_${dateStr}.xlsx`;
    
    saveAs(blob, filename);
}

// 미리보기 제어 함수들
function showPreview() {
    const rows = document.querySelectorAll('#another_table tbody tr');
    const previewControls = document.querySelector('.preview-controls');
    
    rows.forEach((row, index) => {
        if (index >= 30) {
            row.style.display = 'none';
        }
    });
    
    previewControls.style.display = 'block';
}

function showAll() {
    const rows = document.querySelectorAll('#another_table tbody tr');
    const previewControls = document.querySelector('.preview-controls');
    
    rows.forEach(row => {
        row.style.display = '';
    });
    
    previewControls.style.display = 'none';
}

// 페이지 로드 시 미리보기 모드로 시작
window.addEventListener('load', function() {
    const totalRows = document.querySelectorAll('#another_table tbody tr').length;
    if (totalRows > 30) {
        showPreview();
    }
});
</script>