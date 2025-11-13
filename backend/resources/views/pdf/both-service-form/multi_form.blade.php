@php
    $showJeeva = in_array('jeeva_access', $types ?? []) || !empty($selectedJeeva);
    $showWellsoft = in_array('wellsoft', $types ?? []) || !empty($selectedWellsoft);
    $showInternet = in_array('internet_access_request', $types ?? []) || !empty($internetPurposes);
    $fmtDate = function($d){ return $d ? (is_string($d) ? date('d/m/Y', strtotime($d)) : $d->format('d/m/Y')) : ''; };
    $checkbox = function($checked=false){ return '<span style="display:inline-block;width:12px;height:12px;border:1px solid #000;vertical-align:middle;margin:0 6px;'.($checked?'background:#000;':'').'"></span>'; };
@endphp
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Access Request {{ $r->id }}</title>
  <style>
    @page { margin: 20px 22px; }
    body { font-family: 'Times New Roman', Times, serif; font-size: 13px; color: #000; }
    .border { border: 1px solid #000; }
    .row { display: table; width: 100%; table-layout: fixed; }
    .col { display: table-cell; vertical-align: top; }
    .center { text-align: center; }
    .title { font-weight: 700; font-size: 14px; text-transform: none; }
    .subtitle { font-weight: 700; font-size: 13px; }
    .small { font-size: 12px; }
    .section-title { font-weight: 900; font-size: 13px; }
    .ital { font-style: italic; }
    .mt-2 { margin-top: 6px; }
    .mt-3 { margin-top: 10px; }
    .mt-4 { margin-top: 14px; }
    .mb-1 { margin-bottom: 4px; }
    .mb-2 { margin-bottom: 6px; }
    .mb-3 { margin-bottom: 10px; }
    .p-2 { padding: 8px; }
    .p-3 { padding: 12px; }
    .grid { display: table; width: 100%; table-layout: fixed; }
    .gcol { display: table-cell; vertical-align: top; padding-right: 8px; }
    .thin { border-collapse: collapse; }
    .thin td, .thin th { border: 1px solid #000; padding: 6px; vertical-align: top; }
    .label { width: 140px; font-weight: 700; }
    .page-break { page-break-after: always; }
    .footer { width:100%; margin-top:12px; font-weight:700; font-size: 11px; }
    .footer .left { float:left; }
    .footer .right { float:right; }
    /* Header */
    .hdr { width:100%; }
    .hdr .hcol { width:33.33%; }
    .hdr-title { font-weight:700; font-size:13px; text-align:center; }
    .hdr-sub { font-weight:700; font-size:12px; text-align:center; }
    .hdr-center-middle { vertical-align: middle !important; padding-bottom: 0; }
    /* Header separator lines */
    .rule { width:100%; }
    .rule-top { margin-top:0; border-top:1px solid #000; height:0; }
    .rule-heavy { margin-top:1px; height:4px; background:#000; }
    .rule-bottom { margin-top:1px; border-top:1px solid #000; height:0; }
    /* Checkbox styles */
    .cb { display:inline-block; width:14px; height:14px; border:1px solid #000; vertical-align:middle; margin:0 6px; text-align:center; line-height:14px; font-weight:700; font-size:12px; font-family: 'Times New Roman', Times, serif, 'DejaVu Sans', Arial, Helvetica, sans-serif; }
    .cb-checked { font-weight:900; font-size:16px; font-family: 'Times New Roman', Times, serif, 'DejaVu Sans', Arial, Helvetica, sans-serif; }
    .tick { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; font-weight:700; }
  </style>
</head>
<body>
  @if($showJeeva)
    <div class="border p-3">
      @include('pdf.both-service-form.shared_header')
      <div class="center">
        <div class="title">Jeeva Access Request Form</div>
      </div>

      <div class="row mb-2 mt-2">
        <div class="col"><span class="label" style="font-weight:400">PF Number:</span> <strong>{{ $r->pf_number }}</strong></div>
        <div class="col"><span class="label" style="font-weight:400">Staff Name:</span> <strong>{{ $r->staff_name }}</strong></div>
        <div class="col">
          <span class="label" style="font-weight:400">Signature:</span>
          @if(!empty($applicantSigShort)) <strong>{{ $applicantSigShort }} @if(!empty($applicantSignedAt)) ({{ $applicantSignedAt }}) @endif</strong> @else _____________ @endif
        </div>
      </div>

      <div class="row mb-2">
        <div class="col"><strong>Module Requested for:</strong></div>
      <div class="col">Use {!! $checkbox(($moduleRequestedFor==='use')) !!} @if($moduleRequestedFor==='use') <span class="tick">&#10003;</span> @endif</div>
      <div class="col">Revoke {!! $checkbox(($moduleRequestedFor==='revoke')) !!} @if($moduleRequestedFor==='revoke') <span class="tick">&#10003;</span> @endif</div>
      </div>

      <div class="mb-2 subtitle">Modules</div>
      @php $jeeCols=[[],[],[]]; foreach($jeevaModulesMaster as $i=>$m){ $jeeCols[$i%3][]=$m; } @endphp
      <div class="grid small">
        @foreach($jeeCols as $col)
          <div class="gcol">
            @foreach($col as $m)
              {!! $checkbox(in_array($m,$selectedJeeva??[])) !!} {{ $m }} @if(in_array($m,$selectedJeeva??[])) <span class="tick">&#10003;</span> @endif<br/>
            @endforeach
          </div>
        @endforeach
      </div>

      <div class="mt-3 section-title center">Please Specify the Access rights</div>
      <div class="p-2 border" style="min-height:32px; text-align:center;">{{ $hodComments }}</div>

      <div class="row mt-3 small">
        <div class="col">{!! $checkbox(($accessType==='permanent')) !!} Permanent (until retirement) @if($accessType==='permanent') <span class="tick">&#10003;</span> @endif</div>
        <div class="col">{!! $checkbox(($accessType==='temporary')) !!} Temporary: Start ______ End ______ @if($accessType==='temporary') <span class="tick">&#10003;</span> @endif @if($accessType==='temporary' && $temporaryUntil) (<strong>{{ $fmtDate($temporaryUntil) }}</strong>) @endif</div>
      </div>

      @include('pdf.both-service-form.shared_approvals', ['r'=>$r, 'fmtDate'=>$fmtDate])
    </div>
    <div class="footer"><span class="left">Directorate of ICT</span><span class="right">IT and Telephone Department</span></div>
    <div class="page-break"></div>
  @endif

  @if($showWellsoft)
    <div class="border p-3">
      @include('pdf.both-service-form.shared_header')
      <div class="center">
        <div class="title">Wellsoft Access Request Form</div>
      </div>

      <div class="row mb-2 mt-2">
        <div class="col"><span class="label" style="font-weight:400">PF Number:</span> <strong>{{ $r->pf_number }}</strong></div>
        <div class="col"><span class="label" style="font-weight:400">Staff Name:</span> <strong>{{ $r->staff_name }}</strong></div>
        <div class="col">
          <span class="label" style="font-weight:400">Signature:</span>
          @if(!empty($applicantSigShort)) <strong>{{ $applicantSigShort }} @if(!empty($applicantSignedAt)) ({{ $applicantSignedAt }}) @endif</strong> @else _____________ @endif
        </div>
      </div>

      <div class="row mb-2">
        <div class="col"><strong>Module Requested for:</strong></div>
      <div class="col">Use {!! $checkbox(($moduleRequestedFor==='use')) !!} @if($moduleRequestedFor==='use') <span class="tick">&#10003;</span> @endif</div>
      <div class="col">Revoke {!! $checkbox(($moduleRequestedFor==='revoke')) !!} @if($moduleRequestedFor==='revoke') <span class="tick">&#10003;</span> @endif</div>
      </div>

      @php
        $wellsoftExact = [
          'Registrar','Specialist','Cashier','Quality Officer',
          'Resident','Intern Doctor','Medical Recorder','Administrator',
          'Nurse','Intern Nurse','Social Worker','Health Attendant'
        ];
        // include any extra selected
        foreach(($selectedWellsoft??[]) as $m){ if($m && !in_array($m,$wellsoftExact,true)) $wellsoftExact[]=$m; }
        $wcols=[[],[],[]]; foreach($wellsoftExact as $i=>$m){ $wcols[$i%3][]=$m; }
      @endphp
      <div class="grid small">
        @foreach($wcols as $col)
          <div class="gcol">
            @foreach($col as $m)
              {!! $checkbox(in_array($m,$selectedWellsoft??[])) !!} {{ $m }} @if(in_array($m,$selectedWellsoft??[])) <span class="tick">&#10003;</span> @endif<br/>
            @endforeach
          </div>
        @endforeach
      </div>

      @if($showInternet)
        <div class="mt-3 subtitle center">Internet Access</div>
        <div class="small center"><strong>Purposes</strong></div>
        <div class="p-2 border" style="min-height:32px; text-align:center;">{{ implode('; ', $internetPurposes) }}</div>
      @endif

      <div class="mt-3 section-title center">Please Specify the Access rights</div>
      <div class="p-2 border" style="min-height:32px; text-align:center;">{{ $hodComments }}</div>

      <div class="row mt-3 small">
        <div class="col">{!! $checkbox(($accessType==='permanent')) !!} Permanent (Until retirement) @if($accessType==='permanent') <span class="tick">&#10003;</span> @endif</div>
        <div class="col">{!! $checkbox(($accessType==='temporary')) !!} Temporary: Start ______ End ______ @if($accessType==='temporary') <span class="tick">&#10003;</span> @endif @if($accessType==='temporary' && $temporaryUntil) (<strong>{{ $fmtDate($temporaryUntil) }}</strong>) @endif</div>
      </div>

      @include('pdf.both-service-form.shared_approvals', ['r'=>$r, 'fmtDate'=>$fmtDate])
    </div>
    <div class="footer"><span class="left">Directorate of ICT</span><span class="right">IT and Telephone Department</span></div>
  @endif

  @php $renderSeparateInternet = $showInternet && !$showWellsoft; @endphp
  @if($renderSeparateInternet)
    <div class="border p-3">
      @include('pdf.both-service-form.shared_header')
      <div class="center">
        <div class="title">Internet Access Request Form</div>
      </div>

      <div class="row mb-2 mt-2">
        <div class="col"><span class="label" style="font-weight:400">PF Number:</span> <strong>{{ $r->pf_number }}</strong></div>
        <div class="col"><span class="label" style="font-weight:400">Staff Name:</span> <strong>{{ $r->staff_name }}</strong></div>
        <div class="col">
          <span class="label" style="font-weight:400">Signature:</span>
          @if(!empty($applicantSigShort)) <strong>{{ $applicantSigShort }} @if(!empty($applicantSignedAt)) ({{ $applicantSignedAt }}) @endif</strong> @else _____________ @endif
        </div>
      </div>

      <div class="row mb-2">
        <div class="col"><strong>Module Requested for:</strong></div>
      <div class="col">Use {!! $checkbox(($moduleRequestedFor==='use')) !!} @if($moduleRequestedFor==='use') <span class="tick">&#10003;</span> @endif</div>
      <div class="col">Revoke {!! $checkbox(($moduleRequestedFor==='revoke')) !!} @if($moduleRequestedFor==='revoke') <span class="tick">&#10003;</span> @endif</div>
      </div>

      <div class="mb-2 subtitle center">Purposes</div>
      <div class="p-2 border" style="min-height:36px; text-align:center;">{{ implode('; ', $internetPurposes) }}</div>

      <div class="mt-3 section-title center">Please Specify the Access rights</div>
      <div class="p-2 border" style="min-height:32px; text-align:center;">{{ $hodComments }}</div>

      <div class="row mt-3 small">
        <div class="col">{!! $checkbox(($accessType==='permanent')) !!} Permanent (until retirement) @if($accessType==='permanent') <span class="tick">&#10003;</span> @endif</div>
        <div class="col">{!! $checkbox(($accessType==='temporary')) !!} Temporary: Start ______ End ______ @if($accessType==='temporary') <span class="tick">&#10003;</span> @endif @if($accessType==='temporary' && $temporaryUntil) (<strong>{{ $fmtDate($temporaryUntil) }}</strong>) @endif</div>
      </div>

      @include('pdf.both-service-form.shared_approvals', ['r'=>$r, 'fmtDate'=>$fmtDate])
    </div>
    <div class="footer"><span class="left">Directorate of ICT</span><span class="right">IT and Telephone Department</span></div>
  @endif
</body>
</html>
