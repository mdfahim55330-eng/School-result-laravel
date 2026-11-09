<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Results</title>
    <link rel="icon" href="/6716-removebg-preview.png?v=3">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<main id="main" data-title="All Results" class="hidden space-y-6">
    <section class="card">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <div id="scope" class="grid gap-4 sm:col-span-2 sm:grid-cols-3 lg:col-span-3"></div>
            <div><label class="label" for="status">Status</label>
                <select id="status" class="input"><option value="">All</option><option value="published">Published</option><option value="draft">Draft</option></select></div>
            <div class="lg:col-span-2"><label class="label" for="search">Search</label>
                <input id="search" class="input" placeholder="Name, roll or registration" autocomplete="off"></div>
        </div>
    </section>

    <section id="bulk" class="card hidden">
        <div class="flex flex-wrap items-center gap-2">
            <p class="mr-2 text-sm font-semibold text-slate-700" id="bulk-title"></p>
            <span data-admin class="contents">
                <button id="pub-all" class="btn-success btn-sm">Publish all</button>
                <button id="unpub-all" class="btn-warn btn-sm">Unpublish all</button>
            </span>
            <a id="l-merit" class="btn-secondary btn-sm" href="#">Merit list</a>
            <a id="l-tab" class="btn-secondary btn-sm" href="#">Tabulation</a>
            <a id="l-stat" class="btn-secondary btn-sm" href="#">Statistics</a>
            <a id="l-pdf" class="btn-secondary btn-sm" href="#" target="_blank">All marksheets (PDF)</a>
            <span data-admin class="contents">
                <a id="l-csv" class="btn-secondary btn-sm" href="#">Export CSV</a>
                <a id="l-xlsx" class="btn-secondary btn-sm" href="#">Export Excel</a>
                <button id="del-all" class="btn-danger btn-sm ml-auto">Delete all</button>
            </span>
        </div>
    </section>

    <section class="card">
        <div class="mb-3 flex items-center justify-between hidden" id="pagination-bar">
            <p class="text-sm text-slate-500" id="count"></p>
            <div class="flex items-center gap-2 text-sm">
                <button id="prev" class="btn-secondary btn-sm">Prev</button>
                <span id="page-info" class="text-slate-500"></span>
                <button id="next" class="btn-secondary btn-sm">Next</button>
            </div>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th class="text-center">Position</th><th>Roll</th><th>Name</th><th>Class</th><th>Exam</th><th class="text-center">GPA</th><th class="text-center">Grade</th><th>Result</th><th>Status</th><th>Fail Subject</th><th class="text-right">Actions</th></tr></thead>
                <tbody id="rows"></tbody>
            </table>
        </div>
    </section>
</main>

<script src="/js/app.js"></script>
<script>
    const PAGE = 20000;
    let all = [], page = 1, scope = null, isAdmin = false, timer = null;

    function filtered() {
        const q = App.$("#search").value.trim().toLowerCase();
        return all.filter(function (r) {
            return !q || String(r.name).toLowerCase().indexOf(q) !== -1 || String(r.roll).toLowerCase().indexOf(q) !== -1 || String(r.registration || "").toLowerCase().indexOf(q) !== -1;
        });
    }
    function draw() {
        const list = filtered();
        const pages = Math.max(1, Math.ceil(list.length / PAGE));
        if (page > pages) page = pages;
        const slice = list.slice((page - 1) * PAGE, page * PAGE);
        App.$("#count").textContent = list.length + " result(s)";
        App.$("#page-info").textContent = "Page " + page + " / " + pages;
        App.$("#prev").disabled = page <= 1; App.$("#next").disabled = page >= pages;
        App.$("#rows").innerHTML = slice.map(function (r) {
            const draft = r.status !== "published";
            return '<tr><td class="text-center font-semibold">' + (r.position == null ? "-" : App.esc(r.position)) + '</td><td class="font-semibold">' + App.esc(r.roll) + '</td><td class="font-medium text-slate-900">' + App.esc(r.name) + "</td><td>" + App.esc(r.class_name) + "</td><td>" +
                App.esc(r.exam_name) + " " + App.esc(r.exam_year) + '</td><td class="text-center">' + App.fmtGpa(r.final_gpa) + '</td><td class="text-center">' + App.esc(r.final_grade) + "</td><td>" +
                App.resultBadge(r.result_status) + "</td><td>" + App.statusBadge(r.status) + '</td><td class="max-w-[220px] whitespace-normal font-medium text-red-600">' + App.esc(r.failed_subjects || "-") + '</td><td class="whitespace-nowrap text-right">' +
                '<button class="btn-secondary btn-sm" data-act="view" data-id="' + r.id + '">View</button> ' +
                '<a class="btn-secondary btn-sm" href="/pages/individual-result.html?edit=' + r.id + '">Edit</a> ' +
                (isAdmin ? (draft ? '<button class="btn-success btn-sm" data-act="publish" data-id="' + r.id + '">Publish</button> ' : '<button class="btn-warn btn-sm" data-act="unpublish" data-id="' + r.id + '">Unpublish</button> ') +
                    '<button class="btn-danger btn-sm" data-act="delete" data-id="' + r.id + '">Delete</button>' : "") + "</td></tr>";
        }).join("") || App.emptyRow(11, "No results found.");
    }

    async function load() {
        const p = new URLSearchParams();
        if (scope) { p.set("class_name", scope.class_name); p.set("exam_name", scope.exam_name); p.set("exam_year", scope.exam_year); }
        if (App.$("#status").value) p.set("status", App.$("#status").value);
        const r = await App.api("/api/admin/results?" + p.toString());
        all = r.ok ? r.data.results : [];
        page = 1;
        draw();
        const bulk = App.$("#bulk");
        if (scope) {
            const q = App.scopeQuery(scope);
            App.$("#bulk-title").textContent = scope.class_name + " - " + scope.exam_name + " " + scope.exam_year;
            const link = function (id, url) { const el = App.$(id); if (el) el.href = url; };   // admin-only links do not exist for teachers
            link("#l-merit", "/pages/merit-list.html?" + q); link("#l-tab", "/pages/tabulation.html?" + q); link("#l-stat", "/pages/statistics.html?" + q);
            link("#l-pdf", "/api/admin/reports/marksheets.pdf?" + q); link("#l-csv", "/api/admin/export/results.csv?" + q); link("#l-xlsx", "/api/admin/export/results.xlsx?" + q);
            bulk.classList.remove("hidden");
        } else bulk.classList.add("hidden");
    }

    async function bulkPublish(action) {
        const word = action === "publish" ? "Publish" : "Unpublish";
        if (!(await App.confirm(word + " all results", word + " every result of " + App.$("#bulk-title").textContent + "?", { confirmText: word }))) return;
        const r = await App.api("/api/admin/results/publish-bulk", { method: "POST", json: Object.assign({ action: action }, scope) });
        App.toast(r.data.message, r.ok ? "success" : "error");
        load();
    }

    async function view(id) {
        const r = await App.api("/api/admin/results/" + id);
        if (!r.ok) return App.toast(r.data.message || "Could not load.", "error");
        const s = r.data.student;
        const wrap = document.createElement("div");
        wrap.className = "no-print fixed inset-0 z-[90] flex items-center justify-center bg-slate-900/50 p-4";
        wrap.innerHTML = '<div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl"><div class="flex items-start justify-between gap-3"><div><h3 class="text-lg font-bold">' + App.esc(s.name) +
            '</h3><p class="text-sm text-slate-500">Roll ' + App.esc(s.roll) + " - " + App.esc(s.class_name) + " - " + App.esc(s.exam_name) + " " + App.esc(s.exam_year) + "</p></div>" +
            '<button class="rounded-lg p-1 text-slate-400 hover:bg-slate-100" data-close>' + App.icon("x") + '</button></div><div class="mt-4 flex gap-2">' + App.statusBadge(s.status) + App.resultBadge(s.result_status) +
            '<span class="badge-blue">GPA ' + App.fmtGpa(s.final_gpa) + "</span></div>" +
            '<div class="table-wrap mt-4"><table class="table"><thead><tr><th>Subject</th><th class="text-center">Full</th><th class="text-center">Marks</th><th class="text-center">Grade</th></tr></thead><tbody>' +
            r.data.results.map(function (x) { return "<tr><td>" + App.esc(x.subject_name) + '</td><td class="text-center">' + x.full_marks + '</td><td class="text-center font-semibold">' + x.marks + '</td><td class="text-center">' + App.esc(x.grade) + "</td></tr>"; }).join("") +
            "</tbody></table></div></div>";
        document.body.appendChild(wrap);
        wrap.addEventListener("click", function (e) { if (e.target === wrap || e.target.closest("[data-close]")) wrap.remove(); });
    }

    (async function () {
        const user = await App.shell("list");
        if (!user) return;
        isAdmin = user.role === "admin";
        if (!isAdmin) App.$$("[data-admin]").forEach(function (e) { e.remove(); });
        await App.scopePicker(App.$("#scope"), function (s) { scope = s; load(); });
        App.$("#status").onchange = load;
        App.$("#search").oninput = function () { clearTimeout(timer); timer = setTimeout(function () { page = 1; draw(); }, 200); };
        App.$("#prev").onclick = function () { page--; draw(); };
        App.$("#next").onclick = function () { page++; draw(); };
        App.$("#pub-all") && (App.$("#pub-all").onclick = function () { bulkPublish("publish"); });
        App.$("#unpub-all") && (App.$("#unpub-all").onclick = function () { bulkPublish("unpublish"); });
        App.$("#del-all") && (App.$("#del-all").onclick = async function () {
            if (!(await App.confirm("Delete ALL results", "This permanently deletes every result of " + App.$("#bulk-title").textContent + ". This cannot be undone.", { danger: true, confirmText: "Delete all" }))) return;
            const r = await App.api("/api/admin/results/bulk", { method: "DELETE", json: scope });
            App.toast(r.data.message, r.ok ? "success" : "error");
            load();
        });
        App.$("#rows").onclick = async function (e) {
            const b = e.target.closest("[data-act]");
            if (!b) return;
            const id = b.getAttribute("data-id"), act = b.getAttribute("data-act");
            if (act === "view") return view(id);
            if (act === "delete") {
                if (!(await App.confirm("Delete result", "Delete this student's result permanently?", { danger: true, confirmText: "Delete" }))) return;
                const r = await App.api("/api/admin/results/" + id, { method: "DELETE" });
                App.toast(r.data.message, r.ok ? "success" : "error");
            } else {
                const r = await App.api("/api/admin/results/" + id + "/" + act, { method: "PUT" });
                App.toast(r.data.message, r.ok ? "success" : "error");
            }
            load();
        };
    })();
</script>
</body>
</html>
