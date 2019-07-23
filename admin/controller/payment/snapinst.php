<?php
class ControllerPaymentSnapinst extends Controller {

  private $error = array();

  public function index() {
    $this->load->language('payment/snapinst');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('setting/setting');
    $this->load->model('localisation/order_status');
	  $this->config->get('curency');


    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      $this->model_setting_setting->editSetting('snapinst', $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
    }

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->error['display_name'])) {
      $data['error_display_name'] = $this->error['display_name'];
    } else {
      $data['error_display_name'] = '';
    }
    
    if (isset($this->error['merchant_id'])) {
      $data['error_merchant'] = $this->error['merchant_id'];
    } else {
      $data['error_merchant'] = '';
    }

    if (isset($this->error['server_key'])) {
      $data['error_server_key'] = $this->error['server_key'];
    } else {
      $data['error_server_key'] = '';
    }

    if (isset($this->error['client_key'])) {
      $data['error_client_key'] = $this->error['client_key'];
    } else {
      $data['error_client_key'] = '';
    }

    if (isset($this->error['min_txn'])) {
      $data['error_min_txn'] = $this->error['min_txn'];
    } else {
      $data['error_min_txn'] = '';
    }

    $language_entries = array(

      'heading_title',
      'text_enabled',
      'text_disabled',
      'text_yes',
      'text_live',
      'text_successful',
      'text_fail',
      'text_all_zones',
	    'text_edit',

      'entry_api_version',
      'entry_environment',
      'entry_merchant_id',
      'entry_server_key',
      'entry_client_key',
      'entry_order_status',
      'entry_geo_zone',
      'entry_status',
      'entry_sort_order',
      'entry_3d_secure',
      'entry_payment_type',
      'entry_enable_bank_installment',
      'entry_currency_conversion',
      'entry_display_name',
      'entry_min_txn',
      'entry_custom_field',

      'help_min',
      'help_custom_field',
      
      'button_save',
      'button_cancel'
      );

    foreach ($language_entries as $language_entry) {
      $data[$language_entry] = $this->language->get($language_entry);
    }

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      'separator' => false
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_payment'),
      'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      'separator' => ' :: '
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('payment/snapinst', 'token=' . $this->session->data['token'], 'SSL'),
      'separator' => ' :: '
    );

    $data['action'] = $this->url->link('payment/snapinst', 'token=' . $this->session->data['token'], 'SSL');

    $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

    $inputs = array(
      'snapinst_environment',
      'snapinst_server_key',
      'snapinst_client_key',
      'snapinst_merchant_id',
      'snapinst_test',
      'snapinst_total',
      'snapinst_order_status_id',
      'snapinst_geo_zone_id',
      'snapinst_sort_order',
      'snapinst_3d_secure',
      'snapinst_payment_type',
      'snapinst_installment_terms',
      'snapinst_currency_conversion',
      'snapinst_status',
      'midtrans_snapinst_success_mapping',
      'midtrans_snapinst_failure_mapping',
      'midtrans_snapinst_challenge_mapping',
      'snapinst_display_name',
      'snapinst_enabled_payments',
      'snapinst_sanitization',
      'snapinst_min_txn',
      'snapinst_custom_field1',
      'snapinst_custom_field2',
      'snapinst_custom_field3'
    );

    foreach ($inputs as $input) {
      if (isset($this->request->post[$input])) {
        $data[$input] = $this->request->post[$input];
      } else {
        $data[$input] = $this->config->get($input);
      }
    }

    $this->load->model('localisation/order_status');

    $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

    $this->load->model('localisation/geo_zone');

    $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

    $this->template = 'payment/snapinst.tpl';
	$data['column_left'] = $this->load->controller('common/column_left');
	$data['header'] = $this->load->controller('common/header');
	$data['footer'] = $this->load->controller('common/footer');
	
	
	if(!$this->currency->has('IDR'))
	{
		$data['curr'] = true;
	}
	else
	{
		$data['curr'] = false;
	}
	$this->response->setOutput($this->load->view('payment/snapinst.tpl',$data));
	
  }

  protected function validate() {

    if (!$this->user->hasPermission('modify', 'payment/snapinst')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    // check for empty values
    if (!$this->request->post['snapinst_display_name']) {
      $this->error['display_name'] = $this->language->get('error_display_name');
    }
        
    // check for empty values
    if (!$this->request->post['snapinst_client_key']) {
      $this->error['client_key'] = $this->language->get('error_client_key');
    }

    // check for empty values
    if (!$this->request->post['snapinst_server_key']) {
      $this->error['server_key'] = $this->language->get('error_server_key');
    }

    // default values
    if (!$this->request->post['snapinst_environment'])
      $this->request->post['snapinst_environment'] = 1;

      // check for empty values
    if (!$this->request->post['snapinst_merchant_id']) {
       $this->error['merchant_id'] = $this->language->get('error_merchant');
    }

      // check for empty values
    if (!$this->request->post['snapinst_min_txn']) {
       $this->error['min_txn'] = $this->language->get('error_min_txn');
    }
    // currency conversion to IDR
    if (!$this->request->post['snapinst_currency_conversion'] && !$this->currency->has('IDR'))
      $this->error['currency_conversion'] = $this->language->get('error_currency_conversion');

    return !$this->error;

  }
}
?>