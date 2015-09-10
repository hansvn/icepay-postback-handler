<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIcepayTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('icepay_orders', function($table)
		{
			$table->increments('id'); //order id (OrderID)
			$table->string('status')->default('pending');

			$table->string('success_path')->nullable();
			$table->string('error_path')->nullable();

			$table->timestamps();
		});

		Schema::create('icepay_order_responses', function($table)
		{
			$table->increments('id');

			//icepay data
			$table->string('status');
			$table->string('statusCode');
			$table->string('merchant');
			$table->string('orderID');
			$table->string('paymentID');
			$table->text('reference');
			$table->string('transactionID');
			$table->string('checksum');
			$table->string('paymentMethod');

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
		Schema::drop('icepay_orders');
		Schema::drop('icepay_order_responses');
	}

}
