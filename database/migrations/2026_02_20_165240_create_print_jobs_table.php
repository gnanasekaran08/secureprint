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
        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->uuid('job_uuid')->unique()->index();
            $table->integer('doc_no')->unique()->index();
            $table->unsignedBigInteger('user_id');
            $table->string('printer')->nullable();
            $table->string('error_message')->nullable();
            $table->string('status', '20')->default('pending')->index();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('total_cost', 8, 2)->nullable();
            $table->integer('total_pages')->nullable();
            $table->integer('total_copies')->nullable();
            $table->boolean('is_color')->default(false);
            $table->boolean('is_double_sided')->default(false);
            $table->boolean('is_portrait')->default(true);
            $table->integer('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('removed_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_jobs');
    }
};
