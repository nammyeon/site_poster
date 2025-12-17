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
};
