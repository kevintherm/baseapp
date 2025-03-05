<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('content_views', function (Blueprint $table) {
            $table->id();
            $table->string('viewable_type');
            $table->unsignedInteger('viewable_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('ip')->nullable();
            $table->integer('count')->default(0);
            $table->integer('watchtime')->comment('IN MINUTES')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_views');
    }
};
