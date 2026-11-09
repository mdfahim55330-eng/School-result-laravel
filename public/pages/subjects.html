<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subjects</title>
    <link rel="icon" href="/6716-removebg-preview.png?v=3">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<main id="main" data-title="Subjects" class="hidden space-y-6">
    <section id="add-card" class="card hidden">
        <h3 class="card-title">Add a subject</h3>
        <form id="form" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-1"><label class="label" for="class_name">Class</label><select id="class_name" class="input" required><option value="">Select</option></select></div>
            <div class="lg:col-span-2"><label class="label" for="subject_name">Subject name</label><input id="subject_name" class="input" maxlength="150" placeholder="e.g. Bangla" required></div>
            <div><label class="label" for="subject_code">Code</label><input id="subject_code" class="input" maxlength="30" placeholder="e.g. 101" required></div>
            <div><label class="label" for="full_marks">Full marks</label>
                <select id="full_marks" class="input" required><option value="">Select</option><option value="50">50</option><option value="100">100</option></select></div>
            <div><label class="label" for="subject_type">Type</label>
                <select id="subject_type" class="input"><option value="main">Main</option><option value="fourth">4th (optional)</option></select></div>
            <div class="sm:col-span-2 lg:col-span-6"><button class="btn-primary" type="submit">Add subject</button>
                <span class="ml-3 text-xs text-slate-400">A 4th subject only adds bonus points when the student gets more than 2.00.</span></div>
        </form>
    </section>

    <section class="card">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <h3 class="card-title !mb-0">Subject list</h3>
            <div class="w-48"><label class="label" for="filter">Show class</label><select id="filter" class="input"><option value="">All classes</option></select></div>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Class</th><th>Subject</th><th>Code</th><th class="text-center">Full marks</th><th>Type</th><th class="text-right" id="act-h">Action</th></tr></thead>
                <tbody id="rows"></tbody>
            </table>
        </div>
    </section>
</main>

<script src="/js/app.js"></script>
<script>
    let subjects = [], isAdmin = false;
    let CLASS_ITEMS = [];

    function render() {
        const f = App.$("#filter").value;
        const list = subjects.filter(function (s) { return !f || s.class_name === f; })
            .sort(function (a, b) { return App.classSort(a.class_name, b.class_name) || String(a.subject_code).localeCompare(String(b.subject_code), undefined, { numeric: true }); });
        App.$("#rows").innerHTML = list.map(function (s) {
            return "<tr><td>" + App.esc(s.class_name) + '</td><td class="font-medium text-slate-900">' + App.esc(s.subject_name) + "</td><td>" + App.esc(s.subject_code) +
                '</td><td class="text-center">' + App.esc(s.full_marks) + "</td><td>" +
                (String(s.subject_type).toLowerCase() === "fourth" ? '<span class="badge-amber">4th</span>' : '<span class="badge-gray">Main</span>') + "</td>" +
                '<td class="text-right">' + (isAdmin ? '<button class="btn-danger btn-sm" data-del="' + s.id + '">Delete</button>' : "") + "</td></tr>";
        }).join("") || App.emptyRow(6, "No subjects yet.");
    }
    async function load() {
        const r = await App.api("/api/admin/subjects");
        subjects = r.ok ? r.data.subjects : [];
        render();
    }
    async function remove(id) {
        if (!(await App.confirm("Delete subject", "Do you really want to delete this subject?", { danger: true, confirmText: "Delete" }))) return;
        let r = await App.api("/api/admin/subjects/" + id, { method: "DELETE" });
        if (r.status === 409 && r.data.needs_force) {
            if (!(await App.confirm("This subject has marks", r.data.message, { danger: true, confirmText: "Delete anyway" }))) return;
            r = await App.api("/api/admin/subjects/" + id + "?force=true", { method: "DELETE" });
        }
        App.toast(r.data.message || "Done.", r.ok ? "success" : "error");
        if (r.ok) load();
    }

    (async function () {
        const user = await App.shell("subjects");
        if (!user) return;
        isAdmin = user.role === "admin";
        CLASS_ITEMS = await App.loadClasses();
        App.fillSelect(App.$("#class_name"), CLASS_ITEMS, "Select");
        App.fillSelect(App.$("#filter"), CLASS_ITEMS, "All classes");
        if (isAdmin) App.$("#add-card").classList.remove("hidden"); else App.$("#act-h").textContent = "";
        App.$("#filter").onchange = render;
        App.$("#rows").onclick = function (e) { const b = e.target.closest("[data-del]"); if (b) remove(b.getAttribute("data-del")); };
        App.$("#form").onsubmit = async function (e) {
            e.preventDefault();
            const r = await App.api("/api/admin/subjects", { method: "POST", json: {
                class_name: App.$("#class_name").value, subject_name: App.$("#subject_name").value.trim(), subject_code: App.$("#subject_code").value.trim(),
                full_marks: App.$("#full_marks").value, subject_type: App.$("#subject_type").value } });
            App.toast(r.data.message, r.ok ? "success" : "error");
            if (r.ok) { App.$("#subject_name").value = ""; App.$("#subject_code").value = ""; App.$("#subject_type").value = "main"; App.$("#subject_name").focus(); load(); }
        };
        load();
    })();
</script>
</body>
</html>
