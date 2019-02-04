<?php
class ControllerDashboardSeller extends Controller {
  public function index() {
    $this->load->language('dashboard/seller');
 
    $data['heading_title'] = $this->language->get('heading_title');
     
    $data['column_customer_id'] = $this->language->get('column_customer_id');
    $data['column_customer_name'] = $this->language->get('column_customer_name');
    $data['column_customer_email'] = $this->language->get('column_customer_email');
    $data['column_date_added'] = $this->language->get('column_date_added');
    $data['text_no_results'] = $this->language->get('text_no_results');
 
    $data['seller'] = array();
 
    $this->load->model('report/seller');
    $results = $this->model_report_seller->getRecentCustomers();
 
    foreach ($results as $result) {
      $data['seller'][] = array(
        'customer_id' => $result['customer_id'],
        'name' => $result['firstname'] . '&nbsp;' . $result['lastname'],
        'email' => $result['email'],
        'date_added' => $result['date_added']
      );
    }
	$this->response->setOutput($this->load->view('dashboard/seller', $data));
     //$this->load->view('dashboard/seller.twig', $data);
  }
}
