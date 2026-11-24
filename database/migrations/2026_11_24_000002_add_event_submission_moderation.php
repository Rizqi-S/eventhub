<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Status moderasi pengajuan event.
            // Event lama otomatis dianggap sudah disetujui.
            $table->string('submission_status')
                ->default('approved')
                ->index();

            // Identitas pengaju event.
            $table->string('submitted_by_name')->nullable();
            $table->string('submitted_by_email')->nullable();
            $table->string('submitted_by_phone', 20)->nullable();

            // Catatan dari admin ketika meninjau pengajuan.
            $table->text('review_notes')->nullable();

            // Informasi proses peninjauan.
            $table->timestamp('reviewed_at')->nullable();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');

            $table->dropIndex(['submission_status']);

            $table->dropColumn([
                'submission_status',
                'submitted_by_name',
                'submitted_by_email',
                'submitted_by_phone',
                'review_notes',
                'reviewed_at',
            ]);
        });
    }
};