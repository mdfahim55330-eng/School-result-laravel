/* Shared helpers for every page. No libraries needed. */
(function () {
    "use strict";

    const App = (window.App = {});

    const CLASS_ORDER = ["Play-1", "Play-2", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten"];
    App.CLASS_ORDER = CLASS_ORDER;
    App.SCHOOL = { name: "Shaheed Nur Hossain Memorial School", address: "Biral, Dinajpur" };

    // ---------------------------------------------------------------- basics
    App.esc = function (value) {
        return String(value === null || value === undefined ? "" : value)
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
    };
    App.$ = function (selector, root) { return (root || document).querySelector(selector); };
    App.$$ = function (selector, root) { return Array.from((root || document).querySelectorAll(selector)); };

    App.classSort = function (a, b) {
        const ia = CLASS_ORDER.indexOf(a), ib = CLASS_ORDER.indexOf(b);
        if (ia === -1 && ib === -1) return String(a).localeCompare(String(b));
        if (ia === -1) return 1;
        if (ib === -1) return -1;
        return ia - ib;
    };

    App.fmtDate = function (value) {
        if (!value) return "-";
        const d = new Date(value);
        if (isNaN(d)) return "-";
        return d.toLocaleString("en-GB", {
            timeZone: "Asia/Dhaka", day: "2-digit", month: "short", year: "numeric",
            hour: "2-digit", minute: "2-digit", hour12: true
        });
    };
    App.fmtGpa = function (value) { const n = Number(value); return isFinite(n) ? n.toFixed(2) : "-"; };

    // ---------------------------------------------------------------- API
    App.api = async function (url, options) {
        options = options || {};
        const init = { method: options.method || "GET", credentials: "same-origin", headers: {} };
        if (options.json !== undefined) {
            init.headers["Content-Type"] = "application/json";
            init.body = JSON.stringify(options.json);
        } else if (options.form) {
            init.body = options.form;
        }
        let response;
        try {
            response = await fetch(url, init);
        } catch (error) {
            const message = "Server connection failed. Please check your internet and try again.";
            if (!options.quiet) App.toast(message, "error");
            return { ok: false, status: 0, data: { success: false, message } };
        }
        let data = {};
        try { data = await response.json(); } catch (e) { data = { success: false, message: "Unexpected server answer." }; }

        if (!options.noRedirect) {
            if (response.status === 401 && url.indexOf("/api/admin/") === 0) {
                window.location.href = "/pages/admin-login.html";
            } else if (response.status === 403 && data.code === "PASSWORD_CHANGE_REQUIRED") {
                window.location.href = "/pages/change-password.html";
            }
        }
        return { ok: response.ok && data.success !== false, status: response.status, data: data };
    };

    // ---------------------------------------------------------------- icons
    const ICONS = {
        home: "M2.25 12 12 3l9.75 9M4.5 9.75V21h5.25v-6h4.5v6h5.25V9.75",
        book: "M12 6.04A8.97 8.97 0 0 0 6 3.75c-1.05 0-2.06.18-3 .51v14.25A8.99 8.99 0 0 1 6 18c2.3 0 4.4.86 6 2.29m0-14.25a8.97 8.97 0 0 1 6-2.29c1.05 0 2.06.18 3 .51v14.25a8.99 8.99 0 0 0-3-.51c-2.3 0-4.4.86-6 2.29m0-14.25v14.25",
        calendar: "M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5",
        plus: "M12 4.5v15m7.5-7.5h-15",
        upload: "M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5",
        list: "M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm0 5.25h.007v.008H3.75V12Zm0 5.25h.007v.008H3.75v-.008Z",
        trophy: "M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0",
        table: "M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5",
        chart: "M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z",
        users: "M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z",
        logout: "M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75",
        menu: "M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5",
        x: "M6 18 18 6M6 6l12 12",
        search: "m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z",
        download: "M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3",
        print: "M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z",
        shield: "M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z",
        key: "M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z",
        check: "m4.5 12.75 6 6 9-13.5",
        external: "M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25",
        pdf: "M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
    };
    App.icon = function (name, cls) {
        return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="' +
            (cls || "h-5 w-5") + '" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="' + (ICONS[name] || "") + '"/></svg>';
    };

    // ---------------------------------------------------------------- toast + confirm
    App.toast = function (message, type) {
        let box = document.getElementById("toast-box");
        if (!box) {
            box = document.createElement("div");
            box.id = "toast-box";
            box.className = "no-print fixed right-4 top-4 z-[100] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-2";
            document.body.appendChild(box);
        }
        const styles = {
            success: "bg-emerald-600 text-white",
            error: "bg-red-600 text-white",
            info: "bg-slate-800 text-white"
        };
        const item = document.createElement("div");
        item.setAttribute("role", "status");
        item.className = "rounded-xl px-4 py-3 text-sm font-medium shadow-lg " + (styles[type] || styles.info);
        item.textContent = message;
        box.appendChild(item);
        setTimeout(function () { item.remove(); }, type === "error" ? 6000 : 3500);
    };

    App.confirm = function (title, message, options) {
        options = options || {};
        return new Promise(function (resolve) {
            const wrap = document.createElement("div");
            wrap.className = "no-print fixed inset-0 z-[90] flex items-center justify-center bg-slate-900/50 p-4";
            wrap.innerHTML =
                '<div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" role="dialog" aria-modal="true">' +
                '<h3 class="text-lg font-bold">' + App.esc(title) + "</h3>" +
                '<p class="mt-2 text-sm leading-relaxed text-slate-600">' + App.esc(message) + "</p>" +
                '<div class="mt-5 flex justify-end gap-2">' +
                '<button type="button" data-no class="btn-secondary">Cancel</button>' +
                '<button type="button" data-yes class="' + (options.danger ? "btn-danger" : "btn-primary") + '">' +
                App.esc(options.confirmText || "Confirm") + "</button></div></div>";
            document.body.appendChild(wrap);
            const done = function (value) { wrap.remove(); document.removeEventListener("keydown", onKey); resolve(value); };
            const onKey = function (e) { if (e.key === "Escape") done(false); };
            document.addEventListener("keydown", onKey);
            wrap.querySelector("[data-yes]").onclick = function () { done(true); };
            wrap.querySelector("[data-no]").onclick = function () { done(false); };
            wrap.addEventListener("click", function (e) { if (e.target === wrap) done(false); });
            wrap.querySelector("[data-yes]").focus();
        });
    };

    // ---------------------------------------------------------------- small html builders
    App.statusBadge = function (status) {
        return status === "published"
            ? '<span class="badge-green">Published</span>'
            : '<span class="badge-amber">Draft</span>';
    };
    App.resultBadge = function (result) {
        return String(result).toLowerCase() === "pass"
            ? '<span class="badge-green">Pass</span>'
            : '<span class="badge-red">Fail</span>';
    };
    App.emptyRow = function (cols, text) {
        return '<tr><td colspan="' + cols + '" class="px-3 py-10 text-center text-sm text-slate-400">' + App.esc(text) + "</td></tr>";
    };
    App.fillSelect = function (select, items, placeholder, keep) {
        const current = keep ? select.value : "";
        select.innerHTML = '<option value="">' + App.esc(placeholder) + "</option>" +
            items.map(function (i) { return '<option value="' + App.esc(i.value) + '">' + App.esc(i.label) + "</option>"; }).join("");
        if (current && items.some(function (i) { return String(i.value) === String(current); })) select.value = current;
    };

    // ---------------------------------------------------------------- staff layout
    const NAV = [
        { group: "Overview", items: [{ href: "/pages/admin.html", label: "Dashboard", icon: "home", key: "dashboard" }] },
        {
            group: "Setup", items: [
                { href: "/pages/classes.html", label: "Classes", icon: "table", key: "classes" },
                { href: "/pages/subjects.html", label: "Subjects", icon: "book", key: "subjects" },
                { href: "/pages/exams.html", label: "Exams", icon: "calendar", key: "exams" }
            ]
        },
        {
            group: "Results", items: [
                { href: "/pages/individual-result.html", label: "Add Result", icon: "plus", key: "individual" },
                { href: "/pages/bulk-result.html", label: "Excel Import", icon: "upload", key: "bulk" },
                { href: "/pages/result-list.html", label: "All Results", icon: "list", key: "list" }
            ]
        },
        {
            group: "Reports", items: [
                { href: "/pages/merit-list.html", label: "Merit List", icon: "trophy", key: "merit" },
                { href: "/pages/tabulation.html", label: "Tabulation Sheet", icon: "table", key: "tabulation" },
                { href: "/pages/statistics.html", label: "Statistics", icon: "chart", key: "statistics" }
            ]
        },
        { group: "System", admin: true, items: [{ href: "/pages/users.html", label: "Users & Activity", icon: "users", key: "users" }] }
    ];

    App.shell = async function (active) {
        const me = await App.api("/api/admin/me", { noRedirect: true });
        const user = me.data;
        if (!user.loggedIn) { window.location.href = "/pages/admin-login.html"; return null; }
        if (user.mustChangePassword) { window.location.href = "/pages/change-password.html"; return null; }
        App.user = user;

        const main = document.getElementById("main");
        const title = main.getAttribute("data-title") || document.title;
        const isAdmin = user.role === "admin";

        const nav = NAV.filter(function (g) { return !g.admin || isAdmin; }).map(function (g) {
            return '<p class="mb-1 mt-5 px-3 text-[11px] font-bold uppercase tracking-wider text-brand-300">' + g.group + "</p>" +
                g.items.map(function (i) {
                    return '<a href="' + i.href + '" class="nav-link ' + (i.key === active ? "nav-link-active" : "") + '">' +
                        App.icon(i.icon) + "<span>" + i.label + "</span></a>";
                }).join("");
        }).join("");

        const shell = document.createElement("div");
        shell.className = "min-h-screen lg:flex";
        shell.innerHTML =
            '<div id="nav-overlay" class="no-print fixed inset-0 z-30 hidden bg-slate-900/50 lg:hidden"></div>' +
            '<aside id="sidebar" class="no-print fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-brand-900 transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">' +
            '<div class="flex items-center gap-3 border-b border-white/10 px-4 py-4">' +
            '<img src="/6716-removebg-preview.png?v=3" alt="" class="h-12 w-12 rounded-full bg-white/90 object-contain p-0" onerror="this.style.display=\'none\'">' +
            '<div class="min-w-0"><p class="truncate text-sm font-bold leading-tight text-white">' + App.esc(App.SCHOOL.name) + '</p>' +
            '<p class="text-[11px] text-brand-200">Result Management</p></div></div>' +
            '<nav class="flex-1 overflow-y-auto px-3 pb-4">' + nav + "</nav>" +
            '<div class="border-t border-white/10 p-3">' +
            '<a href="/pages/result-search.html" target="_blank" class="nav-link">' + App.icon("external") + "<span>Public result page</span></a>" +
            '<a href="/pages/change-password.html" class="nav-link">' + App.icon("key") + "<span>Change password</span></a>" +
            '<button type="button" id="logout-btn" class="nav-link w-full text-left">' + App.icon("logout") + "<span>Logout</span></button></div></aside>" +
            '<div class="min-w-0 flex min-h-screen flex-1 flex-col">' +
            '<header class="no-print sticky top-0 z-20 flex h-14 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur">' +
            '<button type="button" id="menu-btn" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" aria-label="Open menu">' + App.icon("menu") + "</button>" +
            '<h1 class="truncate text-base font-bold text-slate-900">' + App.esc(title) + "</h1>" +
            '<div class="ml-auto flex items-center gap-2"><span class="hidden text-sm text-slate-500 sm:inline">' + App.esc(user.username) + "</span>" +
            '<span class="' + (isAdmin ? "badge-blue" : "badge-gray") + '">' + (isAdmin ? "Admin" : "Teacher") + "</span></div></header>" +
            '<div id="slot" class="mx-auto w-full max-w-7xl flex-1 p-4 sm:p-6"></div>' +
            '<footer class="no-print mt-auto border-t border-slate-200 bg-white px-4 py-4 text-center text-xs text-slate-500">Made with <span aria-label="love">❤️</span> by <span class="font-semibold text-slate-700">Mohammad Fahim</span></footer></div>';

        document.body.insertBefore(shell, document.body.firstChild);
        document.getElementById("slot").appendChild(main);
        main.classList.remove("hidden");

        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("nav-overlay");
        const toggle = function (open) {
            sidebar.classList.toggle("-translate-x-full", !open);
            overlay.classList.toggle("hidden", !open);
        };
        document.getElementById("menu-btn").onclick = function () { toggle(true); };
        overlay.onclick = function () { toggle(false); };
        document.getElementById("logout-btn").onclick = async function () {
            await App.api("/api/admin/logout", { method: "POST", noRedirect: true });
            window.location.href = "/pages/admin-login.html";
        };
        return user;
    };

    // ---------------------------------------------------------------- dynamic classes
    App.loadClasses = async function () {
        const r = await App.api("/api/admin/classes");
        if (!r.ok) return [];
        return (r.data.classes || []).map(function (x) {
            return { value: x.class_name, label: x.class_name };
        });
    };

    // ---------------------------------------------------------------- class / exam / year picker
    App.loadOptions = async function (url) {
        const res = await App.api(url || "/api/admin/reports/options");
        return res.ok ? res.data.options || [] : [];
    };

    /**
     * Three linked drop-downs. container = element to fill. onChange(scope|null) fires on every change.
     * Remembers the last choice in the address bar (?class_name=...&exam_name=...&exam_year=...).
     */
    App.scopePicker = async function (container, onChange, options) {
        options = options || {};
        const all = await App.loadOptions(options.url);
        container.innerHTML =
            '<div><label class="label" for="sp-class">Class</label><select id="sp-class" class="input"></select></div>' +
            '<div><label class="label" for="sp-exam">Exam</label><select id="sp-exam" class="input"></select></div>' +
            '<div><label class="label" for="sp-year">Year</label><select id="sp-year" class="input"></select></div>';
        const cls = container.querySelector("#sp-class");
        const exam = container.querySelector("#sp-exam");
        const year = container.querySelector("#sp-year");

        const classes = Array.from(new Set(all.map(function (o) { return o.class_name; }))).sort(App.classSort);
        App.fillSelect(cls, classes.map(function (c) { return { value: c, label: c }; }), "Select class");

        function refreshExams(keep) {
            const names = Array.from(new Set(all.filter(function (o) { return o.class_name === cls.value; }).map(function (o) { return o.exam_name; })));
            App.fillSelect(exam, names.map(function (n) { return { value: n, label: n }; }), "Select exam", keep);
            refreshYears(keep);
        }
        function refreshYears(keep) {
            const years = Array.from(new Set(all.filter(function (o) { return o.class_name === cls.value && o.exam_name === exam.value; })
                .map(function (o) { return o.exam_year; }))).sort(function (a, b) { return b - a; });
            App.fillSelect(year, years.map(function (y) { return { value: y, label: y }; }), "Select year", keep);
            if (!year.value && years.length === 1) year.value = years[0];
        }
        function fire() {
            const scope = cls.value && exam.value && year.value
                ? { class_name: cls.value, exam_name: exam.value, exam_year: year.value } : null;
            const url = new URL(window.location.href);
            ["class_name", "exam_name", "exam_year"].forEach(function (k) { url.searchParams.delete(k); });
            if (scope) Object.keys(scope).forEach(function (k) { url.searchParams.set(k, scope[k]); });
            window.history.replaceState(null, "", url);
            onChange(scope);
        }

        cls.onchange = function () { refreshExams(false); fire(); };
        exam.onchange = function () { refreshYears(false); fire(); };
        year.onchange = fire;

        const q = new URLSearchParams(window.location.search);
        if (q.get("class_name") && classes.indexOf(q.get("class_name")) !== -1) {
            cls.value = q.get("class_name");
            refreshExams(false);
            if (q.get("exam_name")) { exam.value = q.get("exam_name"); refreshYears(false); }
            if (q.get("exam_year")) year.value = q.get("exam_year");
        } else if (options.autoSelect && all.length) {
            const first = all[0];
            cls.value = first.class_name; refreshExams(false);
            exam.value = first.exam_name; refreshYears(false);
            year.value = first.exam_year;
        } else {
            refreshExams(false);
        }
        fire();
        return { classes: classes, options: all, value: function () { return cls.value && exam.value && year.value ? { class_name: cls.value, exam_name: exam.value, exam_year: year.value } : null; } };
    };

    App.scopeQuery = function (scope) {
        return "class_name=" + encodeURIComponent(scope.class_name) + "&exam_name=" + encodeURIComponent(scope.exam_name) +
            "&exam_year=" + encodeURIComponent(scope.exam_year);
    };
})();
