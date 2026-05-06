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
        Schema::table('event_types', function (Blueprint $table) {
            if(!Schema::hasColumn('events', 'created_by')){
                $table->foreignId('created_by')->after('is_active')->constrained('users')->cascadeOnDelete();
            }
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_types', function (Blueprint $table) {
            if(Schema::hasColumn('events', 'created_by')){
                $table->dropForeign('created_by');
            $table->dropColumn('created_by');
       
            }
         });    
    }
};
