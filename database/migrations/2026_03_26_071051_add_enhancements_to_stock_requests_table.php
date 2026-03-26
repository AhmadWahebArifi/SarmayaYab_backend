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
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->string('priority')->default('normal')->after('status'); // urgent, normal, low
            $table->date('expected_delivery_date')->nullable()->after('priority');
            $table->string('reason')->nullable()->after('note');
            $table->decimal('total_value', 10, 2)->default(0)->after('reason');
            $table->string('cost_center')->nullable()->after('total_value');
            $table->text('rejection_reason')->nullable()->after('reviewed_at');
            $table->text('approval_notes')->nullable()->after('rejection_reason');
            $table->string('tracking_number')->nullable()->after('dispatched_at');
            $table->timestamp('estimated_delivery')->nullable()->after('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->dropColumn([
                'priority',
                'expected_delivery_date',
                'reason',
                'total_value',
                'cost_center',
                'rejection_reason',
                'approval_notes',
                'tracking_number',
                'estimated_delivery'
            ]);
        });
    }
};