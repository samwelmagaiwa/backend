<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wellsoft_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['name', 'is_active']);
        });

        // Insert default Wellsoft modules
        DB::table('wellsoft_modules')->insert([
            ['name' => 'Registrar', 'description' => 'Patient registration and management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Specialist', 'description' => 'Specialist consultation module', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cashier', 'description' => 'Payment and billing management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Resident Nurse', 'description' => 'Nursing care and patient monitoring', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Intern Doctor', 'description' => 'Medical intern access and training', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Intern Nurse', 'description' => 'Nursing intern access and training', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medical Recorder', 'description' => 'Medical records management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Social Worker', 'description' => 'Social services and patient support', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Quality Officer', 'description' => 'Quality assurance and improvement', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administrator', 'description' => 'System administration and management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Health Attendant', 'description' => 'Health support and assistance', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wellsoft_modules');
    }
};