<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Statistics</title>
    <link rel="icon" href="/6716-removebg-preview.png?v=3">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<main id="main" data-title="Result Statistics" class="hidden space-y-6">
    <section class="card no-print"><div id="scope" class="grid gap-4 sm:grid-cols-3"></div></section>
    <section id="empty" class="card text-center text-sm text-slate-400">Select a class, exam and year to see the statistics.</section>
    <div id="content" class="hidden space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" id="cards"></section>
        <div class="grid gap-6 lg:grid-cols-2">
            <section class="card"><h3 class="card-title">Grade distribution</h3><div id="grades" class="space-y-2.5"></div></section>
            <section class="card"><h3 class="card-title">Top students</h3><div id="toppers" class="space-y-2 text-sm"></div>
                <div id="groups" class="mt-5"></div></section>
        </div>
        <section class="card">
            <h3 class="card-title">Subject-wise performance</h3>
            <div class="table-wrap"><table class="table">
                <thead><tr><th>Subject</th><th class="text-center">Appeared</th><th class="text-center">Passed</th><th class="text-center">Failed</th><th class="w-48">Pass rate</th><th class="text-center">A+</th><th class="text-center">Highest</th><th class="text-center">Lowest</th><th class="text-center">Average</th></tr></thead>
                <tbody id="subjects"></tbody></table></div>
        </section>
    </div>
</main>
<script src="/js/app.js"></script>
<script>
    function card(label, value, sub, tone) {
        return '<div class="card"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">' + label + '</p><p class="mt-1 text-3xl font-bold ' + tone + '">' + value + '</p><p class="text-xs text-slate-400">' + (sub || "&nbsp;") + "</p></div>";
    }
    function bar(pct, tone) {
        return '<div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full ' + tone + '" style="width:' + Math.max(0, Math.min(100, pct)) + '%"></div></div>';
    }
    async function load(scope) {
        App.$("#empty").classList.toggle("hidden", !!scope); App.$("#content").classList.toggle("hidden", !scope);
        if (!scope) return;
        const r = await App.api("/api/admin/reports/statistics?" + App.scopeQuery(scope));
        if (!r.ok) return App.toast(r.data.message || "Could not load.", "error");
        const s = r.data.summary, total = s.total_students || 1;
        App.$("#cards").innerHTML = card("Students", s.total_students, s.published + " published, " + s.draft + " draft", "text-slate-900") +
            card("Passed", s.passed, "Pass rate " + s.pass_rate + "%", "text-emerald-600") + card("Failed", s.failed, "", "text-red-600") +
            card("Average GPA", s.average_gpa.toFixed(2), "Highest " + s.highest_gpa.toFixed(2) + " - Lowest " + s.lowest_gpa.toFixed(2), "text-brand-700");
        const tones = { "A+": "bg-emerald-500", "A": "bg-emerald-400", "A-": "bg-teal-400", "B": "bg-sky-400", "C": "bg-amber-400", "D": "bg-orange-400", "F": "bg-red-500" };
        App.$("#grades").innerHTML = Object.keys(r.data.grades).map(function (g) {
            const n = r.data.grades[g];
            return '<div class="flex items-center gap-3"><span class="w-8 text-sm font-bold">' + g + '</span><div class="flex-1">' + bar((n / total) * 100, tones[g]) + '</div><span class="w-10 text-right text-sm text-slate-600">' + n + "</span></div>";
        }).join("");
        App.$("#toppers").innerHTML = s.toppers.length ? s.toppers.map(function (t) {
            return '<div class="flex items-center justify-between rounded-lg bg-amber-50 px-3 py-2"><span class="font-semibold">' + App.esc(t.name) + ' <span class="text-xs font-normal text-slate-500">Roll ' + App.esc(t.roll) + '</span></span><span class="badge-amber">GPA ' + t.gpa.toFixed(2) + "</span></div>";
        }).join("") : '<p class="text-slate-400">No ranked student yet.</p>';
        const g = r.data.groups;
        App.$("#groups").innerHTML = g.length > 1 ? '<h4 class="mb-2 text-sm font-bold">By group</h4>' + g.map(function (x) {
            return '<div class="flex justify-between border-b border-slate-100 py-1.5 text-sm"><span>' + App.esc(x.group_name || "No group") + "</span><span>" + x.passed + "/" + x.total + " passed (" + x.pass_rate + "%)</span></div>";
        }).join("") : "";
        App.$("#subjects").innerHTML = r.data.subjects.map(function (x) {
            return '<tr><td class="font-medium text-slate-900">' + App.esc(x.subject_name) + '</td><td class="text-center">' + x.appeared + '</td><td class="text-center">' + x.passed + '</td><td class="text-center ' + (x.failed ? "font-bold text-red-600" : "") + '">' + x.failed +
                '</td><td><div class="flex items-center gap-2"><div class="flex-1">' + bar(x.pass_rate, x.pass_rate >= 80 ? "bg-emerald-500" : x.pass_rate >= 50 ? "bg-amber-400" : "bg-red-500") + '</div><span class="w-12 text-right text-xs">' + x.pass_rate + '%</span></div></td><td class="text-center">' + x.a_plus +
                '</td><td class="text-center">' + x.highest + '</td><td class="text-center">' + x.lowest + '</td><td class="text-center">' + x.average + "</td></tr>";
        }).join("") || App.emptyRow(9, "No subjects.");
    }
    (async function () {
        if (!(await App.shell("statistics"))) return;
        await App.scopePicker(App.$("#scope"), load);
    })();
</script>
</body>
</html>
