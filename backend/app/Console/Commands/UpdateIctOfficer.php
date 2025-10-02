<?php

namespace App\Console\Commands;

use App\Models\UserAccess;
use Illuminate\Console\Command;

class UpdateIctOfficer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-access:update-ict-officer 
                            {pf_number : The PF Number of the user access record}
                            {ict_officer_name : The name of the ICT Officer}
                            {--status=implemented : The ICT Officer status}
                            {--comments= : Comments from the ICT Officer}
                            {--create-signature : Create a placeholder signature file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update ICT Officer information for a user access record';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pfNumber = $this->argument('pf_number');
        $ictOfficerName = $this->argument('ict_officer_name');
        $status = $this->option('status');
        $comments = $this->option('comments');
        $createSignature = $this->option('create-signature');

        $record = UserAccess::where('pf_number', $pfNumber)->first();

        if (!$record) {
            $this->error("No user access record found with PF Number: {$pfNumber}");
            return 1;
        }

        $this->info("Found record for {$record->staff_name} (PF: {$pfNumber})");
        
        $updateData = [
            'ict_officer_name' => $ictOfficerName,
            'ict_officer_status' => $status,
        ];

        if ($status === 'implemented') {
            $updateData['ict_officer_implemented_at'] = now();
        }

        if ($comments) {
            $updateData['ict_officer_comments'] = $comments;
        }

        // Handle signature creation if requested
        if ($createSignature) {
            $signaturePath = $this->createIctOfficerSignature($pfNumber);
            if ($signaturePath) {
                $updateData['ict_officer_signature_path'] = $signaturePath;
                $this->info("Created signature file: {$signaturePath}");
            } else {
                $this->warn('Failed to create signature file');
            }
        }

        $record->update($updateData);

        $this->info("Successfully updated ICT Officer information:");
        $this->line("- ICT Officer Name: {$ictOfficerName}");
        $this->line("- Status: {$status}");
        if ($comments) {
            $this->line("- Comments: {$comments}");
        }

        return 0;
    }

    /**
     * Create an ICT Officer signature file.
     */
    private function createIctOfficerSignature(string $pfNumber): ?string
    {
        try {
            // Create ICT officer signature directory if it doesn't exist
            $signatureDir = storage_path('app/public/signatures/ict_officer');
            if (!is_dir($signatureDir)) {
                mkdir($signatureDir, 0755, true);
            }

            // Look for an existing signature file to copy as template
            $templatePath = null;
            $possibleTemplates = [
                storage_path('app/public/signatures/head_of_it'),
                storage_path('app/public/signatures/hod'),
                storage_path('app/public/signatures/ict_director')
            ];

            foreach ($possibleTemplates as $templateDir) {
                if (is_dir($templateDir)) {
                    $files = glob($templateDir . '/*.{jpg,jpeg,png}', GLOB_BRACE);
                    if (!empty($files)) {
                        $templatePath = $files[0]; // Use first found signature
                        break;
                    }
                }
            }

            if (!$templatePath) {
                $this->warn('No template signature found to copy');
                return null;
            }

            // Generate new signature filename
            $timestamp = time();
            $extension = pathinfo($templatePath, PATHINFO_EXTENSION);
            $signatureFilename = "ict_officer_signature_{$pfNumber}_{$timestamp}.{$extension}";
            $signaturePath = $signatureDir . '/' . $signatureFilename;

            // Copy template file to new signature path
            if (copy($templatePath, $signaturePath)) {
                return "signatures/ict_officer/{$signatureFilename}";
            } else {
                $this->error('Failed to copy signature template');
                return null;
            }

        } catch (\Exception $e) {
            $this->error("Error creating signature: {$e->getMessage()}");
            return null;
        }
    }
}
