<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_sessions', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('author_id')->unsigned();
			$table->string('author_comment', 200);
			$table->timestamp('session_todate')->useCurrent();
			$table->unsignedSmallInteger('coefficient')->default(0);
			$table->unsignedSmallInteger('session_teritorial')->default(0);
			
			$table->integer('import_count_orders')->unsigned()->default(0);
			$table->integer('import_count_outlets')->unsigned()->default(0);
			$table->double('import_count_wieght', 15, 2)->unsigned()->default(0);
			$table->double('import_count_money', 15, 2)->unsigned()->default(0);
			$table->integer('import_count_ups')->unsigned()->default(0);
			$table->unsignedSmallInteger('import_count_cars')->default(0);
			$table->integer('import_count_cars_wieght')->unsigned()->default(0);
			
			$table->integer('current_count_orders')->unsigned()->default(0);
			$table->double('current_count_wieght', 15, 2)->unsigned()->default(0);
			
            $table->timestamps();
			
			$table->index('author_id');
			$table->index('session_todate');
			$table->index('session_teritorial');
			
			$table->foreign('author_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_sessions');
    }
}
