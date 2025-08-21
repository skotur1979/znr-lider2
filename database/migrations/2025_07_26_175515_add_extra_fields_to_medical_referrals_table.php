<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medical_referrals', function (Blueprint $table) {
        $table->string('name_of_parents')->nullable();
        $table->string('law_reference1')->nullable();
        $table->string('last_exam_reference1')->nullable();
        $table->string('last_exam_reference2')->nullable();
        $table->string('last_exam_reference3')->nullable();

        $table->boolean('lifting_enabled')->nullable();
        $table->string('lifting_weight')->nullable();
        $table->boolean('carrying_enabled')->nullable();
        $table->string('carrying_weight')->nullable();
        $table->boolean('pushing_enabled')->nullable();
        $table->string('pushing_weight')->nullable();

        $table->json('job_characteristics')->nullable();

        $table->string('chemcial_substances')->nullable();
        $table->string('biological_hazards')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('medical_referrals', function (Blueprint $table) {
        $table->dropColumn([
            'name_of_parents',
            'law_reference1',
            'special_conditions',
            'last_exam_reference1',
            'last_exam_reference2',
            'last_exam_reference3',
            'lifting_enabled',
            'lifting_weight',
            'carrying_enabled',
            'carrying_weight',
            'pushing_enabled',
            'pushing_weight',
            'job_characteristics',
            'hazards',
            'chemcial_substances',
            'biological_hazards',
        ]);
    });
}
};
