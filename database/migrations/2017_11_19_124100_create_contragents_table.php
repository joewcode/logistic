<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContragentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contragents', function (Blueprint $table) {
            $table->increments('id');
			$table->unsignedSmallInteger('teritorial_id')->default(0);
			$table->string('code', 20);
			$table->string('name', 200)->default('Отсутствует');
			$table->string('addresses', 200)->default('Отсутствует');
			$table->string('nachalo', 10)->default('Нет');
			$table->string('konec', 10)->default('Нет');
			$table->decimal('shirota', 9, 6)->default(0.000000);
			$table->decimal('dolgota', 9, 6)->default(0.000000);
			
			$table->index('teritorial_id');
			$table->unique(['code', 'teritorial_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contragents');
    }
}
