@php
    $showJeeva = in_array('jeeva_access', $types ?? []) || !empty($selectedJeeva);
    $showWellsoft = in_array('wellsoft', $types ?? []) || !empty($selectedWellsoft);
    $showInternet = in_array('internet_access_request', $types ?? []) || !empty($internetPurposes);
    $fmtDate = function($d){ return $d ? (is_string($d) ? date('d/m/Y', strtotime($d)) : $d->format('d/m/Y')) : ''; };
@endphp
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Access Request {{ $r->id }}</title>
  <style>
    @page { margin: 22px 22px; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #000; }
    .border { border: 1px solid #000; }
    .row { display: table; width: 100%; table-layout: fixed; }
    .col { display: table-cell; vertical-align: top; }
    .center { text-align: center; }
    .title { font-weight: 700; font-size: 14px; text-transform: uppercase; }
    .small { font-size: 11px; }
    .mt-4 { margin-top: 12px; }
    .mb-2 { margin-bottom: 6px; }
    .mb-4 { margin-bottom: 12px; }
    .p-2 { padding: 8px; }
    .p-3 { padding: 12px; }
    .checkbox { display:inline-block; width: 12px; height: 12px; border:1px solid #000; margin:0 6px; }
    .checked { background:#000; }
    .grid { display: table; width: 100%; table-layout: fixed; }
    .grid .gcol { display: table-cell; vertical-align: top; padding-right: 6px; }
    .thin { border-collapse: collapse; }
    .thin td, .thin th { border: 1px solid #000; padding: 6px; }
    .label { width: 130px; font-weight: 700; }
    .spacer { height: 8px; }
    .page-break { page-break-after: always; }
  </style>
</head>
<body>
  <div class="border p-3">
    <div class="center title mb-2">Jeeva Access Request Form</div>

    <div class="row mb-2">
      <div class="col"><span class="label">PF Number:</span> {{ $r->pf_number }}</div>
      <div class="col"><span class="label">Staff Name:</span> {{ $r->staff_name }}</div>
      <div class="col"><span class="label">Signature</span> ...............</div>
    </div>

    <div class="row mb-2">
      <div class="col"><strong>Module Requested for:</strong></div>
      <div class="col">
        <span>Use</span>
        <span class="checkbox {{ ($moduleRequestedFor==='use') ? 'checked' : '' }}"></span>
      </div>
      <div class="col">
        <span>Revoke</span>
        <span class="checkbox {{ ($moduleRequestedFor==='revoke') ? 'checked' : '' }}"></span>
      </div>
    </div>

    @if($showJeeva)
      <div class="mb-2"><strong>Jeeva Modules</strong></div>
      <div class="grid">
        @php
          // Split Jeeva master list by 3 columns
          $cols = [[],[],[]];
          foreach ($jeevaModulesMaster as $i=>$m) { $cols[$i%3][] = $m; }
        @endphp
        @foreach($cols as $column)
          <div class="gcol">
            @foreach($column as $m)
              @php $isSel = in_array($m, $selectedJeeva ?? []); @endphp
              <div class="small"> <span class="checkbox {{ $isSel ? 'checked' : '' }}"></span> {{ $m }}</div>
            @endforeach
          </div>
        @endforeach
      </div>
    @endif

    @if($showWellsoft)
      <div class="mt-4 mb-2"><strong>Wellsoft Modules</strong></div>
      <div class="grid">
        @php
          // Two columns for Wellsoft
          $wcols = [[],[]];
          foreach ($wellsoftModulesMaster as $i=>$m) { $wcols[$i%2][] = $m; }
        @endphp
        @foreach($wcols as $wcolumn)
          <div class="gcol">
            @foreach($wcolumn as $m)
              @php $isSel = in_array($m, $selectedWellsoft ?? []); @endphp
              <div class="small"> <span class="checkbox {{ $isSel ? 'checked' : '' }}"></span> {{ $m }}</div>
            @endforeach
          </div>
        @endforeach
      </div>
    @endif

    @if($showInternet)
      <div class="mt-4 mb-2"><strong>Internet Access</strong></div>
      <div class="small">Purposes:</div>
      <div class="p-2 border" style="min-height:60px;">{{ implode('; ', $internetPurposes) }}</div>
    @endif

    <div class="mt-4 small">Please Specify the Access rights</div>
    <div class="p-2 border" style="min-height:80px;">{{ $hodComments }}</div>

    <div class="row mt-4 small">
      <div class="col">
        <span class="checkbox {{ $accessType==='permanent' ? 'checked' : '' }}"></span> Permanent (until retirement)
      </div>
      <div class="col">
        <span class="checkbox {{ $accessType==='temporary' ? 'checked' : '' }}"></span> Temporary Until ____/____/20__
        @if($accessType==='temporary' && $temporaryUntil)
          &nbsp; ({{ $fmtDate($temporaryUntil) }})
        @endif
      </div>
    </div>

    <div class="mt-4 mb-2"><strong>Approval</strong></div>
    <table class="thin" width="100%">
      <tr>
        <th width="50%">HoD/BM</th>
        <th width="50%">Divisional Director</th>
      </tr>
      <tr>
        <td>
          <div>Name: {{ $r->hod_name }}</div>
          <div>Signature: {{ $r->hod_signature_path ? 'On file' : ( $r->historyHasSignerByName($r->hod_name) ? 'Digitally signed' : '' ) }}</div>
          <div>Date: {{ $fmtDate($r->hod_approved_at) }}</div>
        </td>
        <td>
          <div>Name: {{ $r->divisional_director_name }}</div>
          <div>Signature: {{ $r->divisional_director_signature_path ? 'On file' : ( $r->historyHasSignerByName($r->divisional_director_name) ? 'Digitally signed' : '' ) }}</div>
          <div>Date: {{ $fmtDate($r->divisional_approved_at) }}</div>
        </td>
      </tr>
      <tr>
        <th>Director of ICT</th>
        <th>Comments</th>
      </tr>
      <tr>
        <td>
          <div>Name: {{ $r->ict_director_name }}</div>
          <div>Signature/Date: {{ $r->ict_director_signature_path ? 'On file' : ( $r->historyHasSignerByName($r->ict_director_name) ? 'Digitally signed' : '' ) }} {{ $fmtDate($r->ict_director_approved_at) }}</div>
        </td>
        <td>
          {{ $r->ict_director_comments }}
        </td>
      </tr>
      <tr>
        <th colspan="2">For Implementation</th>
      </tr>
      <tr>
        <td>
          <div><strong>Head of IT</strong></div>
          <div>Name: {{ $r->head_it_name }}</div>
          <div>Signature/Date: {{ $r->head_it_signature_path ? 'On file' : ( $r->historyHasSignerByName($r->head_it_name) ? 'Digitally signed' : '' ) }} {{ $fmtDate($r->head_it_approved_at) }}</div>
        </td>
        <td>
          <div><strong>ICT officer granting access</strong></div>
          <div>Name: {{ $r->ict_officer_name }}</div>
          <div>Signature/Date: {{ $r->ict_officer_signature_path ? 'On file' : ( $r->historyHasSignerByName($r->ict_officer_name) ? 'Digitally signed' : '' ) }} {{ $fmtDate($r->ict_officer_implemented_at) }}</div>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>