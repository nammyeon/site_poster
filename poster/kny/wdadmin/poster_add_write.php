<?php
include_once('./_common.php');
include_once('./admin.head.php');

// 다음 타입 번호 (wd_poster_data 테이블의 type 컬럼)
$row = sql_fetch(" select max(type) as max_type from wd_poster_data ");
$next_type = (int)$row['max_type'] + 1;



?>


<div class="admin-content poster-write">
    <h2 class="admin-content__title">포스터 데이터 등록</h2>
    <form method="post" enctype="multipart/form-data">
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
                    <td><input type="text" name="type_name" id="type_name" value="<?php echo $next_type; ?>"></td>
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
                        <input type="file" name="poster_file0" accept="image/*" required="">
                        <span>※ 저장될 파일명: <?php echo $next_type; ?>_00.jpg</span>
                    </div>
                </div>
                <div class="poster-write__upload-file-item">
                    <p>파일 01 - <strong>입력 폼 이미지 (필수)</strong></p>
                    <div class="poster-write__upload-file-item-input">
                        <input type="file" name="poster_file1" accept="image/*" required="">
                        <span>※ 저장될 파일명: <?php echo $next_type; ?>_01.jpg</span>
                    </div>
                </div>
            </div>
            
        </div>


        <div class="poster-write__content poster-write__field">
            <h3 class="poster-write__content-title">필드 정보</h3>
            <table class="poster-write__content-table poster-write__field-table">
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
                    <td colspan="7"><textarea name="field1_default" placeholder="기본값 입력&#10;여러 줄 입력 가능"></textarea></td>
                </tr>
            </table>
            <button type="button" class="poster-write__field-add" id="btn-add-field">+ 필드 추가 (최대 6개)</button>
        </div>

            
            
        <div class="poster-write__btn">
            <a href="./poster_add_list.php" class="btn-list">리스트보기</a>
            <button type="submit" class="btn-submit">등록하기</button>
        </div>
    </form>

</div>


<?php include_once('./admin.tail.php'); ?>