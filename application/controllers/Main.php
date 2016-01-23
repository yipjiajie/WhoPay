<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('receipt_model');
        $this->load->model('user_model');
        $this->load->model('item_model');
        $this->load->model('user_item_model');
    }

	// new or join button
	public function index()
	{
		$this->load->view('home');
	}

	// manual create. submits name and items to createManual()
	public function create()
	{
		$this->load->view('create');
	}

	// ocr
	public function ocr()
	{
		$this->load->view('ocr');
	}

	//join
	public function join()
	{
		$this->load->view('join');
	}

	// lobby
	public function receipt()
	{
		$input = $this->input->post();

		if (isset($input['receiptCode'])) {
			// join receipt
			$receiptCode = $this->input->post('receiptCode');
			$receiptId = $this->receipt_model->getByCode($receiptCode)->id;
		} else {
			// create receipt
			$receiptCode = substr(uniqid(), -6);
			$receiptId = $this->receipt_model->insert(['code' => $receiptCode]);
		}
		$this->session->set_userdata('receiptId', $receiptId);
		
		// generate user session
		$name = $input['name']; // user name
		$userId = $this->user_model->insert(['name' => $name, 'receipt_id' => $receiptId]);
		$this->session->set_userdata('userId', $userId);

		if (!isset($input['receiptCode'])) {	
			// generate items
			$items = $input['items']; // array of item names
			$itemcosts = $input['itemcosts']; // array of item prices
			$itemArray = []; // array of item name and prices
			foreach ($items as $i => $item) {
				$itemArray[] = [
					'name' => $items[$i],
					'cost' => $itemcosts[$i],
					'receipt_id' => $receiptId,
				];
			}
			$this->item_model->insertBatch($itemArray);
		}

		var_dump($receiptId, $userId);

		$this->load->view('receipt');
	}

	// result
	public function result($receipt_id)
	{
		$this->load->view('result');
	}
}
