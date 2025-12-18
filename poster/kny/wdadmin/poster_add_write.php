<?php
include_once('./_common.php');
include_once('./admin.head.php');

// sql_escape_string 함수가 없는 경우를 대비한 헬퍼 함수
if (!function_exists('sql_escape_string')) {
    function sql_escape_string($str) {
        global $g5;
        if (isset($g5['connect_db'])) {
            return mysqli_real_escape_string($g5['connect_db'], $str);
        }
        return addslashes($str);
    }
}

// 다음 타입 번호 (wd_poster_data 테이블의 type 컬럼)
$row = sql_fetch(" select max(cast(type as unsigned)) as max_type from wd_poster_data ");
$next_type = (int)$row['max_type'] + 1;


// form 제출 처리
if($_POST['mode'] == "insert"){
    $type = sql_escape_string(trim($_POST['type']));
    $design = sql_escape_string(trim($_POST['design']));
    $poster_active = isset($_POST['poster_active']) ? intval($_POST['poster_active']) : 0;
    $poster_guest = isset($_POST['poster_guest']) ? intval($_POST['poster_guest']) : 0;

    // 파일 업로드 디렉토리
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/poster/kny/image/poster';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    
    // 파일 업로드 처리
    $uploaded_files = array();
    $upload_errors = array();
    
    for ($i = 0; $i < 10; $i++) {
        $file_key = 'poster_file' . $i;
        
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == UPLOAD_ERR_OK) {
            $file_tmp = $_FILES[$file_key]['tmp_name'];
            $file_name = $_FILES[$file_key]['name'];
            $file_size = $_FILES[$file_key]['size'];
            
            // 파일 확장자 확인
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');
            
            if (!in_array($file_ext, $allowed_ext)) {
                $upload_errors[] = "파일 {$i}: 허용되지 않는 파일 형식입니다. (jpg, jpeg, png, gif, webp만 가능)";
                continue;
            }
            
            // 파일 크기 체크 (10MB)
            if ($file_size > 10 * 1024 * 1024) {
                $upload_errors[] = "파일 {$i}: 파일 크기가 너무 큽니다. (최대 10MB)";
                continue;
            }
            
            // 새 파일명 생성: poster_타입명번호_순서.확장자
            $new_filename = $type . '_' . sprintf('%02d', $i) . '.' . $file_ext;
            $upload_path = $upload_dir . '/' . $new_filename;
            
            // 파일 업로드
            if (move_uploaded_file($file_tmp, $upload_path)) {
                chmod($upload_path, 0644);
                $uploaded_files[] = $new_filename;
            } else {
                $upload_errors[] = "파일 {$i}: 업로드 중 오류가 발생했습니다.";
            }
        }
    }

    
    // 업로드 에러가 있으면 표시
    if (!empty($upload_errors)) {
        $error_msg = implode("\\n", $upload_errors);
        alert($error_msg);
    }
    
    $files = implode(',', $uploaded_files);
    

    // poster_order 최대값 가져오기
    $max_order_row = sql_fetch("SELECT MAX(poster_order) as max_order FROM wd_poster_data");
    $poster_order = ($max_order_row['max_order'] > 0) ? $max_order_row['max_order'] + 1 : 1;

    
    $sql = "INSERT INTO wd_poster_data (type, design, poster_active, poster_guest, poster_order";
    $values = "VALUES ('$type', '$design', $poster_active, $poster_guest, $poster_order";
    
    
    // 필드 동적 처리 - 모든 값을 이스케이프 처리
    for ($i = 1; $i <= 6; $i++) {
        if (!empty($_POST['field' . $i . '_label'])) {
            $field_label = sql_escape_string(trim($_POST['field' . $i . '_label']));
            $field_type = sql_escape_string(trim($_POST['field' . $i . '_type']));
            $field_default = sql_escape_string($_POST['field' . $i . '_default']); // trim 제거 (줄바꿈 유지)
            $field_maxlength = !empty($_POST['field' . $i . '_maxlength']) ? intval($_POST['field' . $i . '_maxlength']) : 'NULL';
            $field_required = !empty($_POST['field' . $i . '_required']) ? 1 : 0;
            
            $sql .= ", field{$i}_label, field{$i}_type, field{$i}_default, field{$i}_maxlength, field{$i}_required";
            $values .= ", '$field_label'";
            $values .= ", '$field_type'";
            $values .= ", '$field_default'";
            $values .= ", $field_maxlength";
            $values .= ", $field_required";
        }
    }
    
    $sql .= ") " . $values . ")";
    sql_query($sql);
    
    $success_msg = '등록되었습니다.';
    if (!empty($uploaded_files)) {
        $success_msg .= "\\n\\n업로드된 파일: " . count($uploaded_files) . "개";
    }
    
    alert($success_msg, './poster_add_list.php');
    exit;
    
}

?>


<div class="admin-content poster-write">
    <h2 class="admin-content__title">포스터 데이터 등록</h2>
    <form method="post" enctype="multipart/form-data" id="poster-form">
        <input type="hidden" name="mode" value="insert">
        <input type="hidden" name="poster_active" value="0">

        <div class="poster-write__content poster-write__set">
            <h3 class="poster-write__content-title">포스터 정보</h3>
            <table class="poster-write__content-table poster-write__set-table">
                <colgroup>
                    <col width="80">
                    <col>
                </colgroup>
                <tr>
                    <th>타입</th>
                    <td><input type="text" name="type" id="type" value="<?php echo $next_type; ?>"></td>
                </tr>
                <tr>
                    <th>디자인</th>
                    <td>
                        <input type="text" name="design" id="design" placeholder="콤마로 구분: #3A7094,#4A9FC6,rgb(0,186,136)" >
                        <p class="help">※ 디자인 개수에 따라 필수 업로드 파일 수가 자동으로 조정됩니다.</p>
                    </td>
                </tr>
            </table>
        </div>


        <div class="poster-write__content poster-write__upload">
            <h3 class="poster-write__content-title">이미지 파일 업로드 (필수)</h3>
            <ul class="poster-write__upload-help">
                <li class="essential"><strong>※ 디자인 개수에 따라 필수 파일 수가 자동 설정됩니다.</strong></li>
                <li>※ <strong>_00 파일:</strong> 샘플 이미지 (미리보기용) - 필수</li>
                <li>※ <strong>_01 파일:</strong> 입력 폼에 사용할 이미지 - 필수</li>
                <li>※ <strong>_02~05 파일:</strong> 디자인별 이미지 (디자인 개수에 따라 필수)</li>
                <li>※ 타입을 먼저 선택하면 파일명이 자동으로 생성됩니다.</li>
                <li>※ 이미지 파일만 업로드 가능 (jpg, jpeg, png, gif, webp / 최대 2MB)</li>
                <li>※ 업로드 위치: /kny/image/poster/</li>
            </ul>
            <div id="file-container" class="poster-write__upload-file">
                <div class="poster-write__upload-file-item">
                    <p>파일 00 - <strong>샘플 이미지 (필수)</strong></p>
                    <div class="poster-write__upload-file-item-input">
                        <input type="file" name="poster_file0" class="poster-file-input" accept="image/*" required="">
                        <span>※ 저장될 파일명: <?php echo $next_type; ?>_00.jpg</span>
                    </div>
                </div>
                <div class="poster-write__upload-file-item">
                    <p>파일 01 - <strong>입력 폼 이미지 (필수)</strong></p>
                    <div class="poster-write__upload-file-item-input">
                        <input type="file" name="poster_file1" class="poster-file-input" accept="image/*" required="">
                        <span>※ 저장될 파일명: <?php echo $next_type; ?>_01.jpg</span>
                    </div>
                </div>
            </div>
            
        </div>


        <div class="poster-write__content poster-write__field">
            <h3 class="poster-write__content-title">필드 정보</h3>
            <table class="poster-write__content-table poster-write__field-table">
                <tbody class="field-group">
                    <tr>
                        <th>라벨</th>
                        <td>
                            <select name="field1_label">
                                <option value="">선택</option>
                                <option value="샘플(A)" selected>샘플(A)</option>
                                <option value="샘플(B)">샘플(B)</option>
                                <option value="샘플(C)">샘플(C)</option>
                                <option value="샘플(D)">샘플(D)</option>
                                <option value="샘플(E)">샘플(E)</option>
                                <option value="샘플(F)">샘플(F)</option>
                            </select>
                        </td>
                        <th>타입</th>
                        <td>
                            <select name="field1_type">
                                <option value="input">input</option>
                                <option value="textarea">textarea</option>
                            </select>
                        </td>
                        <th>최대길이</th>
                        <td><input type="number" name="field1_maxlength" placeholder="150"></td>
                        <th>필수</th>
                        <td><input type="checkbox" name="field1_required" value="1" checked></td>
                    </tr>
                    <tr>
                        <th>기본값</th>
                        <td colspan="7">
                            <textarea name="field1_default" placeholder="기본값 입력&#10;여러 줄 입력 가능"></textarea>
                            <button type="button" class="btn btn--small btn--cancel field-group__delete">삭제</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn--normal poster-write__field-add" id="btn-add-field">+ 필드 추가 (최대 6개)</button>
        </div>

            
            
        <div class="poster-write__btn">
            <a href="./poster_add_list.php" class="btn btn--big btn--gray">리스트보기</a>
            <button type="submit" class="btn btn--big btn--primary">등록하기</button>
        </div>
    </form>

</div>


<script>
    $(() => {
        adminJsPkg.PosterUpload();
    });
</script>

<?php include_once('./admin.tail.php'); ?>