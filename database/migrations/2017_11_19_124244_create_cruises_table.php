<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCruisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cruises', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('session_id')->unsigned();
			$table->string('code_auto', 20);
			$table->string('name_auto', 50);
			
			$table->double('weith_sum', 18, 2)->default(0.00)->unsigned();
			$table->double('summa_sum', 18, 2)->default(0.00)->unsigned();
			$table->double('kmdirect', 18, 2)->default(0.00)->unsigned();
			$table->unsignedSmallInteger('status_auto')->default(0);
			$table->string('comment', 200);
			
			$table->index('session_id');
			$table->index('status_auto');
			
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
        Schema::dropIfExists('cruises');
    }
}
