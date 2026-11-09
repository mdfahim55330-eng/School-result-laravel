<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exams</title>
    <link rel="icon" href="/6716-removebg-preview.png?v=3">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<main id="main" data-title="Exams" class="hidden space-y-6">
    <section id="add-card" class="card hidden">
        <h3 class="card-title">Add an exam</h3>
        <form id="form" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div><label class="label" for="exam_name">Exam name</label><input id="exam_name" class="input" maxlength="150" placeholder="e.g. Annual Exam" required></div>
            <div><label class="label" for="exam_year">Year</label><input id="exam_year" type="number" min="2000" max="2100" class="input" required></div>
            <div><label class="label" for="class_name">Class</label><select id="class_name" class="input" required><option value="">Select</option></select></div>
            <div class="flex items-end"><button class="btn-primary w-full" type="submit">Add exam</button></div>
        </form>
    </section>

    <section class="card">
        <h3 class="card-title">Exam list</h3>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Exam</th><th>Class</th><th>Year</th><th class="text-right" id="act-h">Action</th></tr></thead>
                <tbody id="rows"></tbody>
            </table>
        </div>
    </section>
</main>

<script src="/js/app.js"></script>
<script>
    let isAdmin = false;
    async function load() {
        const r = await App.api("/api/admin/exams");
        const list = r.ok ? r.data.exams : [];
        App.$("#rows").innerHTML = list.map(function (x) {
            return '<tr><td class="font-medium text-slate-900">' + App.esc(x.exam_name) + "</td><td>" + App.esc(x.class_name) + "</td><td>" + App.esc(x.exam_year) + '</td><td class="text-right">' +
                (isAdmin ? '<button class="btn-danger btn-sm" data-del="' + x.id + '">Delete</button>' : "") + "</td></tr>";
        }).join("") || App.emptyRow(4, "No exams yet.");
    }
    (async function () {
        const user = await App.shell("exams");
        if (!user) return;
        isAdmin = user.role === "admin";
        App.fillSelect(App.$("#class_name"), await App.loadClasses(), "Select");
        App.$("#exam_year").value = new Date().getFullYear();
        if (isAdmin) App.$("#add-card").classList.remove("hidden"); else App.$("#act-h").textContent = "";
        App.$("#rows").onclick = async function (e) {
            const b = e.target.closest("[data-del]");
            if (!b) return;
            if (!(await App.confirm("Delete exam", "This removes the exam from the list. Results that were already saved are not deleted.", { danger: true, confirmText: "Delete" }))) return;
            const r = await App.api("/api/admin/exams/" + b.getAttribute("data-del"), { method: "DELETE" });
            App.toast(r.data.message, r.ok ? "success" : "error");
            load();
        };
        App.$("#form").onsubmit = async function (e) {
            e.preventDefault();
            const r = await App.api("/api/admin/exams", { method: "POST", json: {
                exam_name: App.$("#exam_name").value.trim(), exam_year: App.$("#exam_year").value, class_name: App.$("#class_name").value } });
            App.toast(r.data.message, r.ok ? "success" : "error");
            if (r.ok) { App.$("#exam_name").value = ""; load(); }
        };
        load();
    })();
</script>
</body>
</html>
