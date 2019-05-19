<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_plans', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('auhtor_id')->unsigned();
			$table->string('text')->default('Описание задачи');
			$table->boolean('status')->default(false)->index();
            $table->timestamps();
			
			$table->index('auhtor_id');
			
			$table->foreign('auhtor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_plans');
    }
}
