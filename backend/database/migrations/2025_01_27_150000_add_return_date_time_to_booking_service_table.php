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
        if (Schema::hasTable('booking_service')) {
            Schema::table('booking_service', function (Blueprint $table) {
                if (!Schema::hasColumn('booking_service', 'return_date_time')) {
                    // Add return_date_time field
                    $table->dateTime('return_date_time')->nullable()->after('return_time');
                    
                    // Add index for performance when querying upcoming returns
                    $table->index(['return_date_time', 'status']);
                }
            });
            
            // Migrate existing data: combine return_date and return_time into return_date_time
            DB::statement("
                UPDATE booking_service 
                SET return_date_time = CONCAT(return_date, ' ', return_time) 
                WHERE return_date IS NOT NULL AND return_time IS NOT NULL
            ");
        } else {
            echo "⚠️ Table booking_service does not exist yet. Migration will be skipped and handled later.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('booking_service') && Schema::hasColumn('booking_service', 'return_date_time')) {
            Schema::table('booking_service', function (Blueprint $table) {
                $table->dropIndex(['return_date_time', 'status']);
                $table->dropColumn('return_date_time');
            });
        }
    }
};