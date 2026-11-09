<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users &amp; Activity</title>
    <link rel="icon" href="/6716-removebg-preview.png?v=3">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<main id="main" data-title="Users &amp; Activity" class="hidden space-y-6">
    <div class="flex gap-1 rounded-xl bg-slate-200/70 p-1 text-sm font-semibold" id="tabs">
        <button data-tab="users" class="flex-1 rounded-lg bg-white px-3 py-2 shadow-sm">Users</button>
        <button data-tab="audit" class="flex-1 rounded-lg px-3 py-2 text-slate-600">Activity log</button>
        <button data-tab="backup" class="flex-1 rounded-lg px-3 py-2 text-slate-600">Backup</button>
    </div>

    <section id="tab-users" class="space-y-6">
        <div class="card">
            <h3 class="card-title">Add a user</h3>
            <form id="user-form" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div><label class="label" for="u-name">Username</label><input id="u-name" class="input" maxlength="50" required autocomplete="off"></div>
                <div><label class="label" for="u-pass">Temporary password</label><input id="u-pass" type="text" class="input" minlength="8" required autocomplete="off"></div>
                <div><label class="label" for="u-role">Role</label><select id="u-role" class="input"><option value="teacher">Teacher</option><option value="admin">Admin</option></select></div>
                <div class="flex items-end"><button class="btn-primary w-full" type="submit">Create user</button></div>
            </form>
            <p class="mt-3 text-xs text-slate-400"><b>Teacher:</b> can add and edit draft results, import Excel (as draft) and view reports. <b>Admin:</b> can also publish, delete, manage subjects/exams/users, export and back up. New users must choose their own password at the first login.</p>
        </div>
        <div class="card">
            <h3 class="card-title">Users</h3>
            <div class="table-wrap"><table class="table">
                <thead><tr><th>Username</th><th>Role</th><th>Status</th><th>Last login</th><th class="text-right">Actions</th></tr></thead>
                <tbody id="users"></tbody></table></div>
        </div>
    </section>

    <section id="tab-audit" class="card hidden">
        <div class="mb-4 grid gap-3 sm:grid-cols-3">
            <select id="a-action" class="input"><option value="">All activity</option></select>
            <input id="a-q" class="input sm:col-span-2" placeholder="Search (e.g. a student name or a username)">
        </div>
        <div class="table-wrap"><table class="table">
            <thead><tr><th>When</th><th>Who</th><th>What happened</th></tr></thead>
            <tbody id="logs"></tbody></table></div>
        <div class="mt-3 flex items-center justify-between text-sm">
            <span id="a-count" class="text-slate-500"></span>
            <div class="flex gap-2"><button id="a-prev" class="btn-secondary btn-sm">Newer</button><button id="a-next" class="btn-secondary btn-sm">Older</button></div>
        </div>
    </section>

    <section id="tab-backup" class="card hidden">
        <h3 class="card-title">Backup</h3>
        <p class="text-sm text-slate-600">Download all subjects, exams, students and results as one file. Passwords are <b>not</b> included. Keep the file somewhere safe, for example on your computer and in an email to yourself.</p>
        <a href="/api/admin/backup" class="btn-primary mt-4">Download backup now</a>
        <p class="mt-4 text-xs text-slate-400">To restore a backup into an empty database run: <code class="rounded bg-slate-100 px-1.5 py-0.5">npm run restore -- backup-file.json</code></p>
    </section>
</main>

<script src="/js/app.js"></script>
<script>
    let me = null, offset = 0, total = 0, timer = null;
    const LIMIT = 30;

    function showTab(name) {
        App.$$("#tabs button").forEach(function (b) {
            const on = b.getAttribute("data-tab") === name;
            b.className = "flex-1 rounded-lg px-3 py-2 " + (on ? "bg-white shadow-sm" : "text-slate-600");
        });
        ["users", "audit", "backup"].forEach(function (t) { App.$("#tab-" + t).classList.toggle("hidden", t !== name); });
        if (name === "audit") loadAudit();
    }

    async function loadUsers() {
        const r = await App.api("/api/admin/users");
        App.$("#users").innerHTML = (r.ok ? r.data.users : []).map(function (u) {
            const self = u.id === me.id;
            return '<tr><td class="font-semibold text-slate-900">' + App.esc(u.username) + (self ? ' <span class="badge-gray ml-1">you</span>' : "") + '</td><td><span class="' + (u.role === "admin" ? "badge-blue" : "badge-gray") + '">' + (u.role === "admin" ? "Admin" : "Teacher") + "</span></td><td>" +
                (u.active ? '<span class="badge-green">Active</span>' : '<span class="badge-red">Disabled</span>') + (u.must_change_password ? ' <span class="badge-amber">must change password</span>' : "") + "</td><td>" + App.fmtDate(u.last_login) + '</td><td class="whitespace-nowrap text-right">' +
                (self ? "" : '<button class="btn-secondary btn-sm" data-act="role" data-id="' + u.id + '" data-role="' + (u.role === "admin" ? "teacher" : "admin") + '">Make ' + (u.role === "admin" ? "teacher" : "admin") + "</button> " +
                    '<button class="btn-secondary btn-sm" data-act="active" data-id="' + u.id + '" data-active="' + (u.active ? "0" : "1") + '">' + (u.active ? "Disable" : "Enable") + "</button> ") +
                '<button class="btn-secondary btn-sm" data-act="reset" data-id="' + u.id + '" data-name="' + App.esc(u.username) + '">Reset password</button></td></tr>';
        }).join("");
    }

    async function loadAudit() {
        const p = new URLSearchParams({ limit: LIMIT, offset: offset });
        if (App.$("#a-action").value) p.set("action", App.$("#a-action").value);
        if (App.$("#a-q").value.trim()) p.set("q", App.$("#a-q").value.trim());
        const r = await App.api("/api/admin/audit?" + p.toString());
        if (!r.ok) return;
        total = r.data.total;
        const sel = App.$("#a-action"), cur = sel.value;
        if (sel.options.length <= 1) App.fillSelect(sel, r.data.actions.map(function (a) { return { value: a, label: a.replace(/_/g, " ") }; }), "All activity");
        sel.value = cur;
        App.$("#logs").innerHTML = r.data.logs.map(function (l) {
            return '<tr><td class="whitespace-nowrap text-xs text-slate-500">' + App.fmtDate(l.created_at) + '</td><td class="whitespace-nowrap font-medium">' + App.esc(l.username || "-") + '</td><td>' +
                App.esc(l.summary || l.action) + ' <span class="badge-gray ml-1">' + App.esc(String(l.action).replace(/_/g, " ")) + "</span></td></tr>";
        }).join("") || App.emptyRow(3, "No activity found.");
        App.$("#a-count").textContent = total + " entries" + (total ? " - showing " + (offset + 1) + "-" + Math.min(offset + LIMIT, total) : "");
        App.$("#a-prev").disabled = offset === 0; App.$("#a-next").disabled = offset + LIMIT >= total;
    }

    (async function () {
        me = await App.shell("users");
        if (!me) return;
        const who = await App.api("/api/admin/users");
        const mine = (who.data.users || []).filter(function (u) { return u.username === me.username; })[0];
        me.id = mine ? mine.id : 0;
        await loadUsers();

        App.$("#tabs").onclick = function (e) { const b = e.target.closest("[data-tab]"); if (b) showTab(b.getAttribute("data-tab")); };
        App.$("#user-form").onsubmit = async function (e) {
            e.preventDefault();
            const r = await App.api("/api/admin/users", { method: "POST", json: { username: App.$("#u-name").value.trim(), password: App.$("#u-pass").value, role: App.$("#u-role").value } });
            App.toast(r.data.message, r.ok ? "success" : "error");
            if (r.ok) { App.$("#user-form").reset(); loadUsers(); }
        };
        App.$("#users").onclick = async function (e) {
            const b = e.target.closest("[data-act]");
            if (!b) return;
            const id = b.getAttribute("data-id"), act = b.getAttribute("data-act");
            let r;
            if (act === "reset") {
                const pw = window.prompt("Type a temporary password for " + b.getAttribute("data-name") + " (at least 8 characters).\nThey must change it at the next login.");
                if (!pw) return;
                r = await App.api("/api/admin/users/" + id + "/reset-password", { method: "POST", json: { new_password: pw } });
            } else {
                const body = act === "role" ? { role: b.getAttribute("data-role") } : { active: b.getAttribute("data-active") === "1" };
                if (!(await App.confirm("Change user", "Are you sure?", { confirmText: "Yes" }))) return;
                r = await App.api("/api/admin/users/" + id, { method: "PUT", json: body });
            }
            App.toast(r.data.message, r.ok ? "success" : "error");
            loadUsers();
        };
        App.$("#a-action").onchange = function () { offset = 0; loadAudit(); };
        App.$("#a-q").oninput = function () { clearTimeout(timer); timer = setTimeout(function () { offset = 0; loadAudit(); }, 250); };
        App.$("#a-prev").onclick = function () { offset = Math.max(0, offset - LIMIT); loadAudit(); };
        App.$("#a-next").onclick = function () { offset += LIMIT; loadAudit(); };
    })();
</script>
</body>
</html>
