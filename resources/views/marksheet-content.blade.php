<section class="sheet"><div class="content">
<div class="logo"><img src="{{ public_path('6716-removebg-preview.png') }}" alt="School Logo"></div>
<h1 class="school-name">{{ $school['name'] }}</h1>
<p class="school-address">{{ $school['address'] }}</p>
@if(!empty($school['eiin']))<p class="school-eiin">EIIN: {{ $school['eiin'] }}</p>@endif
<hr class="separator">
<h2 class="title">ACADEMIC MARKSHEET</h2>
<p class="exam">{{ $student['exam_name'] }} - {{ $student['exam_year'] }}</p>
@if(($student['status'] ?? 'published') !== 'published')<p class="draft">DRAFT - NOT PUBLISHED YET</p>@endif
<table class="info"><tbody>
<tr><td><span class="label">Student Name:</span> <span class="value">{{ $student['name'] }}</span></td><td><span class="label">Roll:</span> <span class="value">{{ $student['roll'] }}</span></td></tr>
<tr><td><span class="label">Registration:</span> <span class="value">{{ $student['registration'] ?: '-' }}</span></td><td><span class="label">Class:</span> <span class="value">{{ $student['class_name'] }}</span></td></tr>
<tr><td><span class="label">Group:</span> <span class="value">{{ $student['group_name'] ?: '-' }}</span></td><td><span class="label">Year:</span> <span class="value">{{ $student['exam_year'] }}</span></td></tr>
</tbody></table>
<table class="marks"><thead><tr><th>Subject</th><th>Code</th><th>Full Marks</th><th>Marks</th><th>Grade</th><th>Grade Point</th></tr></thead><tbody>
@foreach($subjects as $s)<tr><td>{{ $s['subject_name'] }}</td><td>{{ $s['subject_code'] }}</td><td>{{ $s['full_marks'] }}</td><td>{{ $s['marks'] }}</td><td>{{ $s['grade'] }}</td><td>{{ number_format((float)$s['grade_point'],2) }}</td></tr>@endforeach
</tbody></table>
<div class="summary">
<div class="box"><span class="label">Total Marks</span><span class="value">{{ $summary['total_marks'] }} / {{ $summary['total_full_marks'] }}</span></div>
<div class="box"><span class="label">GPA</span><span class="value">{{ number_format((float)$summary['gpa'],2) }}</span></div>
<div class="box"><span class="label">Result</span><span class="value {{ strtolower($summary['result'])==='fail'?'fail':'' }}">{{ $summary['result'] }}</span></div>
<div class="box"><span class="label">Position</span><span class="value">{{ $summary['position'] ?? '-' }}</span></div>
</div></div>
<div class="footer">
<div class="qr">@if(!empty($qr))<img src="{{ $qr }}" alt="QR verification">@endif<span>Scan to verify this result</span></div>
<div class="signature"><img src="{{ public_path('head-signature.png') }}" alt="Head Teacher Signature"><div class="line">Head Teacher</div></div>
</div>
<p class="note">This is a computer generated marksheet. Its authenticity can be checked by scanning the QR code.</p>
</section>