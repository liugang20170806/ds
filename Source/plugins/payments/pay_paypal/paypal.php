<?php
/**
 * @copyright Copyright(c) 2011 shop.gteamei.com
 * @file paypal.php
 * @brief 贝宝(外卡)接口
 * @author chendeshan
 * @date 2011-01-27
 * @version 0.6
 * @note
 */

 /**
 * @class paypal
 * @brief 贝宝(外卡)接口
 */
class paypal extends paymentPlugin
{
	//支付插件名称
    public $name = '贝宝支付';

	/**
	 * @see paymentplugin::getSubmitUrl()
	 */
	public function getSubmitUrl()
	{
		return 'https://www.paypal.com/cgi-bin/webscr';
	}

	//获取返回url
	public function getCallbackUrl()
	{
		return IUrl::getHost().IUrl::creatUrl("/ucenter/order");;
	}

	/**
	 * @see paymentplugin::notifyStop()
	 */
	public function notifyStop()
	{
		echo "success";
	}

	/**
	 * @see paymentplugin::callback()
	 */
	public function callback($callbackData,&$paymentId,&$money,&$message,&$orderNo){}

	/**
	 * @see paymentplugin::serverCallback()
	 */
	public function serverCallback($callbackData,&$paymentId,&$money,&$message,&$orderNo)
	{
		$UserName = Payment::getConfigParam($paymentId,'M_PartnerId');
		$IDcode   = Payment::getConfigParam($paymentId,'M_PartnerKey');

		$return                = array();
		$return['business']    = urldecode($UserName);
		$return['item_number'] = urldecode($callbackData['item_number']);
		$return['amount']      = urldecode($callbackData['payment_gross']);
		$return['return']      = urldecode($this->getCallbackUrl());
		$return['notify_url']  = urldecode($this->serverCallbackUrl);
		$md5Code               = $this->createMD5($return,$IDcode);

		//校验md5码 防止篡改数据
		if(urldecode($callbackData['custom']) == $md5Code)
		{
            switch($callbackData['payment_status'])
            {
                case 'Completed':
                {
					$orderNo  = $callbackData['item_number'];
					$money    = $callbackData['payment_gross'];

					//记录回执流水号
					if(isset($callbackData['tx']) && $callbackData['tx'])
					{
						$this->recordTradeNo($orderNo,$callbackData['tx']);
					}
                	return true;
                }
                break;

                default:
                {
                	return false;
                }
                break;
            }
		}
		else
		{
			$message = '校验码不正确';
		}
		return false;
	}

	/**
	 * @see paymentplugin::getSendData()
	 */
	public function getSendData($payment)
	{
    	$return = array();

		$UserName = $payment['M_PartnerId'];
		$IDcode   = $payment['M_PartnerKey'];

		$return['business']    = $UserName;
		$return['item_number'] = $payment['M_OrderNO'];
		$return['amount']      = $payment['M_Amount'];
		$return['return']      = $this->getCallbackUrl();
		$return['notify_url']  = $this->serverCallbackUrl;
		$return['custom']      = $this->createMD5($return,$IDcode);
		$return['item_name']   = $payment['R_Name'];
		$return['cmd']         = '_xclick';
		$return['charset']     = 'utf-8';

        return $return;
	}

    /**
    * @brief 生成md5防篡改码
	* @param array  要加密的原数据
	* @param string id密钥
	× @return string md5码
    */
    private function createMD5($rdata,$idCode)
    {
    	$rdataMD5   = '';
    	$rdataArray = array();

    	//让数组以键值进行排序
        ksort($rdata);
        reset($rdata);

    	foreach($rdata as $key => $val)
    	{
    		$rdataArray[] = $val;
    	}

    	$rdataMD5 = join('&',$rdataArray);
    	return md5($rdataMD5.$idCode);
    }
}