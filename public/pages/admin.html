<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="icon" href="/6716-removebg-preview.png?v=3">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<main id="main" data-title="Dashboard" class="hidden space-y-6">
    <section class="rounded-2xl bg-gradient-to-r from-brand-800 to-brand-600 p-6 text-white shadow-soft">
        <h2 class="text-xl font-bold text-white">Welcome, <span id="username"></span></h2>
        <p class="mt-1 text-sm text-brand-100">Manage subjects, exams and results of Shaheed Nur Hossain Memorial School from here.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" id="stats"></section>

    <section>
        <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-500">Quick actions</h3>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4" id="actions"></div>
    </section>

    <section class="card">
        <h3 class="card-title">Exams with results</h3>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Class</th><th>Exam</th><th>Year</th><th class="text-center">Students</th><th class="text-center">Published</th><th class="text-right">Reports</th></tr></thead>
                <tbody id="exams"></tbody>
            </table>
        </div>
    </section>
</main>

<script src="/js/app.js"></script>
<script>
    const ACTIONS = [
        { href: "/pages/individual-result.html", icon: "plus", label: "Add a result", text: "Enter marks for one student" },
        { href: "/pages/bulk-result.html", icon: "upload", label: "Import from Excel", text: "Upload a whole class at once" },
        { href: "/pages/result-list.html", icon: "list", label: "All results", text: "Search, edit, publish" },
        { href: "/pages/merit-list.html", icon: "trophy", label: "Merit list", text: "Positions of every student" }
    ];
    function statCard(label, value, icon, tone) {
        return '<div class="card flex items-center gap-4"><div class="flex h-12 w-12 items-center justify-center rounded-xl ' + tone + '">' + App.icon(icon, "h-6 w-6") +
            '</div><div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">' + label + '</p><p class="text-3xl font-bold text-slate-900">' + value + "</p></div></div>";
    }
    (async function () {
        const user = await App.shell("dashboard");
        if (!user) return;
        App.$("#username").textContent = user.username;
        App.$("#actions").innerHTML = ACTIONS.map(function (a) {
            return '<a href="' + a.href + '" class="card group flex items-start gap-3 transition hover:-translate-y-0.5 hover:shadow-lg"><div class="rounded-lg bg-brand-50 p-2 text-brand-700">' +
                App.icon(a.icon) + '</div><div><p class="font-bold text-slate-900 group-hover:text-brand-700">' + a.label + '</p><p class="text-xs text-slate-500">' + a.text + "</p></div></a>";
        }).join("");

        const [stats, options] = await Promise.all([
            App.api("/api/admin/dashboard-stats"),
            App.api("/api/admin/reports/options")
        ]);
        const s = stats.data;
        App.$("#stats").innerHTML =
            statCard("Total students", s.totalStudents || 0, "users", "bg-brand-100 text-brand-700") +
            statCard("Results entered", s.totalResults || 0, "list", "bg-violet-100 text-violet-700") +
            statCard("Published", s.publishedResults || 0, "check", "bg-emerald-100 text-emerald-700") +
            statCard("Draft", s.draftResults || 0, "book", "bg-amber-100 text-amber-700");

        const rows = (options.data.options || []).map(function (o) {
            const q = App.scopeQuery(o);
            return "<tr><td>" + App.esc(o.class_name) + '</td><td class="font-medium text-slate-900">' + App.esc(o.exam_name) + "</td><td>" + App.esc(o.exam_year) +
                '</td><td class="text-center">' + o.students + '</td><td class="text-center">' +
                (o.published === o.students ? '<span class="badge-green">' + o.published + "</span>" : '<span class="badge-amber">' + o.published + " / " + o.students + "</span>") +
                '</td><td class="text-right whitespace-nowrap"><a class="text-brand-700 hover:underline" href="/pages/result-list.html?' + q + '">Results</a> &middot; ' +
                '<a class="text-brand-700 hover:underline" href="/pages/merit-list.html?' + q + '">Merit</a> &middot; ' +
                '<a class="text-brand-700 hover:underline" href="/pages/tabulation.html?' + q + '">Tabulation</a> &middot; ' +
                '<a class="text-brand-700 hover:underline" href="/pages/statistics.html?' + q + '">Stats</a></td></tr>';
        }).join("");
        App.$("#exams").innerHTML = rows || App.emptyRow(6, "No results yet. Add subjects and an exam, then add or import results.");
    })();
</script>
</body>
</html>
