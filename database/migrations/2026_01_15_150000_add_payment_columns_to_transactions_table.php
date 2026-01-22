<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('tr_payment_method', 20)->default('cod')->index();
            $table->tinyInteger('tr_payment_status')->default(0)->index();
            $table->string('tr_payment_code')->nullable();
            $table->text('tr_payment_meta')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('tr_payment_method');
            $table->dropColumn('tr_payment_status');
            $table->dropColumn('tr_payment_code');
            $table->dropColumn('tr_payment_meta');
        });
    }
};
