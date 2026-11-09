<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tabulation Sheet</title>
    <link rel="icon" href="/6716-removebg-preview.png?v=3">
    <link rel="stylesheet" href="/css/app.css">
    <style>@page { size: A4 landscape; margin: 8mm; }</style>
</head>
<body>
<main id="main" data-title="Tabulation Sheet" class="hidden space-y-6">
    <section class="card no-print">
        <div class="flex flex-wrap items-end gap-4">
            <div id="scope" class="grid flex-1 gap-4 sm:grid-cols-3"></div>
            <button id="print" class="btn-primary" disabled>Print (landscape)</button>
        </div>
    </section>
    <section id="empty" class="card text-center text-sm text-slate-400">Select a class, exam and year to see the tabulation sheet.</section>
    <section id="sheet" class="card hidden">
        <div class="mb-4 text-center">
            <h2 class="text-xl font-bold" id="school"></h2>
            <p class="text-sm text-slate-500" id="address"></p>
            <p class="font-semibold" id="title"></p>
        </div>
        <div class="table-wrap">
            <table class="table text-xs" id="tab"></table>
        </div>
    </section>
</main>
<script src="/js/app.js"></script>
<script>
    async function load(scope) {
        App.$("#print").disabled = !scope;
        App.$("#empty").classList.toggle("hidden", !!scope); App.$("#sheet").classList.toggle("hidden", !scope);
        if (!scope) return;
        const r = await App.api("/api/admin/reports/tabulation?" + App.scopeQuery(scope));
        if (!r.ok) return App.toast(r.data.message || "Could not load.", "error");
        const d = r.data;
        App.$("#school").textContent = d.school.name; App.$("#address").textContent = d.school.address;
        App.$("#title").textContent = "Tabulation Sheet - " + scope.class_name + " - " + scope.exam_name + " " + scope.exam_year;
        const head = "<thead><tr><th>Pos.</th><th>Roll</th><th>Name</th>" + d.subjects.map(function (s) {
            return '<th class="text-center" title="' + App.esc(s.subject_name) + '">' + App.esc(s.subject_name) + '<br><span class="font-normal normal-case text-slate-400">' + s.full_marks + (String(s.subject_type).toLowerCase() === "fourth" ? " (4th)" : "") + "</span></th>";
        }).join("") + '<th class="text-center">Total</th><th class="text-center">GPA</th><th class="text-center">Grade</th><th>Result</th></tr></thead>';
        const body = "<tbody>" + (d.students.map(function (s) {
            return "<tr><td>" + (s.position === null ? "-" : s.position) + "</td><td>" + App.esc(s.roll) + '</td><td class="whitespace-nowrap font-medium text-slate-900">' + App.esc(s.name) + "</td>" +
                d.subjects.map(function (sub) {
                    const m = s.marks[sub.id], g = s.grades[sub.id];
                    return '<td class="text-center ' + (g === "F" ? "font-bold text-red-600" : "") + '">' + (m === undefined ? "-" : m) + "</td>";
                }).join("") + '<td class="text-center font-semibold">' + s.total_marks + '</td><td class="text-center font-semibold">' + App.fmtGpa(s.final_gpa) + '</td><td class="text-center">' + App.esc(s.final_grade) +
                "</td><td>" + App.esc(s.result_status) + "</td></tr>";
        }).join("") || App.emptyRow(d.subjects.length + 7, "No students found.")) + "</tbody>";
        App.$("#tab").innerHTML = head + body;
    }
    (async function () {
        if (!(await App.shell("tabulation"))) return;
        App.$("#print").onclick = function () { window.print(); };
        await App.scopePicker(App.$("#scope"), load);
    })();
</script>
</body>
</html>
