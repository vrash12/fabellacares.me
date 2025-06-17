{{--resources/views/queue/print.blade.php--}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Token {{ $token->code }}</title>

<style>
/* ---------- 1.   physical size & zero margin --------------------------- */
@media print {
    @page { size: 76mm 130mm; margin: 0; }
}
html,body      { width: 76mm; margin:0; font-family: Arial, sans-serif; }
body           { display:flex; flex-direction:column; align-items:center;
                 justify-content:center; text-align:center; padding:8mm 4mm; }

/* ---------- 2.   simple branding -------------------------------------- */
.logo          { width:30mm; margin-bottom:6mm; }
.dept          { font-size:16pt; font-weight:bold; margin-bottom:3mm; }
.code          { font-size:46pt; font-weight:900; letter-spacing:2px;
                 margin:6mm 0; }
.time          { font-size:10pt; color:#555; }
hr             { width:100%; border:none; border-top:1px dashed #000;
                 margin:6mm 0; }
                 
    .patient {
      font-size: 14pt;
      font-weight: 600;
      margin: 3mm 0;
    }
</style>
</head>
<body onload="window.print(); setTimeout(()=>window.close(),400);">
  <img src="{{ asset('images/fabella-logo.png') }}" class="logo" alt="Logo">
  <div class="dept">{{ $token->queue->name }}</div>
  <hr>

  <div class="code">{{ $token->code }}</div>

  @if(! empty($patientName))
    <div class="patient">{{ $patientName }}</div>
  @endif

  <hr>
  <div class="time">{{ $timestamp }}</div>

</body>
</html>
