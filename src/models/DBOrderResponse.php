<?php namespace Hansvn\Icepay;

class DBOrderResponse extends \Eloquent {
	public $timestamps = true;

	protected $table = 'icepay_order_responses';
	protected $fillable = ['status', 'statusCode', 'merchant', 'orderID', 'paymentID', 'reference', 'transactionID', 'checksum', 'paymentMethod'];

	public function order() {
		return $this->belongsTo('DBOrder');
	}

}
