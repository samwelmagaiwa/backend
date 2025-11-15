<table class="thin mt-4" width="100%">
  <tr>
    <th width="50%" class="ital">Approval</th>
    <th width="50%" class="ital">Approval</th>
  </tr>
  <tr>
    <td>
      <div class="subtitle">HoD/BM:</div>
      <div>Name: {{ $r->hod_name }}</div>
      <div>Signature: @if($r->hod_signature_path) On file @elseif($r->historyHasSignerByName($r->hod_name)) <strong>Digitally signed</strong> @endif @if(!empty($sigShorts['hod'])) <strong>{{ $sigShorts['hod'] }}</strong> @endif</div>
      <div>Date: {{ $fmtDate($r->hod_approved_at) }}</div>
      @if(!empty($r->hod_comments))
        <div>Comment: {{ $r->hod_comments }}</div>
      @endif
    </td>
    <td>
      <div class="subtitle">Divisional Director:</div>
      <div>Name: {{ $r->divisional_director_name }}</div>
      <div>Signature: @if($r->divisional_director_signature_path) On file @elseif($r->historyHasSignerByName($r->divisional_director_name)) <strong>Digitally signed</strong> @endif @if(!empty($sigShorts['divisional'])) <strong>{{ $sigShorts['divisional'] }}</strong> @endif</div>
      <div>Date: {{ $fmtDate($r->divisional_approved_at) }}</div>
      @if(!empty($r->divisional_director_comments))
        <div>Comment: {{ $r->divisional_director_comments }}</div>
      @endif
    </td>
  </tr>
  <tr>
    <td>
      <div class="subtitle">Director of ICT:</div>
      <div>Name: {{ $r->ict_director_name }}</div>
      <div>Signature: @if($r->ict_director_signature_path) On file @elseif($r->historyHasSignerByName($r->ict_director_name)) <strong>Digitally signed</strong> @endif @if(!empty($sigShorts['ict_director'])) <strong>{{ $sigShorts['ict_director'] }}</strong> @endif</div>
      <div>Date: {{ $fmtDate($r->ict_director_approved_at) }}</div>
      @if(!empty($r->ict_director_comments))
        <div>Comment: {{ $r->ict_director_comments }}</div>
      @endif
    </td>
    <td>
      <div class="subtitle">Comments:</div>
      <div style="min-height:48px;">{{ $r->ict_director_comments }}</div>
    </td>
  </tr>
  <tr>
    <th colspan="2" class="subtitle ital">For Implementation</th>
  </tr>
  <tr>
    <td>
      <div class="subtitle">Head of IT:</div>
      <div>Name: {{ $r->head_it_name }}</div>
      <div>Signature: @if($r->head_it_signature_path) On file @elseif($r->historyHasSignerByName($r->head_it_name)) <strong>Digitally signed</strong> @endif @if(!empty($sigShorts['head_it'])) <strong>{{ $sigShorts['head_it'] }}</strong> @endif</div>
      <div>Date: {{ $fmtDate($r->head_it_approved_at) }}</div>
      @if(!empty($r->head_it_comments))
        <div>Comment: {{ $r->head_it_comments }}</div>
      @endif
    </td>
    <td>
      <div class="subtitle">ICT Officer granting access</div>
      <div>Name: {{ $r->ict_officer_name }}</div>
      <div>Signature: @if($r->ict_officer_signature_path) On file @elseif($r->historyHasSignerByName($r->ict_officer_name)) <strong>Digitally signed</strong> @endif @if(!empty($sigShorts['ict_officer'])) <strong>{{ $sigShorts['ict_officer'] }}</strong> @endif</div>
      <div>Date: {{ $fmtDate($r->ict_officer_implemented_at) }}</div>
      @if(!empty($r->ict_officer_comments))
        <div>Comment: {{ $r->ict_officer_comments }}</div>
      @endif
    </td>
  </tr>
</table>
