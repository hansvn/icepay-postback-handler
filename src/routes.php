<?php

Route::group(array('prefix' => Config::get('icepay-postback::route_prefix')), function()
{
	Route::get(Config::get('icepay-postback::success_path'), function() {
		$result = Icepay::result();
		$status = "";

		//get the result and validate
		try {
			if($result->validate()){
				switch ($result->getStatus()) {
					case Hansvn\Icepay\StatusCode::OPEN: 
						// Close the cart 
						$status = "awaiting payment";
						break;
					case Hansvn\Icepay\StatusCode::SUCCESS:
						// Close the cart 
						$status = "completed";
						break;
					case Hansvn\Icepay\StatusCode::ERROR: 
						//Redirect to cart 
						$status = "error";
						break; 
				} 
			}
			else {
				//unable to validate
				//redirect to cart
				if($db_order->error && $db_order->error != "")
					return \Redirect::to($db_order->error)->with('OrderID', $db_order->id);
				else
					return \Redirect::to(Config::get('icepay-postback::error_redirect_path'))->with('OrderID', $db_order->id);
			}
		} catch (Exception $e) {
			\Log::error($e->getMessage());
		}

		//log the order in the DB
		try {
			$db_order = Hansvn\Icepay\DBOrder::find($result->getOrderID());
			$db_order->setResponse((array) $result->getResultData());
			$db_order->updateStatus($status);

			if($status == "error") {
				//redirect to cart
				if($db_order->error_path && $db_order->error_path != "")
					return \Redirect::to($db_order->error_path)->with('OrderID', $db_order->id);
				else
					return \Redirect::to(Config::get('icepay-postback::error_redirect_path'))->with('OrderID', $db_order->id);
			}

			if($db_order->success_path && $db_order->success_path != "")
				return \Redirect::to($db_order->success_path)->with('OrderID', $db_order->id);
			else
				return \Redirect::to(Config::get('icepay-postback::success_redirect_path'))->with('OrderID', $db_order->id);
		} catch (Exception $e) {
			\Log::error($e->getMessage());
			return \Redirect::to(Config::get('icepay-postback::error_redirect_path'))->with('message', $e->getMessage());
		}
	});

	Route::get(Config::get('icepay-postback::error_path'), function() {
		$error = Icepay::result();
		$status = "";

		//get the result and validate
		try {
			if($error->validate()){
				switch ($error->getStatus()) {
					case Hansvn\Icepay\StatusCode::OPEN: 
						// Close the cart 
						$status = "awaiting payment";
						break;
					case Hansvn\Icepay\StatusCode::SUCCESS:
						// Close the cart 
						$status = "completed";
						break;
					case Hansvn\Icepay\StatusCode::ERROR: 
						//Redirect to cart 
						$status = "error";
						break; 
				} 
			}
			else {
				//unable to validate
				//redirect to cart
				if($db_order->error_path && $db_order->error_path != "")
					return \Redirect::to($db_order->error_path)->with('OrderID', $db_order->id);
				else
					return \Redirect::to(Config::get('icepay-postback::error_redirect_path'))->with('OrderID', $db_order->id);
			}
		} catch (Exception $e) {
			\Log::error($e->getMessage());
		}

		//log the order in the DB
		try {
			$db_order = Hansvn\Icepay\DBOrder::find($error->getOrderID());

			if($db_order->error_path && $db_order->error_path != "")
				return \Redirect::to($db_order->error_path)->with('OrderID', $db_order->id);
			else
				return \Redirect::to(Config::get('icepay-postback::error_redirect_path'))->with('OrderID', $db_order->id);
		} catch (Exception $e) {
			\Log::error($e->getMessage());
			return \Redirect::to(Config::get('icepay-postback::error_redirect_path'))->with('message', $e->getMessage());
		}
	});

	Route::any(Config::get('icepay-postback::postback_path'), function() {
		$postback = Icepay::postback();
		$status = "";

		//get the postback and validate
		try { 
			if($postback->validate()) { 
				/* Actions based on statuscode */ 
				switch ($postback->getStatus()) {
					case Hansvn\Icepay\StatusCode::OPEN:
					$status = "awaiting payment";
					break;
					case Hansvn\Icepay\StatusCode::SUCCESS:
					$status = "completed";
					break;
					case Hansvn\Icepay\StatusCode::ERROR:
					$status = "error";
					break;
					case Hansvn\Icepay\StatusCode::REFUND:
					$status = "refund";
					break;
					case Hansvn\Icepay\StatusCode::CHARGEBACK:
					$status = "chargeback";
					break;
				} 
			} else {
				//unable to validate
				\Log::error($e->getMessage());
			}
		} catch (Exception $e){
			\Log::error($e->getMessage()); 
		}

		//log the order in the DB
		try {
			$db_order = Hansvn\Icepay\DBOrder::find($postback->getOrderID());
			$db_order->setResponse((array) $postback->getPostback());
			$db_order->updateStatus($status);
			
			return \Redirect::to(Config::get('icepay-postback::success_redirect_path'))->with('OrderID', $db_order->id);
		} catch (Exception $e) {
			\Log::error($e->getMessage());
			return \Redirect::to(Config::get('icepay-postback::error_redirect_path'))->with('message', $e->getMessage());
		}
	});

});