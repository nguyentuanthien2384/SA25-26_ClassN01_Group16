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
        Schema::create('outbox_messages', function (Blueprint $table) {
            $table->id();
            $table->string('aggregate_type')->comment('Entity type: Product, Order, User, etc');
            $table->unsignedBigInteger('aggregate_id')->comment('Entity ID');
            $table->string('event_type')->comment('Event name: ProductCreated, OrderPlaced, etc');
            $table->json('payload')->comment('Event data in JSON format');
            $table->timestamp('occurred_at')->useCurrent()->comment('Event timestamp');
            $table->boolean('published')->default(false)->comment('Is published to queue?');
            $table->timestamp('published_at')->nullable()->comment('Published timestamp');
            $table->timestamps();
            
            $table->index(['published', 'occurred_at']);
            $table->index(['aggregate_type', 'aggregate_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbox_messages');
    }
};
