<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jeeva Access Request Form</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        .section { margin-bottom: 18px; }
        .label { font-weight: bold; }
        .signature-img { max-width: 180px; max-height: 60px; border: 1px solid #ccc; border-radius: 4px; margin-top: 4px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .table th, .table td { border: 1px solid #ccc; padding: 4px 8px; }
        .table th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">MUHIMBILI NATIONAL HOSPITAL<br>MLOGANZILA</h2>
    <h3 style="text-align:center;">Jeeva Access Request Form</h3>
    <div class="section">
        <span class="label">PF Number:</span> {{ $form->pf_number }}<br>
        <span class="label">Staff Name:</span> {{ $form->staff_name }}<br>
        <span class="label">Signature:</span><br>
        @if($form->signature)
            <img src="{{ $form->signature }}" class="signature-img" alt="Signature">
        @endif
    </div>
    <div class="section">
        <span class="label">Module Action:</span> {{ ucfirst($form->module_action) }}<br>
        <span class="label">Modules:</span> {{ implode(', ', json_decode($form->modules, true) ?? []) }}<br>
        <span class="label">Access Rights:</span> {{ $form->access_rights }}<br>
        <span class="label">Access Duration:</span> {{ ucfirst($form->access_duration) }}<br>
        @if($form->access_duration === 'temporary')
            <span class="label">Temporary Until:</span> {{ $form->temporary_until }}<br>
        @endif
    </div>
    <div class="section">
        <span class="label">Approval for Staff Only</span>
        <table class="table">
            <tr>
                <th>HoD/BM</th>
                <th>Divisional Director</th>
                <th>Director of ICT</th>
            </tr>
            <tr>
                <td>
                    Name: {{ $form->hodbm_name }}<br>
                    Signature: {{ $form->hodbm_signature }}<br>
                    Date: {{ $form->hodbm_date }}
                </td>
                <td>
                    Name: {{ $form->divdir_name }}<br>
                    Signature: {{ $form->divdir_signature }}<br>
                    Date: {{ $form->divdir_date }}
                </td>
                <td>
                    Name: {{ $form->ictdir_name }}<br>
                    Signature/Date: {{ $form->ictdir_signature_date }}<br>
                    Comments: {{ $form->ictdir_comments }}
                </td>
            </tr>
        </table>
    </div>
    <div class="section">
        <span class="label">For Implementation</span>
        <table class="table">
            <tr>
                <th>Head of IT</th>
                <th>ICT Officer Granting Access</th>
            </tr>
            <tr>
                <td>
                    Name: {{ $form->headit_name }}<br>
                    Signature/Date: {{ $form->headit_signature_date }}
                </td>
                <td>
                    Name: {{ $form->ict_officer_name }}<br>
                    Signature/Date: {{ $form->ict_officer_signature_date }}
                </td>
            </tr>
        </table>
    </div>
    <div style="text-align:center; margin-top: 24px;">
        <span class="label">Directorate of ICT &nbsp; IT and Telephone Department</span>
    </div>
</body>
</html>
