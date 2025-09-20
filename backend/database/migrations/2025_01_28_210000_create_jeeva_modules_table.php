<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jeeva_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['name', 'is_active']);
        });

        // Insert default Jeeva modules
        DB::table('jeeva_modules')->insert([
            ['name' => 'FINANCIAL ACCOUNTING', 'description' => 'Financial accounting and reporting', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'DOCTOR CONSULTATION', 'description' => 'Doctor consultation management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MEDICAL RECORDS', 'description' => 'Medical records management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'OUTPATIENT', 'description' => 'Outpatient services management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'NURSING STATION', 'description' => 'Nursing station operations', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'INPATIENT', 'description' => 'Inpatient services management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'IP CASHIER', 'description' => 'Inpatient cashier services', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HIV', 'description' => 'HIV care and treatment', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'LINEN & LAUNDRY', 'description' => 'Linen and laundry management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'FIXED ASSETS', 'description' => 'Fixed assets management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PMTCT', 'description' => 'Prevention of Mother-to-Child Transmission', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PHARMACY', 'description' => 'Pharmacy management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BILL NOTE', 'description' => 'Billing and notes management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BLOOD BANK', 'description' => 'Blood bank operations', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ORDER MANAGEMENT', 'description' => 'Order management system', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PRIVATE CREDIT', 'description' => 'Private credit management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'LABORATORY', 'description' => 'Laboratory services', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GENERAL STORE', 'description' => 'General store management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'IP BILLING', 'description' => 'Inpatient billing services', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'RADIOLOGY', 'description' => 'Radiology services', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PURCHASE', 'description' => 'Purchase management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SCROLLING', 'description' => 'Scrolling and display management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'OPERATION THEATRE', 'description' => 'Operation theatre management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CSSD', 'description' => 'Central Sterile Supply Department', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'WEB INDENT', 'description' => 'Web indent management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MORTUARY', 'description' => 'Mortuary services', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GENERAL MAINTENANCE', 'description' => 'General maintenance services', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PERSONNEL', 'description' => 'Personnel management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MAINTENANCE', 'description' => 'Maintenance services', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PAYROLL', 'description' => 'Payroll management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CMS', 'description' => 'Content Management System', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MIS STATISTICS', 'description' => 'Management Information System Statistics', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jeeva_modules');
    }
};