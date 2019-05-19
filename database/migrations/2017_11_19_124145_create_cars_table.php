<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('session_id')->unsigned();
			$table->string('code', 10)->default('not');
			$table->string('name', 50)->default('Авто');
			$table->string('nomer', 30)->default('not');
			$table->integer('tonag')->default(2000)->unsigned();
			
			$table->double('cur_weith', 18, 2)->default(0.00)->unsigned();
			$table->double('cur_summa', 18, 2)->default(0.00)->unsigned();
			$table->unsignedSmallInteger('cur_count')->default(0);
			
			$table->double('total_weith', 18, 2)->default(0.00)->unsigned();
			$table->double('total_summa', 18, 2)->default(0.00)->unsigned();
			$table->unsignedSmallInteger('total_count')->default(0);
			
			$table->index('session_id');
			$table->unique(['code', 'session_id']);
			
			$table->foreign('session_id')->references('id')->on('order_sessions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars');
    }
}
