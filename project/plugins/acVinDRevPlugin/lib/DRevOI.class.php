<?php
class DRevOI
{
	public $drev;
	public $context;
	public $regions;


	public function __construct(DRev $drev, $context = null) {
		if (!$drev->validation_odg) {
			throw new sfException('DRev non validée');
		}
		$this->drev = $drev;
		$this->context = ($context) ? $context : sfContext::getInstance();
		$this->regions = sfConfig::get('app_oi_regions');
	}

	public function send()
	{
		if($this->regions){
			$regionSended = array();
			foreach ($this->regions as $region => $regionOpt) {
				if(!count($this->drev->declaration->getProduits($region))){
					continue;
				}
				$regionSended[] = $this->sendXml($region, $regionOpt);
			}
		}else{
			$this->sendXml();
		}
		if(!$this->regions || count($regionSended)){
			 $this->drev->add('envoi_oi', date('c'));
			 $this->drev->save();
		}
		return $this->drev;
	}

	public function sendXml($region = null, $regionOpt = null){
		$domain_action = ($regionOpt)? $regionOpt['domain_action'] : sfConfig::get('app_oi_domain_action');
		$url_http = ($regionOpt)? $regionOpt['url_http'] : sfConfig::get('app_oi_url_http');
		$headers = array(
			"Content-Type: text/xml;charset=UTF-8",
			"SOAPAction: http://".$domain_action."/CreationDrev"
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_http);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getXml($region));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		if ($output === false || $info['http_code'] != 200) {
			$result = "No cURL data returned (HTTP Code : ". $info['http_code']. ")";
			if (curl_error($ch)) {
				$result .= "\n". curl_error($ch);
			}
			throw new sfException($result);
		} else {
			$stderr = fopen("php://stderr", "w");
			fwrite($stderr, "Retour envoi oi : ".$output);
		}
		curl_close($ch);
	}

	protected function getXml($region = null) {
		return $this->context->getController()->getAction('drev', 'main')->getPartial('drev/xml', array('drev' => $this->drev, 'region' => $region));
	}
}
