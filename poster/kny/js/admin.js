$(() => {
    adminJsPkg.init();
});

const adminJsPkg = {
    init: function () {
        this.sidebarToggle();
    },
    sidebarToggle: function () {
        // 토글 버튼 클릭 이벤트
        $(".admin-sidebar__head-toggle").on("click", function () {
            const $sidebar = $("#admin-sidebar");
            const $main = $("#admin-main");

            // 클래스 토글 (CSS 스타일 적용)
            $sidebar.toggleClass("close");
            $main.toggleClass("nav-close");

            // 현재 상태를 로컬스토리지에 저장 (1: 닫힘, null: 열림)
            if ($sidebar.hasClass("close")) {
                localStorage.setItem("sidebar_close", "1");
            } else {
                localStorage.removeItem("sidebar_close");
            }
        });
    },
    PosterUpload: function () {
        // 포스터 업로드 관련 코드

        // ======= {{ 포스터 필드 추가 시작
        let fieldCount = 1;
        if (fieldCount <= 1) {
            $(".field-group__delete").hide();
        }
        $("#btn-add-field").on("click", function () {
            createField();
        });

        function createField() {
            if (fieldCount >= 6) {
                alert("필드는 최대 6개까지 추가할 수 있습니다.");
                return;
            }

            fieldCount++;
            const $table = $(".poster-write__field-table");
            const label = ["샘플(A)", "샘플(B)", "샘플(C)", "샘플(D)", "샘플(E)", "샘플(F)"];
            const selectedLabel = label[fieldCount] || "";

            const fieldHtml = `
                <tbody class="field-group">
                    <tr>
                        <th>라벨</th>
                        <td>
                            <select name="field${fieldCount}_label">
                                <option value="">선택</option>
                                <option value="샘플(A)"${selectedLabel === "샘플(A)" ? " selected" : ""}>샘플(A)</option>
                                <option value="샘플(B)"${selectedLabel === "샘플(B)" ? " selected" : ""}>샘플(B)</option>
                                <option value="샘플(C)"${selectedLabel === "샘플(C)" ? " selected" : ""}>샘플(C)</option>
                                <option value="샘플(D)"${selectedLabel === "샘플(D)" ? " selected" : ""}>샘플(D)</option>
                                <option value="샘플(E)"${selectedLabel === "샘플(E)" ? " selected" : ""}>샘플(E)</option>
                                <option value="샘플(F)"${selectedLabel === "샘플(F)" ? " selected" : ""}>샘플(F)</option>
                            </select>
                        </td>
                        <th>타입</th>
                        <td>
                            <select name="field${fieldCount}_type">
                                <option value="input">input</option>
                                <option value="textarea">textarea</option>
                            </select>
                        </td>
                        <th>최대길이</th>
                        <td><input type="number" name="field${fieldCount}_maxlength" placeholder="150"></td>
                        <th>필수</th>
                        <td><input type="checkbox" name="field${fieldCount}_required" value="1" checked></td>
                    </tr>
                    <tr>
                        <th>기본값</th>
                        <td colspan="7">
                            <textarea name="field${fieldCount}_default" placeholder="기본값 입력\n여러 줄 입력 가능"></textarea>
                            <button type="button" class="btn btn--small btn--cancel field-group__delete">삭제</button>
                        </td>
                    </tr>
                </tbody>
            `;

            $table.append(fieldHtml);

            if (fieldCount >= 6) {
                $("#btn-add-field").hide();
            }
            if (fieldCount > 1) {
                $(".field-group__delete").show();
            }
        }

        // 포스터 필드 삭제
        $(document).on("click", ".field-group__delete", function () {
            $(this).closest(".field-group").remove();
            fieldCount--;

            console.log(fieldCount);
            if (fieldCount < 6) {
                $("#btn-add-field").show();
            }
            if (fieldCount <= 1) {
                $(".field-group__delete").hide();
            }
        });

        // ======= 포스터 필드 추가 끝}}

        // ======= {{파일업로드 필드 추가 시작
        let requiredFileCount = 0;
        let designCount = 0;
        const typeNumber = $("#type_number").val();
        const $fileContainer = $(".poster-write__upload-file");

        // 디자인타입 늘어날 경우 파일 업로드도 늘어남
        $("#design").on("input", function () {
            createFileInputs();
        });

        function createFileInputs() {
            $fileContainer.empty();
            const designInput = $("#design").val().trim();

            if (designInput) {
                designCount = designInput.split(",").filter((d) => d.trim()).length;
            }

            // 필수 파일 개수 결정 (디자인 0~1개: 2개, 2개: 3개, 3개: 4개, 4개: 5개, 5개: 6개)
            if (designCount <= 1) {
                requiredFileCount = 2;
            } else if (designCount === 2) {
                requiredFileCount = 3;
            } else if (designCount === 3) {
                requiredFileCount = 4;
            } else if (designCount === 4) {
                requiredFileCount = 5;
            } else {
                requiredFileCount = 6;
            }

            for (let i = 0; i < requiredFileCount; i++) {
                const fileHtml = `
                    <div class="poster-write__upload-file-item">
                        <p>파일 ${String(i).padStart(2, "0")} - <strong>샘플 이미지 (필수)</strong></p>
                        <div class="poster-write__upload-file-item-input">
                            <input type="file" name="poster_file${String(i)}" class="poster-file-input" accept="image/*" required="">
                            <span>※ 저장될 파일명: ${typeNumber}_${String(i).padStart(2, "0")}.jpg</span>
                        </div>
                    </div>
                `;
                $fileContainer.append(fileHtml);
            }
        }

        // 이미지 업로드 시 유효성 검사
        $(".poster-file-input").on("change", function () {
            const file = this.files[0];
            const fileName = file.name;
            const fileExtension = fileName.split(".").pop().toLowerCase();
            const fileSize = file.size;
            const allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (!allowedExtensions.includes(fileExtension)) {
                alert("지원하지 않는 파일 형식입니다. (JPG, JPEG, PNG, GIF, WEBP만 가능)");
                this.value = "";
                return;
            }

            if (fileSize > maxSize) {
                alert("파일 크기가 2MB를 초과할 수 없습니다.");
                this.value = "";
                return;
            }
        });

        // ======= 파일업로드 필드 추가 끝}}

        // ======= {{ 폼 제출 시 유효성 검사 연결
        $("#poster-form").on("submit", function () {
            return validateForm();
        });
        function validateForm() {
            const type = $("#type").val();
            if (!type) {
                alert("타입을 선택해주세요.");
                return false;
            }

            // 필수 파일 개수 체크
            let uploadedFiles = 0;
            for (let i = 0; i < requiredFileCount; i++) {
                const fileInput = $(`input[name="poster_file${i}"]`);
                if (fileInput && fileInput[0].files.length > 0) {
                    uploadedFiles++;
                }
            }
            if (uploadedFiles < requiredFileCount) {
                let message = `필수 ${requiredFileCount}개의 이미지 파일을 모두 업로드해야 합니다.\n\n`;
                message += "- _00 파일: 샘플 이미지 (필수)\n";
                message += "- _01 파일: 입력 폼 이미지 (필수)\n";
                if (requiredFileCount > 2) {
                    for (let i = 2; i < requiredFileCount; i++) {
                        message += `- _${String(i).padStart(2, "0")} 파일: 디자인 ${i - 1} 이미지 (필수)\n`;
                    }
                }

                alert(message);
                return false;
            }

            return true;
        }
        // ======= 폼 제출 시 유효성 검사 연결 끝}}
    },
};
