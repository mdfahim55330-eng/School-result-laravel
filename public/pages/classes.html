<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Classes</title>
    <link rel="icon" href="/6716-removebg-preview.png?v=3">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<main id="main" data-title="Classes" class="hidden space-y-6">
    <section id="add-card" class="card hidden">
        <h3 class="card-title">Add a class</h3>
        <form id="form" class="grid gap-4 sm:grid-cols-[1fr_auto]">
            <div>
                <label class="label" for="class_name">Class name</label>
                <input id="class_name" class="input" maxlength="150"
                    placeholder="e.g. Play-1, One, Six, Nine, Ten" required>
            </div>
            <div class="flex items-end">
                <button class="btn-primary w-full sm:w-auto" type="submit">Add class</button>
            </div>
        </form>
    </section>

    <section class="card">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h3 class="card-title !mb-0">Class list</h3>
                <p class="mt-1 text-sm text-slate-500">Manage the classes used throughout the result system.</p>
            </div>
            <span id="count" class="badge-gray">0 classes</span>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th class="text-right" id="act-h">Action</th>
                    </tr>
                </thead>
                <tbody id="rows"></tbody>
            </table>
        </div>
    </section>
</main>

<script src="/js/app.js"></script>
<script>
    let isAdmin = false;
    let classes = [];

    function render() {
        classes = classes.slice().sort(function (a, b) {
            return App.classSort(a.class_name, b.class_name) ||
                String(a.class_name).localeCompare(String(b.class_name), undefined, { numeric: true });
        });

        App.$("#count").textContent = classes.length + (classes.length === 1 ? " class" : " classes");

        App.$("#rows").innerHTML = classes.map(function (item) {
            return '<tr>' +
                '<td class="font-medium text-slate-900">' + App.esc(item.class_name) + '</td>' +
                '<td class="text-right">' +
                (isAdmin
                    ? '<button class="btn-danger btn-sm" data-del="' + App.esc(item.id) + '">Delete</button>'
                    : '<span class="text-xs text-slate-400">Admin only</span>') +
                '</td></tr>';
        }).join("") || App.emptyRow(2, "No classes yet.");
    }

    async function load() {
        const r = await App.api("/api/admin/classes");
        classes = r.ok ? (r.data.classes || []) : [];
        render();
        if (!r.ok) App.toast(r.data.message || "Could not load classes.", "error");
    }

    async function removeClass(id) {
        const ok = await App.confirm(
            "Delete class",
            "Are you sure you want to delete this class? It cannot be deleted if it is already being used by other records.",
            { danger: true, confirmText: "Delete" }
        );
        if (!ok) return;

        const r = await App.api("/api/admin/classes/" + encodeURIComponent(id), { method: "DELETE" });
        App.toast(r.data.message || "Delete operation completed.", r.ok ? "success" : "error");
        if (r.ok) load();
    }

    (async function () {
        const user = await App.shell("classes");
        if (!user) return;

        isAdmin = user.role === "admin";
        if (isAdmin) App.$("#add-card").classList.remove("hidden");
        else App.$("#act-h").textContent = "";

        App.$("#rows").onclick = function (e) {
            const button = e.target.closest("[data-del]");
            if (button) removeClass(button.getAttribute("data-del"));
        };

        App.$("#form").onsubmit = async function (e) {
            e.preventDefault();

            const input = App.$("#class_name");
            const className = input.value.trim();
            if (!className) return;

            const r = await App.api("/api/admin/classes", {
                method: "POST",
                json: { class_name: className }
            });

            App.toast(r.data.message || "Class operation completed.", r.ok ? "success" : "error");

            if (r.ok) {
                input.value = "";
                input.focus();
                load();
            }
        };

        load();
    })();
</script>
</body>
</html>
