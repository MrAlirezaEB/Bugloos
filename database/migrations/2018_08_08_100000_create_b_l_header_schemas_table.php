<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBLHeaderSchemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('b_l_header_schemas', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('content');
            $table->boolean('dynamic');
            $table->string('sort_by')->nullable();
            $table->integer('count_per_page')->default(-1);
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
        Schema::dropIfExists('b_l_header_schemas');
    }
}