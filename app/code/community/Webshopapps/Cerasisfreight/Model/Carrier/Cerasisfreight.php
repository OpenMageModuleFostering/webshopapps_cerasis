<?php
/* Cerasisfreight Freight Shipping
 *
* @category   Webshopapps
* @package    Webshopapps_Cerasisfreight
* @copyright  Copyright (c) 2012 Zowta Ltd (http://www.webshopapps.com)
* @license    http://www.webshopapps.com/license/license.txt - Commercial license
*/


class Webshopapps_Cerasisfreight_Model_Carrier_Cerasisfreight
extends Webshopapps_Wsafreightcommon_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{

	protected $_code = 'cerasisfreight';

	protected $_modName = 'Webshopapps_Cerasisfreight';

    protected $_prodRateQuoteServiceWsdl = 'http://cerasis.ltlship.net/webservices/soap/freight/v3/rating.asmx?WSDL';
    protected $_devRateQuoteServiceWsdl = 'http://dev.ltlship.net/webservices/soap/freight/v3/rating.asmx?WSDL';

    const DEV_RATE_URL = 'http://dev.ltlship.net/webservices/soap/freight/v3/rating.asmx';
    const DEV_SHIP_URL = 'http://dev.ltlship.net/webservices/soap/freight/v3/shipping.asmx';
    const DEV_TRACK_URL = 'http://dev.ltlship.net/webservices/soap/freight/v3/requestinformation.asmx';

    const PROD_RATE_URL = 'http://cerasis.ltlship.net/webservices/soap/freight/v3/rating.asmx';
    const PROD_SHIP_URL = 'http://cerasis.ltlship.net/webservices/soap/freight/v3/shipping.asmx';
    const PROD_TRACK_URL = 'http://cerasis.ltlship.net/webservices/soap/freight/v3/requestinformation.asmx';

    const SHIP = 1;
    const TRACK = 2;
    const RATE = 3;

    public function setRequest(Mage_Shipping_Model_Rate_Request $request)
	{
		$r = $this->setBaseRequest($request);

        $this->setAccessRequest($r);

		$this->_rawRequest = $r;

		return $this;
	}

    private function setAccessRequest(&$r){
        $r->setShipperID($this->getConfigData('shipper_id'));
        $r->setUsername($this->getConfigData('user_name'));
        $r->setPassword($this->getConfigData('password'));
        $r->setAccessKey($this->getConfigData('access_key'));
        $r->setBillingType($this->getConfigData('billing_type'));
    }

	protected function _formRateRequest()
	{
		$r = $this->_rawRequest;

        $accSettings=$r->getAccessories();

        $ratesOuterRequest = new stdClass();

        $ratesOuterRequest->AccessRequest = array(
            'ShipperID'	    => $r->getShipperID(),
            'Username'		=> $r->getUsername(),
            'Password'      => $r->getPassword(),
            'AccessKey'     => $r->getAccessKey(),

		);
        $ratesOuterRequest->Version = '1';

        $ratesInnerRequest  = new stdClass();
        $ratesInnerRequest->Direction = 'Outbound';
        $ratesInnerRequest->BillingType = $r->getBillingType();
        $ratesInnerRequest->Carrier = 'Rateshop';
        $ratesInnerRequest->ShipDate = $currentDate = date("c",strtotime(date('Ymd',time()) . ' +1 day'));
        $ratesInnerRequest->CODShipment = false;
        $ratesInnerRequest->CODShipmentCharge = 0;
        $ratesInnerRequest->CODAmount = 0;
        $ratesInnerRequest->TotalPallets = 0;

        $destResidential = false;
        $origResidential = false;

        foreach($accSettings as $setting){
            switch($setting){
                case 'RES':
                    $destResidential = true;
                    break;
                case 'RES_ORIGIN':
                    $origResidential = true;
                    break;
            }
        }

        $ratesInnerRequest->Origin = array (
        	'State' 		=> $r->getOrigRegionCode(),
            'PostalCode'  	=> $r->getOrigPostal(),
            'Country' 		=> $r->getOrigCountry(),
            'ResidentialDelivery' => $origResidential,
            'AddrType'      => 1,  // ignore, internal
        );
        $ratesInnerRequest->Destination = array (
            'State' 		=> $r->getDestRegionCode(),
            'PostalCode'  	=> $r->getDestPostal(),
            'Country' 		=> $r->getDestCountry(),
            'ResidentialDelivery' => $destResidential,
            'AddrType'      => 1,  // ignore, internal
        );


        $ratesInnerRequest->Details = $this->_getLineItems();
        $ratesInnerRequest->Accessorials = $this->_getAccessories($r);
        $ratesOuterRequest->Request = $ratesInnerRequest;

        $ratesRequest['RateRequest'] = $ratesOuterRequest;

		if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvY2VyYXNpc2ZyZWlnaHQvc2hpcF9vbmNl',
				'Z3JpenpseWJlYXI=','Y2FycmllcnMvY2VyYXNpc2ZyZWlnaHQvc2VyaWFs')) {
		    return;
		}


		if ($this->_debug) {
			Mage::helper('wsacommon/log')->postNotice('cerasisfreight','Request',$ratesRequest);
		}

        return $ratesRequest;
	}

    protected function _getQuotes()
    {
        $ratesRequest = $this->_formRateRequest();
        $requestString = serialize($ratesRequest);
        $response = $this->_getCachedQuotes($requestString);
        $debugData = array('request' => $ratesRequest);
        if ($response === null) {
            try {
                $client = $this->_createRateSoapClient();
                $response = $client->RateShipment($ratesRequest);
                $this->_setCachedQuotes($requestString, serialize($response));
                $debugData['result'] = $response;
            } catch (Exception $e) {
                $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                Mage::logException($e);
            }
        } else {
            $response = unserialize($response);
            $debugData['result'] = $response;
        }
        if($this->_debug)
        {
            //Mage::helper('wsalogger/log')->postInfo('cerasisfreight','Request XML',$client->__getLastRequest());
            Mage::helper('wsalogger/log')->postInfo('cerasisfreight','Response',$debugData);
        }
        return $this->_parseRateResponse($ratesRequest,$response);
    }

	public function isCityRequired()
	{
		return true;
	}

	protected function _parseRateResponse($ratesRequest,$response)
	{

		$costArr = array();
		$priceArr = array();
		$quoteId=''; // quote id not available

		if (is_object($response)) {
			$resp = $response->RateResponse;
			if (isset($resp) && is_object($resp->Error) &&
                is_object($resp->Carriers) && isset($resp->Carriers->Carrier)) {

				$allowedMethods = explode(",", $this->getConfigData('allowed_methods'));

                $carrierArr = $resp->Carriers->Carrier;

				if (is_array($carrierArr)) {
					foreach ($carrierArr as $carrier) {

                        $serviceName = (string)$carrier->CarrierSCAC;

						if (in_array($serviceName, $allowedMethods)) {

							$amount = (string)$carrier->ShipmentRate;
							$costArr[$serviceName]  = $amount;
							$priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
						}
					}
					asort($priceArr);
				} else {
                    $carrier = $resp->Carriers->Carrier;
                    $serviceName = (string)$carrier->CarrierSCAC;

                    if (in_array($serviceName, $allowedMethods)) {

                        $amount = (string)$carrier->ShipmentRate;
                        $costArr[$serviceName]  = $amount;
                        $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                    }
				}
			}
		}

        if ($this->getConfigFlag('show_cheapest')) {
            $priceArr = $this->_getCheapest($priceArr);
        }

		return $this->getResultSet($priceArr,$ratesRequest,$response,'');

	}

    protected function _getCheapest($priceArr) {

        if (count($priceArr)<1) {return $priceArr;}

        $cheapest = -1;
        foreach ($priceArr as $serviceName=>$possRate) {
            if ($cheapest==-1 || $possRate<$cheapest) {
                $cheapestService = $serviceName;
                $cheapest = $possRate;
            }
        }

        $newPriceArr[$cheapestService]= $priceArr[$cheapestService];

        return $newPriceArr;

    }



	public function getCode($type, $code='')
	{
		$codes = array(
                'billing_type' =>array(
                        'Prepaid'  => Mage::helper('shipping')->__('Prepaid'),
                        'PPA'      => Mage::helper('shipping')->__('Prepaid & Add'),

                ),
				'method'=>array(
                    'PYLE'=> Mage::helper('shipping')->__('A. Duie Pyle, Inc.'),
                    'AACT'=> Mage::helper('shipping')->__('AAA Cooper Transportation'),
                    'AGCE'=> Mage::helper('shipping')->__('ACI Motor Freight'),
                    'ASAP'=> Mage::helper('shipping')->__('ASAP Delivery'),
                    'AXRN'=> Mage::helper('shipping')->__('Atchesons Express, Inc.'),
                    'AVRT'=> Mage::helper('shipping')->__('Averitt Express, Inc.'),
                    'BEAV'=> Mage::helper('shipping')->__('Beaver Express'),
                    'BLLF'=> Mage::helper('shipping')->__('Bullet Freight Systems'),
                    'BEXT'=> Mage::helper('shipping')->__('Bullocks Express Transportation'),
                    'CAPC'=> Mage::helper('shipping')->__('Capital Couriers, Inc.'),
                    'CTSQ'=> Mage::helper('shipping')->__('Cargo Transportation Services, Inc.'),
                    'CGOR'=> Mage::helper('shipping')->__('Cargo Transporters'),
                    'CDNK'=> Mage::helper('shipping')->__('Celadon'),
                    'CENF'=> Mage::helper('shipping')->__('Central Freight Lines, Inc.'),
                    'CTII'=> Mage::helper('shipping')->__('Central Transport International. Inc.'),
                    'CHEE'=> Mage::helper('shipping')->__('Cheetah'),
                    'CSKL'=> Mage::helper('shipping')->__('Chris Truck Line'),
                    'COAS'=> Mage::helper('shipping')->__('Coast to Coast Transportation'),
                    'CMMS'=> Mage::helper('shipping')->__('Command Transportation LLC'),
                    'CNWY'=> Mage::helper('shipping')->__('Con-Way Freight'),
                    'CTDS'=> Mage::helper('shipping')->__('Crosstown Delivery Service'),
                    'DYLT'=> Mage::helper('shipping')->__('Daylight Transport'),
                    'DAFG'=> Mage::helper('shipping')->__('Dayton Freight Lines'),
                    'DPHE'=> Mage::helper('shipping')->__('Dependable Highway Express'),
                    'DHL'=> Mage::helper('shipping')->__('DHL'),
                    'DHRN'=> Mage::helper('shipping')->__('Dohrn Transfer Company'),
                    'DOUG'=> Mage::helper('shipping')->__('Douglas & Sons'),
                    'DTSB'=> Mage::helper('shipping')->__('DTS'),
                    'ENCO'=> Mage::helper('shipping')->__('Encore'),
                    'EXLA'=> Mage::helper('shipping')->__('Estes Express Lines'),
                    'FXNL'=> Mage::helper('shipping')->__('FedExFreightEconomy'),
                    'ARFW'=> Mage::helper('shipping')->__('FedExFreightPriority'),
                    'FINE'=> Mage::helper('shipping')->__('Fineline Express'),
                    'FCAB'=> Mage::helper('shipping')->__('Freight Cab'),
                    'HMES'=> Mage::helper('shipping')->__('Holland'),
                    'HOLL'=> Mage::helper('shipping')->__('Holland Logistics'),
                    'HTMS'=> Mage::helper('shipping')->__('Holland Transportation Management Services'),
                    'HOTA'=> Mage::helper('shipping')->__('Hot Services, Inc.'),
                    'HUSR'=> Mage::helper('shipping')->__('Houser Transport, Inc.'),
                    'HVHT'=> Mage::helper('shipping')->__('HVH Transportation, Inc.'),
                    'JKTI'=> Mage::helper('shipping')->__('JK Transport'),
                    'KEXP'=> Mage::helper('shipping')->__('Kings Express'),
                    'LMTS'=> Mage::helper('shipping')->__('L&M Transportation'),
                    'LKVL'=> Mage::helper('shipping')->__('Lakeville Motor Express'),
                    'LAXV'=> Mage::helper('shipping')->__('Land Air Express of New England'),
                    'LETL'=> Mage::helper('shipping')->__('Lewis Transportation Systems, Inc.'),
                    'MVTL'=> Mage::helper('shipping')->__('Mark VII Truckload'),
                    'MERC'=> Mage::helper('shipping')->__('Mercer'),
                    'MSXN'=> Mage::helper('shipping')->__('Mid-States Express'),
                    'MIDW'=> Mage::helper('shipping')->__('Midwest Motor Express'),
                    'MLXP'=> Mage::helper('shipping')->__('Milan Express'),
                    'MTRG'=> Mage::helper('shipping')->__('Motor Cargo'),
                    'MQDS'=> Mage::helper('shipping')->__('M-Quick Delivery Service'),
                    'NMTF'=> Mage::helper('shipping')->__('N&M Transfer Co., Inc.'),
                    'NEBT'=> Mage::helper('shipping')->__('Nebraska Transport Company'),
                    'NEMF'=> Mage::helper('shipping')->__('New England Motor Freight'),
                    'NPME'=> Mage::helper('shipping')->__('New Penn Motor Express'),
                    'OAKH'=> Mage::helper('shipping')->__('Oak Harbor Freight Lines'),
                    'ODFL'=> Mage::helper('shipping')->__('Old Dominion Freight Line, Inc.'),
                    'PMLI'=> Mage::helper('shipping')->__('Pace Motor Lines'),
                    'PATT'=> Mage::helper('shipping')->__('Patterson Motor Freight'),
                    'PITD'=> Mage::helper('shipping')->__('Pitt Ohio Express'),
                    'PRIC'=> Mage::helper('shipping')->__('Price Truck Line, Inc.'),
                    'RLCA'=> Mage::helper('shipping')->__('R&L Carriers'),
                    'RETL'=> Mage::helper('shipping')->__('Reddaway'),
                    'RDFS'=> Mage::helper('shipping')->__('Roadrunner Transportation Services'),
                    'RRVT'=> Mage::helper('shipping')->__('Root River Valley Transfer, Inc.'),
                    'MLLR'=> Mage::helper('shipping')->__('Roy Miller Transportation'),
                    'RPMI'=> Mage::helper('shipping')->__('RPM Transportation, Inc.'),
                    'SAIA'=> Mage::helper('shipping')->__('Saia, Inc.'),
                    'SEFL'=> Mage::helper('shipping')->__('Southeastern Freight Lines'),
                    'SMTL'=> Mage::helper('shipping')->__('Southwestern Motor Transport'),
                    'STDF'=> Mage::helper('shipping')->__('Standard Forwarding'),
                    'TBST'=> Mage::helper('shipping')->__('TBS Trucking'),
                    'TYAR'=> Mage::helper('shipping')->__('Three Way Transfer of Arkansas'),
                    'TQL5'=> Mage::helper('shipping')->__('Total Quality Logistics, LLC'),
                    'TSDL'=> Mage::helper('shipping')->__('Transdel'),
                    'TSXW'=> Mage::helper('shipping')->__('Tri-State Express'),
                    'USRD'=> Mage::helper('shipping')->__('U.S. Road Freight Express'),
                    'UPGF'=> Mage::helper('shipping')->__('UPS Freight'),
                    'UPPN'=> Mage::helper('shipping')->__('US Special Delivery'),
                    'USFB'=> Mage::helper('shipping')->__('USF Bestway'),
                    'CEXP'=> Mage::helper('shipping')->__('Velocity Express'),
                    'VIKN'=> Mage::helper('shipping')->__('Viking Freight Lines'),
                    'VITR'=> Mage::helper('shipping')->__('Vitran'),
                    'WARD'=> Mage::helper('shipping')->__('Ward Trucking'),
                    'WERN'=> Mage::helper('shipping')->__('Werner'),
                    'WTVA'=> Mage::helper('shipping')->__('Wilson Trucking'),
                    'WWMF'=> Mage::helper('shipping')->__('Wiseway Transportation Services'),
                    'XGSI'=> Mage::helper('shipping')->__('Xpress Global Systems'),
                    'YFEE'=> Mage::helper('shipping')->__('Yellow Transportation Exact Express'),
                    'RDWY'=> Mage::helper('shipping')->__('YRC Freight'),
                    'YFSY'=> Mage::helper('shipping')->__('YRC, Inc.')
				),
		);

		if (!isset($codes[$type])) {
			return false;
		} elseif (''===$code) {
			return $codes[$type];
		}

		if (!isset($codes[$type][$code])) {
			return false;
		} else {
			return $codes[$type][$code];
		}
	}

    /**
     * Get selected Accessorials and add to request
     */
    protected function _getAccessories($r) {


        if (!Mage::helper('wsafreightcommon')->getUseLiveAccessories()) {
            return;
        }
        $accessorials = new StdClass;
        $accInners = array();

        $accSettings=$r->getAccessories();
        foreach ($accSettings as $acc) { // Add accessorials to the XML Request
            switch ($acc) {
                case 'RES':
                    $accInners[] = array('AccessorialName' => 'Residential/NonCommercial Delivery');
                    break;
                case 'LIFT_ORIGIN':
                    $accInners[] = array('AccessorialName' => 'Lift Gate at Origin');
                    break;
                case 'LIFT':
                    $accInners[] = array('AccessorialName' => 'Lift Gate at Destination');
                    break;
                case 'INSIDE':
                    $accInners[] = array('AccessorialName' => 'Inside Delivery');
                    break;
                case 'NOTIFY':
                    $accInners[] = array('AccessorialName' => 'Notification');
                    break;

            }
        }

        if (empty($accInners)) {
            return;
        }

        foreach($accInners as $accessorial){
            $accessorials->Accessorial[] = $accessorial;
        }

        return $accessorials;
    }



    protected function _getLineItems() {
        $r = $this->_rawRequest;
        $details = new StdClass;

        foreach ($this->getLineItems($r->getIgnoreFreeItems()) as $class =>$weight){
            $detail = array (
                'Class'	    => $class,
                'Quantity'  => '1',
                'Weight'	=> $weight,
                'Hazmat'    => false,
            );
            $details->Detail[] = $detail;
        }

        return $details;
    }


	private function _array_to_objecttree($array) {
	  if (is_numeric(key($array))) {
	    foreach ($array as $key => $value) {
	      $array[$key] = $this->_array_to_objecttree($value);
	    }
	    return $array;
	  }
	  $Object = new stdClass;
	  foreach ($array as $key => $value) {
	    if (is_array($value)) {
	      $Object->$key = $this->_array_to_objecttree($value);
	    }  else {
	      $Object->$key = $value;
	    }
	  }
	  return $Object;
	}

    /**
     * Create rate soap client
     *
     * @return SoapClient
     */
    protected function _createRateSoapClient()
    {
        if($this->getConfigFlag('sandbox_mode')){
            return $this->_createSoapClient($this->_devRateQuoteServiceWsdl,$this->_debug);
        }
        else{
            return $this->_createSoapClient($this->_prodRateQuoteServiceWsdl,$this->_debug);
        }

    }

    protected function _createSoapClient($wsdl, $trace = false, $type=self::RATE)
    {
        $client = new SoapClient($wsdl, array('trace' => $trace));

        switch($type){
            case self::RATE:
                $client->__setLocation($this->getConfigFlag('sandbox_mode')
                    ? self::DEV_RATE_URL
                    : self::PROD_RATE_URL
                ); break;
            case self::TRACK:
                $client->__setLocation($this->getConfigFlag('sandbox_mode')
                        ? self::DEV_TRACK_URL
                        : self::PROD_TRACK_URL
                ); break;
            case self::SHIP:
                $client->__setLocation($this->getConfigFlag('sandbox_mode')
                        ? self::DEV_SHIP_URL
                        : self::PROD_SHIP_URL
                ); break;
            default:
                $client->__setLocation($this->getConfigFlag('sandbox_mode')
                        ? self::DEV_RATE_URL
                        : self::PROD_RATE_URL
                ); break;
        }

        return $client;
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param Varien_Object $request
     * @return Varien_Object
     */
    /*
    protected function _doShipmentRequest(Varien_Object $request){
        $this->_prepareShipmentRequest($request);
        $result = new Varien_Object();
        $client = $this->_createSoapClient($this->_prodRateQuoteServiceWsdl,$this->_debug, self::SHIP);
        $requestClient = $this->_formShipmentRequest($request);
        $response = $client->processShipment($requestClient);

        if ($response) {
            $shippingLabelContent = $response->getLabelInfo;
            $trackingNumber = $response->getTrackingNumber;
            $result->setShippingLabelContent($shippingLabelContent);
            $result->setTrackingNumber($trackingNumber);
            $debugData = array('request' => $client->__getLastRequest(), 'result' => $client->__getLastResponse());
            $this->_debug($debugData);
        } else {
            $debugData = array(
                'request' => $client->__getLastRequest(),
                'result' => array(
                    'error' => '',
                    'code' => '',
                    'xml' => $client->__getLastResponse()
                )
            );
            if (is_array($response->Notifications)) {
                foreach ($response->Notifications as $notification) {
                    $debugData['result']['code'] .= $notification->Code . '; ';
                    $debugData['result']['error'] .= $notification->Message . '; ';
                }
            } else {
                $debugData['result']['code'] = $response->Notifications->Code . ' ';
                $debugData['result']['error'] = $response->Notifications->Message . ' ';
            }
            $this->_debug($debugData);
            $result->setErrors($debugData['result']['error']);
        }
        $result->setGatewayResponse($client->__getLastResponse());

        return $result;
    }*/
}
