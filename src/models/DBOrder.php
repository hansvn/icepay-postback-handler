<?php namespace Hansvn\Icepay;

class DBOrder extends \Eloquent {
	public $timestamps = true;
	public $icepayObject = null;

	protected $table = 'icepay_orders';
	protected $fillable = [];

	public function __construct() {
		parent::__construct();
		$this->icepayObject = "not initialized";
	}

	public function responses() {
		return $this->hasMany('DBOrderResponse', 'orderID', 'id');
	}

	public function setResponse(array $data) {
		$response = DBOrderResponse::create($data);

		if($response->paymentMethod == "") {
			$response->paymentMethod = array_key_exists('paymentMethod', $data) ? $data['paymentMethod'] : \Input::get('PaymentMethod') ?: "";
			$response->save();
		}
	}

	public function updateStatus($status) {
		if(is_string($status)) {
			if($this->status == "pending") {
				$this->status = $status;
				$this->save();
			}
			elseif($this->status != "completed") {
				$this->status = $status;
				$this->save();
			}
			elseif($this->status == "completed") {
				throw new \Exception("Couldn't update the status from $this->status to $status", 500);
			}
		}
		else throw new \Exception("Argument 1 passed to updateStatus() must be a string", 500);
	}

	public static function boot()
	{
		parent::boot();

		DBOrder::created(function($order) {   
			$order->icepayObject = \Icepay::paymentObject();
			$order->icepayObject->setOrderID($order->id);
		});
	}
}
