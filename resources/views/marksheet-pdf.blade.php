<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Academic Marksheet</title><style>
@page{size:A4;margin:0}
@font-face{font-family:HindSiliguri;src:url("file://{{ base_path('assets/fonts/HindSiliguri-Regular.ttf') }}") format("truetype");font-weight:400}
@font-face{font-family:HindSiliguri;src:url("file://{{ base_path('assets/fonts/HindSiliguri-Bold.ttf') }}") format("truetype");font-weight:700}
*{box-sizing:border-box}
body{margin:0;padding:0;font-family:HindSiliguri,DejaVu Sans,sans-serif;color:#111827;font-size:10.5pt}
.sheet{position:relative;width:210mm;height:297mm;padding:16mm 14mm 15mm;page-break-after:always;overflow:hidden}
.sheet:last-child{page-break-after:auto}
.sheet:before{content:"";position:absolute;inset:8.5mm;border:2px solid #1e3a8a;pointer-events:none}
.sheet:after{content:"";position:absolute;inset:10mm;border:.6px solid #1e3a8a;pointer-events:none}
.content{position:relative;z-index:1}
.logo{text-align:center;height:66px;margin-top:0}
.logo img{width:60px;height:60px;object-fit:contain}
.school-name{text-align:center;color:#1e3a8a;font-size:20pt;line-height:1.2;font-weight:700;margin:0}
.school-address{text-align:center;color:#4b5563;font-size:11pt;margin:2px 0 0}
.school-eiin{text-align:center;color:#4b5563;font-size:10pt;margin:1px 0 0}
.separator{border:0;border-top:.8px solid #9ca3af;margin:8px 0}
.title{text-align:center;font-size:15pt;font-weight:700;margin:0;color:#111827}
.exam{text-align:center;font-size:11.5pt;margin:1px 0 0}
.draft{text-align:center;color:#b91c1c;font-weight:700;font-size:10pt;margin:3px 0}
.info{width:100%;border-collapse:collapse;margin:10px 0 8px}
.info td{width:50%;padding:3px 5px;vertical-align:top;font-size:10.5pt}
.info .label{font-weight:700;color:#4b5563;width:88px;display:inline-block}
.info .value{color:#111827}
.marks{width:100%;border-collapse:collapse;table-layout:fixed}
.marks th,.marks td{border:.5px solid #9ca3af;padding:5px 5px;font-size:9.5pt;line-height:1.2}
.marks th{background:#e5e7eb;font-weight:700;text-align:center}
.marks th:first-child,.marks td:first-child{text-align:left;width:34%}
.marks th:nth-child(2),.marks td:nth-child(2){width:10%;text-align:center}
.marks th:nth-child(3),.marks td:nth-child(3){width:14%;text-align:center}
.marks th:nth-child(4),.marks td:nth-child(4){width:12%;text-align:center}
.marks th:nth-child(5),.marks td:nth-child(5){width:12%;text-align:center}
.marks th:nth-child(6),.marks td:nth-child(6){width:18%;text-align:center}
.summary{display:table;width:100%;table-layout:fixed;border-spacing:10px 0;margin:15px -10px 0}
.summary .box{display:table-cell;border:.8px solid #9ca3af;border-radius:4px;height:46px;text-align:center;padding:5px 3px}
.summary .label{display:block;color:#4b5563;font-size:8.5pt}
.summary .value{display:block;color:#111827;font-weight:700;font-size:14pt;line-height:1.2;margin-top:2px}
.summary .fail{color:#b91c1c}
.footer{position:absolute;left:14mm;right:14mm;bottom:25mm;height:36mm}
.qr{position:absolute;left:0;top:0;width:29mm;text-align:center;color:#4b5563;font-size:8pt}
.qr img{width:25mm;height:25mm;display:block;margin:0 auto 2px}
.signature{position:absolute;right:0;top:0;width:45mm;text-align:center}
.signature img{width:34mm;height:16mm;object-fit:contain;display:block;margin:0 auto}
.signature .line{border-top:.8px solid #111827;margin-top:1mm;padding-top:1mm;font-weight:700;font-size:10pt}
.note{position:absolute;bottom:9mm;left:14mm;right:14mm;text-align:center;color:#4b5563;font-size:7.5pt}
</style></head><body>@include('marksheet-content')</body></html>