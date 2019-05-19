<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('session_id')->unsigned();
			
			$table->string('number', 20);
			$table->string('code', 20);
			$table->string('koment', 200);
			$table->double('weith', 18, 2)->default(0.00)->unsigned();
			$table->double('summa', 18, 2)->default(0.00)->unsigned();
			$table->string('razvoz', 30);
			$table->integer('auto_id')->default(0)->unsigned(); // № авто
			$table->integer('cruise_id')->default(0)->unsigned(); // № рейса
			
			$table->index('session_id');
			$table->index('cruise_id');
			$table->unique(['number', 'session_id']);
			
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
        Schema::dropIfExists('orders');
    }
}
