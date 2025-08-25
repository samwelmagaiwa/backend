<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Both Service Form - {{ $form->pf_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
            border: 1px solid #ddd;
            padding: 15px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .form-row {
            display: flex;
            margin-bottom: 8px;
        }
        .form-label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }
        .form-value {
            flex: 1;
        }
        .services-list {
            margin: 10px 0;
        }
        .service-item {
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }
        .approval-section {
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
        }
        .approval-section.approved {
            border-left-color: #28a745;
        }
        .approval-section.rejected {
            border-left-color: #dc3545;
        }
        .approval-section.pending {
            border-left-color: #ffc107;
        }
        .signature-box {
            border: 1px solid #ccc;
            height: 60px;
            margin: 10px 0;
            padding: 5px;
            text-align: center;
            background-color: #f8f9fa;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BOTH SERVICE FORM</h1>
        <h2>Combined Access Request for Wellsoft, Jeeva & Internet Access</h2>
        <p><strong>Form ID:</strong> {{ $form->id }} | <strong>Date:</strong> {{ $form->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Personal Information Section -->
    <div class="section">
        <div class="section-title">Personal Information</div>
        <div class="form-row">
            <span class="form-label">PF Number:</span>
            <span class="form-value">{{ $form->pf_number }}</span>
        </div>
        <div class="form-row">
            <span class="form-label">Staff Name:</span>
            <span class="form-value">{{ $form->staff_name }}</span>
        </div>
        <div class="form-row">
            <span class="form-label">Phone Number:</span>
            <span class="form-value">{{ $form->phone_number }}</span>
        </div>
        <div class="form-row">
            <span class="form-label">Department:</span>
            <span class="form-value">{{ $form->department->name ?? 'N/A' }}</span>
        </div>
    </div>

    <!-- Services Requested Section -->
    <div class="section">
        <div class="section-title">Services Requested</div>
        <div class="services-list">
            @foreach($form->services_requested ?? [] as $service)
                <div class="service-item">
                    ✓ {{ \App\Models\ModuleAccessRequest::AVAILABLE_SERVICES[$service] ?? $service }}
                </div>
            @endforeach
        </div>
        <div class="form-row">
            <span class="form-label">Access Type:</span>
            <span class="form-value">{{ ucfirst($form->access_type) }}</span>
        </div>
        @if($form->access_type === 'temporary' && $form->temporary_until)
            <div class="form-row">
                <span class="form-label">Valid Until:</span>
                <span class="form-value">{{ $form->temporary_until->format('d/m/Y') }}</span>
            </div>
        @endif
        @if($form->modules && count($form->modules) > 0)
            <div class="form-row">
                <span class="form-label">Modules:</span>
                <span class="form-value">{{ implode(', ', $form->modules) }}</span>
            </div>
        @endif
        @if($form->comments)
            <div class="form-row">
                <span class="form-label">Comments:</span>
                <span class="form-value">{{ $form->comments }}</span>
            </div>
        @endif
    </div>

    <!-- Approval Workflow Section -->
    <div class="section">
        <div class="section-title">Approval Workflow</div>
        
        <!-- HOD Approval -->
        <div class="approval-section {{ $form->hod_approval_status }}">
            <div class="section-title">
                1. Head of Department (HOD) Approval
                <span class="status-badge status-{{ $form->hod_approval_status }}">{{ ucfirst($form->hod_approval_status) }}</span>
            </div>
            @if($form->hod_approval_status !== 'pending')
                <div class="form-row">
                    <span class="form-label">Approved By:</span>
                    <span class="form-value">{{ $form->hodUser->name ?? 'N/A' }}</span>
                </div>
                <div class="form-row">
                    <span class="form-label">Date:</span>
                    <span class="form-value">{{ $form->hod_approved_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
                @if($form->hod_comments)
                    <div class="form-row">
                        <span class="form-label">Comments:</span>
                        <span class="form-value">{{ $form->hod_comments }}</span>
                    </div>
                @endif
                <div class="signature-box">
                    @if($form->hod_signature_path)
                        <img src="{{ storage_path('app/public/' . $form->hod_signature_path) }}" alt="HOD Signature" style="max-height: 50px;">
                    @else
                        Digital Signature
                    @endif
                </div>
            @else
                <p><em>Pending HOD approval</em></p>
            @endif
        </div>

        <!-- Divisional Director Approval -->
        <div class="approval-section {{ $form->divisional_director_approval_status }}">
            <div class="section-title">
                2. Divisional Director Approval
                <span class="status-badge status-{{ $form->divisional_director_approval_status }}">{{ ucfirst($form->divisional_director_approval_status) }}</span>
            </div>
            @if($form->divisional_director_approval_status !== 'pending')
                <div class="form-row">
                    <span class="form-label">Approved By:</span>
                    <span class="form-value">{{ $form->divisionalDirectorUser->name ?? 'N/A' }}</span>
                </div>
                <div class="form-row">
                    <span class="form-label">Date:</span>
                    <span class="form-value">{{ $form->divisional_director_approved_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
                @if($form->divisional_director_comments)
                    <div class="form-row">
                        <span class="form-label">Comments:</span>
                        <span class="form-value">{{ $form->divisional_director_comments }}</span>
                    </div>
                @endif
                <div class="signature-box">
                    @if($form->divisional_director_signature_path)
                        <img src="{{ storage_path('app/public/' . $form->divisional_director_signature_path) }}" alt="Divisional Director Signature" style="max-height: 50px;">
                    @else
                        Digital Signature
                    @endif
                </div>
            @else
                <p><em>Pending Divisional Director approval</em></p>
            @endif
        </div>

        <!-- DICT Approval -->
        <div class="approval-section {{ $form->dict_approval_status }}">
            <div class="section-title">
                3. Director of ICT (DICT) Approval
                <span class="status-badge status-{{ $form->dict_approval_status }}">{{ ucfirst($form->dict_approval_status) }}</span>
            </div>
            @if($form->dict_approval_status !== 'pending')
                <div class="form-row">
                    <span class="form-label">Approved By:</span>
                    <span class="form-value">{{ $form->dictUser->name ?? 'N/A' }}</span>
                </div>
                <div class="form-row">
                    <span class="form-label">Date:</span>
                    <span class="form-value">{{ $form->dict_approved_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
                @if($form->dict_comments)
                    <div class="form-row">
                        <span class="form-label">Comments:</span>
                        <span class="form-value">{{ $form->dict_comments }}</span>
                    </div>
                @endif
                <div class="signature-box">
                    @if($form->dict_signature_path)
                        <img src="{{ storage_path('app/public/' . $form->dict_signature_path) }}" alt="DICT Signature" style="max-height: 50px;">
                    @else
                        Digital Signature
                    @endif
                </div>
            @else
                <p><em>Pending DICT approval</em></p>
            @endif
        </div>

        <!-- Head of IT Approval -->
        <div class="approval-section {{ $form->hod_it_approval_status }}">
            <div class="section-title">
                4. Head of IT Approval
                <span class="status-badge status-{{ $form->hod_it_approval_status }}">{{ ucfirst($form->hod_it_approval_status) }}</span>
            </div>
            @if($form->hod_it_approval_status !== 'pending')
                <div class="form-row">
                    <span class="form-label">Approved By:</span>
                    <span class="form-value">{{ $form->hodItUser->name ?? 'N/A' }}</span>
                </div>
                <div class="form-row">
                    <span class="form-label">Date:</span>
                    <span class="form-value">{{ $form->hod_it_approved_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
                @if($form->hod_it_comments)
                    <div class="form-row">
                        <span class="form-label">Comments:</span>
                        <span class="form-value">{{ $form->hod_it_comments }}</span>
                    </div>
                @endif
                <div class="signature-box">
                    @if($form->hod_it_signature_path)
                        <img src="{{ storage_path('app/public/' . $form->hod_it_signature_path) }}" alt="Head of IT Signature" style="max-height: 50px;">
                    @else
                        Digital Signature
                    @endif
                </div>
            @else
                <p><em>Pending Head of IT approval</em></p>
            @endif
        </div>

        <!-- ICT Officer Approval -->
        <div class="approval-section {{ $form->ict_officer_approval_status }}">
            <div class="section-title">
                5. ICT Officer Approval (Final)
                <span class="status-badge status-{{ $form->ict_officer_approval_status }}">{{ ucfirst($form->ict_officer_approval_status) }}</span>
            </div>
            @if($form->ict_officer_approval_status !== 'pending')
                <div class="form-row">
                    <span class="form-label">Approved By:</span>
                    <span class="form-value">{{ $form->ictOfficerUser->name ?? 'N/A' }}</span>
                </div>
                <div class="form-row">
                    <span class="form-label">Date:</span>
                    <span class="form-value">{{ $form->ict_officer_approved_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
                @if($form->ict_officer_comments)
                    <div class="form-row">
                        <span class="form-label">Comments:</span>
                        <span class="form-value">{{ $form->ict_officer_comments }}</span>
                    </div>
                @endif
                <div class="signature-box">
                    @if($form->ict_officer_signature_path)
                        <img src="{{ storage_path('app/public/' . $form->ict_officer_signature_path) }}" alt="ICT Officer Signature" style="max-height: 50px;">
                    @else
                        Digital Signature
                    @endif
                </div>
            @else
                <p><em>Pending ICT Officer approval</em></p>
            @endif
        </div>
    </div>

    <!-- Overall Status -->
    <div class="section">
        <div class="section-title">Overall Status</div>
        <div class="form-row">
            <span class="form-label">Current Status:</span>
            <span class="form-value">
                <span class="status-badge status-{{ $form->overall_status }}">{{ ucfirst($form->overall_status) }}</span>
            </span>
        </div>
        <div class="form-row">
            <span class="form-label">Current Stage:</span>
            <span class="form-value">{{ ucfirst(str_replace('_', ' ', $form->current_approval_stage)) }}</span>
        </div>
    </div>

    <div class="footer">
        <p>This document was generated automatically on {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Hospital Management System - Both Service Form</p>
    </div>
</body>
</html>