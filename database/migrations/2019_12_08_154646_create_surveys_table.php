<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->longText('description');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->boolean('is_mystery_brand')->default(false);
            $table->enum('status', ['draft', 'disable', 'in progress']);
            $table->integer('brand_id');
            $table->foreign('brand_id')->references('id')->on('organizations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surveys');
    }
}
