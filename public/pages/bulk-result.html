<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Excel Import</title>
    <link rel="icon" href="/6716-removebg-preview.png?v=3">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<main id="main" data-title="Import Results from Excel" class="hidden space-y-6">
    <section class="card">
        <h3 class="card-title">1. Choose the exam and the file</h3>
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <label class="label" for="exam">Exam</label>
                <select id="exam" class="input"><option value="">Select exam</option></select>
                <button id="template" type="button" class="btn-secondary btn-sm mt-3" disabled>Download Excel template</button>
                <p class="mt-2 text-xs text-slate-400">The template has one column for every subject of the class.</p>
            </div>
            <div class="lg:col-span-2">
                <label class="label" for="file">Excel file (.xlsx)</label>
                <label for="file" id="drop" class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center transition hover:border-brand-400 hover:bg-brand-50">
                    <span class="text-brand-700" id="up-icon"></span>
                    <span id="file-name" class="mt-2 text-sm font-semibold text-slate-700">Click to choose a file, or drop it here</span>
                    <span class="text-xs text-slate-400">Only the first sheet is read. Maximum 5 MB.</span>
                </label>
                <input id="file" type="file" accept=".xlsx" class="sr-only">
            </div>
        </div>
    </section>

    <section class="card">
        <h3 class="card-title">2. Options</h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="mode">If a roll already exists</label>
                <select id="mode" class="input">
                    <option value="skip">Skip it (keep the saved result)</option>
                    <option value="update">Replace it with the Excel data</option>
                </select>
            </div>
            <div id="publish-box">
                <label class="label" for="publish">After importing</label>
                <select id="publish" class="input">
                    <option value="true">Publish immediately</option>
                    <option value="false">Save as draft (publish later)</option>
                </select>
            </div>
        </div>
        <div class="mt-5 flex flex-wrap gap-3">
            <button id="check" type="button" class="btn-secondary" disabled>Check the file</button>
            <button id="import" type="button" class="btn-primary" disabled>Import results</button>
        </div>
    </section>

    <section id="preview" class="card hidden">
        <h3 class="card-title">Check result</h3>
        <div id="chips" class="mb-4 flex flex-wrap gap-2"></div>
        <div class="table-wrap max-h-[28rem] overflow-y-auto">
            <table class="table">
                <thead class="sticky top-0"><tr><th>Row</th><th>Name</th><th>Roll</th><th>Status</th><th>GPA</th><th>Problem</th></tr></thead>
                <tbody id="preview-rows"></tbody>
            </table>
        </div>
    </section>

    <section id="done" class="card hidden"></section>
</main>

<script src="/js/app.js"></script>
<script>
    let file = null, isAdmin = false;
    const $exam = App.$("#exam"), $check = App.$("#check"), $import = App.$("#import"), $tpl = App.$("#template");
    App.$("#up-icon").innerHTML = App.icon("upload", "h-8 w-8");

    function ready() {
        const ok = !!($exam.value && file);
        $check.disabled = !ok; $import.disabled = !ok;
        $tpl.disabled = !$exam.value;
    }
    function setFile(f) {
        file = f;
        App.$("#file-name").textContent = f ? f.name + " (" + Math.round(f.size / 1024) + " KB)" : "Click to choose a file, or drop it here";
        App.$("#preview").classList.add("hidden"); App.$("#done").classList.add("hidden");
        ready();
    }
    function formData() {
        const fd = new FormData();
        fd.append("exam_id", $exam.value);
        fd.append("duplicate_mode", App.$("#mode").value);
        fd.append("publish", App.$("#publish").value);
        fd.append("excelFile", file);
        return fd;
    }
    function chip(text, cls) { return '<span class="' + cls + '">' + text + "</span>"; }

    async function check() {
        $check.disabled = true; $check.textContent = "Checking...";
        const r = await App.api("/api/admin/bulk-preview", { method: "POST", form: formData() });
        $check.textContent = "Check the file"; ready();
        if (!r.ok) return App.toast(r.data.message || "Could not read the file.", "error");
        const c = r.data.counts;
        App.$("#chips").innerHTML = chip(c.total + " rows", "badge-gray") + chip(c.new + " new", "badge-green") +
            (c.update ? chip(c.update + " will be replaced", "badge-blue") : "") + (c.skipped ? chip(c.skipped + " already exist (skipped)", "badge-amber") : "") +
            (c.invalid ? chip(c.invalid + " with problems", "badge-red") : "");
        App.$("#preview-rows").innerHTML = r.data.analysis.map(function (x) {
            let status;
            if (!x.valid) status = x.state === "skip" ? '<span class="badge-amber">Skipped</span>' : '<span class="badge-red">Problem</span>';
            else status = x.state === "update" ? '<span class="badge-blue">Replace</span>' : '<span class="badge-green">New</span>';
            return "<tr><td>" + x.row + '</td><td class="font-medium text-slate-900">' + App.esc(x.name) + "</td><td>" + App.esc(x.roll) + "</td><td>" + status + "</td><td>" +
                (x.gpa === null ? "-" : App.fmtGpa(x.gpa) + " " + App.resultBadge(x.result)) + '</td><td class="text-xs text-red-600">' + App.esc(x.errors.join(" ")) + "</td></tr>";
        }).join("");
        App.$("#preview").classList.remove("hidden");
        App.$("#done").classList.add("hidden");
    }

    async function doImport() {
        if (!(await App.confirm("Import results", "Save the valid rows of this file? Rows with problems are skipped and listed afterwards.", { confirmText: "Import" }))) return;
        $import.disabled = true; $import.textContent = "Importing...";
        const r = await App.api("/api/admin/bulk-import", { method: "POST", form: formData() });
        $import.textContent = "Import results"; ready();
        if (!r.ok) return App.toast(r.data.message || "Import failed.", "error");
        const d = r.data, box = App.$("#done");
        box.innerHTML = '<h3 class="card-title">Import finished</h3><div class="flex flex-wrap gap-2">' +
            chip(d.processed + " saved", "badge-green") + (d.updated ? chip(d.updated + " replaced", "badge-blue") : "") +
            (d.failed ? chip(d.failed + " not imported", "badge-red") : "") + chip(d.published ? "Published" : "Draft", d.published ? "badge-green" : "badge-amber") + "</div>" +
            (d.errors.length ? '<div class="table-wrap mt-4 max-h-80 overflow-y-auto"><table class="table"><thead><tr><th>Row</th><th>Name</th><th>Roll</th><th>Problem</th></tr></thead><tbody>' +
                d.errors.map(function (e) { return "<tr><td>" + e.row + "</td><td>" + App.esc(e.name) + "</td><td>" + App.esc(e.roll) + '</td><td class="text-xs text-red-600">' + App.esc(e.error) + "</td></tr>"; }).join("") + "</tbody></table></div>" : "") +
            '<p class="mt-4"><a class="btn-primary" href="/pages/result-list.html">View results</a></p>';
        box.classList.remove("hidden");
        App.$("#preview").classList.add("hidden");
        box.scrollIntoView({ behavior: "smooth" });
    }

    (async function () {
        const user = await App.shell("bulk");
        if (!user) return;
        isAdmin = user.role === "admin";
        if (!isAdmin) { App.$("#publish").innerHTML = '<option value="false">Save as draft (an admin publishes later)</option>'; App.$("#publish").disabled = true; }
        const r = await App.api("/api/admin/exams");
        App.fillSelect($exam, (r.ok ? r.data.exams : []).map(function (e) { return { value: e.id, label: e.exam_name + " - " + e.class_name + " - " + e.exam_year }; }), "Select exam");
        $exam.onchange = function () { App.$("#preview").classList.add("hidden"); ready(); };
        App.$("#file").onchange = function () { setFile(this.files[0] || null); };
        const drop = App.$("#drop");
        ["dragover", "dragenter"].forEach(function (n) { drop.addEventListener(n, function (e) { e.preventDefault(); drop.classList.add("border-brand-400"); }); });
        drop.addEventListener("dragleave", function () { drop.classList.remove("border-brand-400"); });
        drop.addEventListener("drop", function (e) { e.preventDefault(); drop.classList.remove("border-brand-400"); if (e.dataTransfer.files[0]) setFile(e.dataTransfer.files[0]); });
        $check.onclick = check; $import.onclick = doImport;
        $tpl.onclick = function () { window.location.href = "/api/admin/download-excel-demo?exam_id=" + encodeURIComponent($exam.value); };
    })();
</script>
</body>
</html>
