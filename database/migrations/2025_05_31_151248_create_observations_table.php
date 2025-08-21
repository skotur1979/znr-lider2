<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->date('incident_date');
            $table->string('location');
            $table->string('item');
            $table->string('potential_incident_type');
            $table->text('action')->nullable();
            $table->string('responsible')->nullable();
            $table->date('target_date')->nullable();
            $table->enum('status', ['Not started', 'In progress', 'Complete'])->default('Not started');
            $table->text('comments')->nullable();
            $table->string('picture_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observations');
    }
};
