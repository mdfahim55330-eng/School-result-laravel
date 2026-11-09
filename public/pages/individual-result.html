<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Result</title>
    <link rel="icon" href="/6716-removebg-preview.png?v=3">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<main id="main" data-title="Add Result" class="hidden">
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <section class="card">
                <h3 class="card-title" id="student-title">Student information</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="label" for="exam">Exam</label>
                        <select id="exam" class="input"><option value="">Select exam</option></select>
                        <p id="exam-note" class="mt-1 hidden text-xs text-slate-500"></p>
                    </div>
                    <div><label class="label" for="name">Student name</label><input id="name" class="input" maxlength="200" required></div>
                    <div><label class="label" for="roll">Roll</label><input id="roll" class="input" maxlength="50" inputmode="numeric" required></div>
                    <div><label class="label" for="registration">Registration (optional)</label><input id="registration" class="input" maxlength="100"></div>
                    <div><label class="label" for="group">Group</label>
                        <select id="group" class="input"><option value="Science">Science</option><option value="Commerce">Commerce</option><option value="Arts">Arts</option><option value="">None</option></select></div>
                </div>
            </section>

            <section class="card">
                <h3 class="card-title">Subject marks</h3>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Subject</th><th>Code</th><th class="text-center">Full</th><th class="w-32">Marks</th><th class="text-center">Grade</th><th class="text-center">Point</th></tr></thead>
                        <tbody id="subjects"><tr><td colspan="6" class="px-3 py-10 text-center text-sm text-slate-400">Select an exam first.</td></tr></tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-slate-400">Marks are needed for every main subject. A 4th subject can be left empty if the student did not take it.</p>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="card xl:sticky xl:top-20">
                <h3 class="card-title">Live result</h3>
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-[11px] font-semibold uppercase text-slate-400">GPA</p><p id="p-gpa" class="text-2xl font-bold text-brand-700">-</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-[11px] font-semibold uppercase text-slate-400">Grade</p><p id="p-grade" class="text-2xl font-bold">-</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-[11px] font-semibold uppercase text-slate-400">Result</p><p id="p-result" class="text-2xl font-bold">-</p></div>
                </div>
                <p class="mt-3 text-xs text-slate-400">New results are saved as <b>draft</b>. An admin publishes them from the results list.</p>
                <div class="mt-5 flex gap-2">
                    <button id="save" type="button" class="btn-primary flex-1 !py-2.5">Save result</button>
                    <button id="clear" type="button" class="btn-secondary">Clear</button>
                </div>
                <a id="back" href="/pages/result-list.html" class="mt-4 hidden text-sm font-semibold text-brand-700 hover:underline">&larr; Back to results</a>
            </section>
        </aside>
    </div>
</main>

<script src="/js/app.js"></script>
<script>
    // same grading rules as the server (the server always recalculates)
    function gradeOf(marks, full) {
        const p = (marks / full) * 100;
        if (p >= 80) return { g: "A+", pt: 5 }; if (p >= 70) return { g: "A", pt: 4 }; if (p >= 60) return { g: "A-", pt: 3.5 };
        if (p >= 50) return { g: "B", pt: 3 }; if (p >= 40) return { g: "C", pt: 2 }; if (p >= 33) return { g: "D", pt: 1 };
        return { g: "F", pt: 0 };
    }
    function gpaGrade(g) { return g >= 5 ? "A+" : g >= 4 ? "A" : g >= 3.5 ? "A-" : g >= 3 ? "B" : g >= 2 ? "C" : g >= 1 ? "D" : "F"; }

    let exams = [], allSubjects = [], classSubjects = [], editId = null, currentClass = "";
    const params = new URLSearchParams(window.location.search);

    function isFourth(s) { return String(s.subject_type).toLowerCase() === "fourth"; }

    function renderSubjects(saved) {
        const body = App.$("#subjects");
        if (!classSubjects.length) { body.innerHTML = App.emptyRow(6, currentClass ? "No subjects found for " + currentClass + "." : "Select an exam first."); update(); return; }
        body.innerHTML = classSubjects.map(function (s) {
            const value = saved && saved[s.id] !== undefined ? saved[s.id] : "";
            return "<tr><td class=\"font-medium text-slate-900\">" + App.esc(s.subject_name) + (isFourth(s) ? ' <span class="badge-amber ml-1">4th</span>' : "") + "</td><td>" + App.esc(s.subject_code) +
                '</td><td class="text-center">' + App.esc(s.full_marks) + '</td><td><input type="number" min="0" max="' + s.full_marks + '" step="any" class="input marks" data-id="' + s.id +
                '" data-full="' + s.full_marks + '" value="' + App.esc(value) + '" inputmode="decimal"></td><td class="text-center" id="g-' + s.id + '">-</td><td class="text-center" id="pt-' + s.id + '">-</td></tr>';
        }).join("");
        update();
    }

    function update() {
        const rows = [];
        App.$$(".marks").forEach(function (input) {
            const id = input.getAttribute("data-id"), full = Number(input.getAttribute("data-full"));
            const gCell = App.$("#g-" + id), pCell = App.$("#pt-" + id);
            const v = input.value;
            const bad = v !== "" && (Number(v) < 0 || Number(v) > full);
            input.classList.toggle("!border-red-500", bad);
            if (v === "" || bad) { gCell.textContent = "-"; pCell.textContent = "-"; return; }
            const r = gradeOf(Number(v), full);
            gCell.innerHTML = '<span class="' + (r.g === "F" ? "badge-red" : "badge-blue") + '">' + r.g + "</span>";
            pCell.textContent = r.pt.toFixed(2);
            const s = classSubjects.filter(function (x) { return String(x.id) === id; })[0];
            rows.push({ g: r.g, pt: r.pt, fourth: s && isFourth(s) });
        });
        const main = rows.filter(function (r) { return !r.fourth; });
        const mainCount = classSubjects.filter(function (s) { return !isFourth(s); }).length;
        if (!mainCount || main.length < mainCount) { setPreview("-", "-", "-"); return; }
        if (main.some(function (r) { return r.g === "F"; })) { setPreview("0.00", "F", "Fail"); return; }
        let total = main.reduce(function (a, r) { return a + r.pt; }, 0);
        rows.filter(function (r) { return r.fourth && r.pt > 2; }).forEach(function (r) { total += r.pt - 2; });
        const gpa = Math.min(5, Math.round((total / main.length) * 100) / 100);
        setPreview(gpa.toFixed(2), gpaGrade(gpa), "Pass");
    }
    function setPreview(gpa, grade, result) {
        App.$("#p-gpa").textContent = gpa; App.$("#p-grade").textContent = grade;
        const r = App.$("#p-result"); r.textContent = result;
        r.className = "text-2xl font-bold " + (result === "Fail" ? "text-red-600" : result === "Pass" ? "text-emerald-600" : "");
    }

    function collect() {
        const results = [];
        App.$$(".marks").forEach(function (i) { if (i.value !== "") results.push({ subject_id: Number(i.getAttribute("data-id")), marks: Number(i.value) }); });
        return results;
    }

    async function save() {
        const name = App.$("#name").value.trim(), roll = App.$("#roll").value.trim();
        if (!editId && !App.$("#exam").value) return App.toast("Please select an exam.", "error");
        if (!name || !roll) return App.toast("Student name and roll are required.", "error");
        const results = collect();
        if (!results.length) return App.toast("Please enter the marks.", "error");
        const student = { name: name, roll: roll, registration: App.$("#registration").value.trim(), group_name: App.$("#group").value };

        const btn = App.$("#save");
        btn.disabled = true;
        const r = editId
            ? await App.api("/api/admin/results/" + editId, { method: "PUT", json: { student: student, results: results } })
            : await App.api("/api/admin/results/individual", { method: "POST", json: { exam_id: Number(App.$("#exam").value), student: student, results: results } });
        btn.disabled = false;
        App.toast(r.data.message || (r.ok ? "Saved." : "Could not save."), r.ok ? "success" : "error");
        if (!r.ok) return;
        if (editId) { setTimeout(function () { window.location.href = "/pages/result-list.html"; }, 800); return; }
        App.toast("GPA " + Number(r.data.gpa).toFixed(2) + " - " + r.data.result, "info");
        clearStudent();
    }

    function clearStudent() {
        ["#name", "#roll", "#registration"].forEach(function (s) { App.$(s).value = ""; });
        App.$$(".marks").forEach(function (i) { i.value = ""; });
        update();
        App.$("#roll").focus();
    }

    async function pickExam() {
        const exam = exams.filter(function (e) { return String(e.id) === App.$("#exam").value; })[0];
        currentClass = exam ? exam.class_name : "";
        classSubjects = exam ? allSubjects.filter(function (s) { return s.class_name === exam.class_name; }).sort(function (a, b) { return a.id - b.id; }) : [];
        renderSubjects();
    }

    (async function () {
        const user = await App.shell("individual");
        if (!user) return;
        const [ex, sb] = await Promise.all([App.api("/api/admin/exams"), App.api("/api/admin/subjects")]);
        exams = ex.ok ? ex.data.exams : []; allSubjects = sb.ok ? sb.data.subjects : [];
        App.fillSelect(App.$("#exam"), exams.map(function (e) { return { value: e.id, label: e.exam_name + " - " + e.class_name + " - " + e.exam_year }; }), "Select exam");
        App.$("#exam").onchange = pickExam;
        App.$("#subjects").addEventListener("input", update);
        App.$("#save").onclick = save;
        App.$("#clear").onclick = clearStudent;
        App.$("#subjects").addEventListener("keydown", function (e) {
            // Enter moves to the next marks box (fast typing), and saves on the last one
            if (e.key !== "Enter" || !e.target.classList.contains("marks")) return;
            e.preventDefault();
            const all = App.$$(".marks"), i = all.indexOf(e.target);
            if (i < all.length - 1) all[i + 1].focus(); else save();
        });

        if (params.get("edit")) {
            editId = Number(params.get("edit"));
            App.$("#main").setAttribute("data-title", "Edit Result");
            App.$("#student-title").textContent = "Edit student";
            App.$("#save").textContent = "Update result";
            App.$("#clear").classList.add("hidden");
            App.$("#back").classList.remove("hidden");
            App.$("#exam").disabled = true;
            const r = await App.api("/api/admin/results/" + editId);
            if (!r.ok) { App.toast(r.data.message || "Could not load the result.", "error"); return; }
            const s = r.data.student;
            App.$("#name").value = s.name || ""; App.$("#roll").value = s.roll || ""; App.$("#registration").value = s.registration || "";
            App.$("#group").value = s.group_name || "";
            const note = App.$("#exam-note");
            note.textContent = s.exam_name + " " + s.exam_year + " - " + s.class_name + " (the exam of an existing result cannot be changed)";
            note.classList.remove("hidden");
            const match = exams.filter(function (e) { return e.exam_name === s.exam_name && String(e.exam_year) === String(s.exam_year) && e.class_name === s.class_name; })[0];
            if (match) App.$("#exam").value = match.id;
            currentClass = s.class_name;
            classSubjects = allSubjects.filter(function (x) { return x.class_name === s.class_name; }).sort(function (a, b) { return a.id - b.id; });
            const saved = {};
            r.data.results.forEach(function (x) { saved[x.subject_id] = x.marks; });
            renderSubjects(saved);
        } else {
            renderSubjects();
        }
    })();
</script>
</body>
</html>
