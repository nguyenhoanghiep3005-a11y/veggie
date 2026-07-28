<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('damage_slips', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropIndex(['source', 'occurred_at']);
            
            $table->dropColumn(['source', 'created_by']);
            
            $table->index(['occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::table('damage_slips', function (Blueprint $table) {
            $table->string('source')->default('purchase_import');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->dropIndex(['occurred_at']);
            $table->index(['source', 'occurred_at']);
        });
    }
};
