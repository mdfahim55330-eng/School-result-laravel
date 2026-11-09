<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex">
<title>Result verified</title>
<link rel="stylesheet" href="/css/app.css">
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
<main class="mx-auto flex min-h-screen max-w-lg items-center px-4 py-10">
<div class="w-full overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-slate-200">
  <div class="bg-emerald-600 px-6 py-5 text-center text-white">
    <div class="mx-auto mb-2 flex h-12 w-12 items-center justify-center rounded-full bg-white/20 text-2xl font-bold">&#10003;</div>
    <h1 class="text-xl font-bold">Result Verified</h1>
    <p class="mt-1 text-sm text-emerald-50">This result matches the official school records.</p>
  </div>
  <div class="px-6 py-5">
    <p class="mb-3 text-center text-sm font-semibold text-slate-600">{{ $data['school']['name'] }}</p>
    <dl>
      <div class="flex justify-between gap-4 border-b border-slate-100 py-2.5 text-sm"><dt class="text-slate-500">Student</dt><dd class="text-right font-semibold text-slate-900">{{ $data['student']['name'] }}</dd></div>
      <div class="flex justify-between gap-4 border-b border-slate-100 py-2.5 text-sm"><dt class="text-slate-500">Roll</dt><dd class="text-right font-semibold text-slate-900">{{ $data['student']['roll'] }}</dd></div>
      <div class="flex justify-between gap-4 border-b border-slate-100 py-2.5 text-sm"><dt class="text-slate-500">Class</dt><dd class="text-right font-semibold text-slate-900">{{ $data['student']['class_name'] }}</dd></div>
      <div class="flex justify-between gap-4 border-b border-slate-100 py-2.5 text-sm"><dt class="text-slate-500">Examination</dt><dd class="text-right font-semibold text-slate-900">{{ $data['student']['exam_name'] }} {{ $data['student']['exam_year'] }}</dd></div>
      <div class="flex justify-between gap-4 border-b border-slate-100 py-2.5 text-sm"><dt class="text-slate-500">GPA</dt><dd class="text-right font-semibold text-slate-900">{{ number_format((float)$data['summary']['gpa'],2) }}</dd></div>
      <div class="flex justify-between gap-4 border-b border-slate-100 py-2.5 text-sm"><dt class="text-slate-500">Result</dt><dd class="text-right font-semibold text-slate-900">{{ $data['summary']['result'] }}</dd></div>
      <div class="flex justify-between gap-4 border-b border-slate-100 py-2.5 text-sm"><dt class="text-slate-500">Position</dt><dd class="text-right font-semibold text-slate-900">{{ $data['summary']['position'] }}</dd></div>
    </dl>
    <p class="mt-4 rounded-lg {{ strtolower($data['summary']['result'])==='fail'?'bg-red-50 text-red-700':'bg-emerald-50 text-emerald-700' }} px-3 py-2 text-center text-sm font-semibold">
      {{ strtolower($data['summary']['result'])==='fail'?'Not Passed':'Passed' }} &middot; Grade {{ $data['summary']['grade'] }}
    </p>
  </div>
</div>
</main>
</body>
</html>