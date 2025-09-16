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
        if (!Schema::hasTable('user_access')) {
            // If table doesn't exist, create it with all needed columns
            Schema::create('user_access', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('pf_number', 50);
                $table->string('staff_name', 255);
                $table->string('phone_number', 20)->nullable();
                $table->unsignedBigInteger('department_id')->nullable();
                $table->string('signature_path', 500)->nullable();
                $table->json('purpose')->nullable();
                $table->json('request_type');
                $table->enum('status', [
                    'pending', 
                    'pending_hod', 
                    'hod_approved', 
                    'hod_rejected',
                    'approved', 
                    'rejected', 
                    'in_review',
                    'cancelled'
                ])->default('pending');
                
                // HOD Approval columns
                $table->text('hod_comments')->nullable();
                $table->string('hod_name', 255)->nullable();
                $table->timestamp('hod_approved_at')->nullable();
                
                // Cancellation columns
                $table->text('cancellation_reason')->nullable();
                $table->unsignedBigInteger('cancelled_by')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                
                $table->timestamps();
                
                // Indexes
                $table->index(['status', 'created_at']);
                $table->index('pf_number');
                $table->index('staff_name');
                $table->index('department_id');
                
                // Foreign key constraints
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
                $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('set null');
            });
            
            echo "✅ Created user_access table with HOD approval columns\n";
        } else {
            // Add missing columns to existing table
            Schema::table('user_access', function (Blueprint $table) {
                // Check and add HOD approval columns
                if (!Schema::hasColumn('user_access', 'hod_comments')) {
                    $table->text('hod_comments')->nullable()->after('status');
                }
                if (!Schema::hasColumn('user_access', 'hod_name')) {
                    $table->string('hod_name', 255)->nullable()->after('hod_comments');
                }
                if (!Schema::hasColumn('user_access', 'hod_approved_at')) {
                    $table->timestamp('hod_approved_at')->nullable()->after('hod_name');
                }
                
                // Check and add cancellation columns
                if (!Schema::hasColumn('user_access', 'cancellation_reason')) {
                    $table->text('cancellation_reason')->nullable()->after('hod_approved_at');
                }
                if (!Schema::hasColumn('user_access', 'cancelled_by')) {
                    $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancellation_reason');
                }
                if (!Schema::hasColumn('user_access', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
                }
            });
            
            // Update status enum to include new statuses if not already present
            try {
                DB::statement("ALTER TABLE user_access MODIFY COLUMN status ENUM(
                    'pending', 
                    'pending_hod', 
                    'hod_approved', 
                    'hod_rejected',
                    'approved', 
                    'rejected', 
                    'in_review',
                    'cancelled'
                ) DEFAULT 'pending'");
                
                echo "✅ Updated status enum with HOD statuses\n";
            } catch (\Exception $e) {
                echo "⚠️ Status enum may already be updated: " . $e->getMessage() . "\n";
            }
            
            // Add foreign key constraints if they don't exist
            try {
                if (!$this->foreignKeyExists('user_access', 'user_access_cancelled_by_foreign')) {
                    Schema::table('user_access', function (Blueprint $table) {
                        $table->foreign('cancelled_by', 'user_access_cancelled_by_foreign')
                              ->references('id')->on('users')->onDelete('set null');
                    });
                }
            } catch (\Exception $e) {
                echo "⚠️ Foreign key constraint may already exist: " . $e->getMessage() . "\n";
            }
            
            echo "✅ Added HOD approval columns to existing user_access table\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_access')) {
            Schema::table('user_access', function (Blueprint $table) {
                // Drop foreign key first
                if ($this->foreignKeyExists('user_access', 'user_access_cancelled_by_foreign')) {
                    $table->dropForeign('user_access_cancelled_by_foreign');
                }
                
                // Drop the added columns
                $table->dropColumn([
                    'hod_comments',
                    'hod_name', 
                    'hod_approved_at',
                    'cancellation_reason',
                    'cancelled_by',
                    'cancelled_at'
                ]);
            });
            
            // Reset status enum to original values
            try {
                DB::statement("ALTER TABLE user_access MODIFY COLUMN status ENUM(
                    'pending', 
                    'approved', 
                    'rejected', 
                    'in_review'
                ) DEFAULT 'pending'");
            } catch (\Exception $e) {
                echo "⚠️ Could not revert status enum: " . $e->getMessage() . "\n";
            }
            
            echo "✅ Removed HOD approval columns from user_access table\n";
        }
    }
    
    /**
     * Check if a foreign key constraint exists
     */
    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $result = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ?
        ", [$table, $constraintName]);
        
        return !empty($result);
    }
};
