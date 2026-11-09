<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Merit List</title>
    <link rel="icon" href="/6716-removebg-preview.png?v=3">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<main id="main" data-title="Merit List" class="hidden space-y-6">
    <section class="card no-print">
        <div class="flex flex-wrap items-end gap-4">
            <div id="scope" class="grid flex-1 gap-4 sm:grid-cols-3"></div>
            <button id="print" class="btn-primary" disabled>Print</button>
        </div>
    </section>
    <section id="empty" class="card text-center text-sm text-slate-400">Select a class, exam and year to see the merit list.</section>
    <section id="sheet" class="card hidden">
        <div class="mb-4 text-center">
            <h2 class="text-xl font-bold" id="school"></h2>
            <p class="font-semibold" id="title"></p>
            <p class="text-xs text-slate-400">Ranked by GPA, then total marks. Students who did not pass are not ranked.</p>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th class="text-center">Position</th><th>Roll</th><th>Name</th><th class="text-center">Total marks</th><th class="text-center">GPA</th><th class="text-center">Grade</th><th>Result</th><th class="no-print">Status</th></tr></thead>
                <tbody id="rows"></tbody>
            </table>
        </div>
    </section>
</main>
<script src="/js/app.js"></script>
<script>
    function medal(p) {
        if (p === null) return '<span class="text-slate-300">-</span>';
        const c = p === 1 ? "bg-amber-400 text-white" : p === 2 ? "bg-slate-400 text-white" : p === 3 ? "bg-orange-400 text-white" : "bg-slate-100 text-slate-700";
        return '<span class="inline-flex h-7 min-w-[1.75rem] items-center justify-center rounded-full px-2 text-xs font-bold ' + c + '">' + p + "</span>";
    }
    async function load(scope) {
        App.$("#print").disabled = !scope;
        App.$("#empty").classList.toggle("hidden", !!scope); App.$("#sheet").classList.toggle("hidden", !scope);
        if (!scope) return;
        const r = await App.api("/api/admin/reports/merit?" + App.scopeQuery(scope));
        if (!r.ok) return App.toast(r.data.message || "Could not load.", "error");
        App.$("#school").textContent = App.SCHOOL.name;
        App.$("#title").textContent = "Merit List - " + scope.class_name + " - " + scope.exam_name + " " + scope.exam_year;
        App.$("#rows").innerHTML = r.data.students.map(function (s) {
            return '<tr><td class="text-center">' + medal(s.position) + "</td><td>" + App.esc(s.roll) + '</td><td class="font-medium text-slate-900">' + App.esc(s.name) +
                '</td><td class="text-center">' + s.total_marks + '</td><td class="text-center font-semibold">' + App.fmtGpa(s.final_gpa) + '</td><td class="text-center">' + App.esc(s.final_grade) +
                "</td><td>" + App.resultBadge(s.result_status) + '</td><td class="no-print">' + App.statusBadge(s.status) + "</td></tr>";
        }).join("") || App.emptyRow(8, "No students found.");
    }
    (async function () {
        if (!(await App.shell("merit"))) return;
        App.$("#print").onclick = function () { window.print(); };
        await App.scopePicker(App.$("#scope"), load);
    })();
</script>
</body>
</html>
