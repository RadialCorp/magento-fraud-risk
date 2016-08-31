<?php
/**
 * Copyright (c) 2013-2016 Radial, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright   Copyright (c) 2013-2016 Radial, Inc. (http://www.radial.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Radial_Eb2cFraud_Helper_Http extends Mage_Core_Helper_Http
{
    const COOKIES_DELIMITER = ';';
    // Relative path where scripts are stored
    const JSC_JS_PATH = 'radial_eb2cfraud';
    // Form field name that will contain the name of the randomly selected JSC
    // form field. Used to find the generated JSC data in the POST data
    const JSC_FIELD_NAME = 'eb2cszyvl';

    /** @var Mage_Core_Model_Cookie */
    protected $_cookie;
    /** @var string */
    protected $_jscUrl;

    /**
     * inject dependencies
     * @param array
     */
    public function __construct(array $args = [])
    {
        list($this->_cookie) =
            $this->_checkTypes(
                $this->_nullCoalesce('cookie', $args, Mage::getSingleton('core/cookie'))
            );
        $this->_jscUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS, true) . self::JSC_JS_PATH;
    }

    /**
     * return $ar[$key] if it exists otherwise return $default
     * @param  string
     * @param  array
     * @param  mixed
     * @return mixed
     */
    protected function _nullCoalesce($key, array $ar, $default)
    {
        return isset($ar[$key]) ? $ar[$key] : $default;
    }

    /**
     * ensure correct types
     * @param Mage_Core_Model_Cookie
     * @return array
     */
    protected function _checkTypes(Mage_Core_Model_Cookie $cookie)
    {
        return [$cookie];
    }

    public function getHttpHost($clean = true)
    {
	return $this->_getHttpCleanValue('HTTP_HOST', $clean);
    }

    public function getHttpOrigin($clean = true)
    {
	return $this->_getHttpCleanValue('HTTP_ORIGIN', $clean);
    }

    public function getHttpXPrototypeVersion($clean = true )
    {
	return $this->_getHttpCleanValue('HTTP_X_PROTOTYPE_VERSION', $clean);
    }

    public function getHttpXRequestedWith($clean = true )
    {
	return $this->_getHttpCleanValue('HTTP_X_REQUESTED_WITH', $clean);
    }

    public function getHttpUserAgent($clean = true )
    {
	return $this->_getHttpCleanValue('HTTP_USER_AGENT', $clean);
    }
  
    public function getHttpCookie($clean = true )
    {
	return $this->_getHttpCleanValue('HTTP_COOKIE', $clean);
    }

    public function getHttpXForwardedProto($clean = true )
    {
	return $this->_getHttpCleanValue('HTTP_X_FORWARDED_PROTO', $clean);
    }

    public function getHttpXForwardedFor($clean = true )
    {
	return $this->_getHttpCleanValue('HTTP_X_FORWARDED_FOR', $clean);
    }

    public function getHttpContentType($clean = true )
    {
	return $this->_getHttpCleanValue('HTTP_CONTENT_TYPE', $clean);
    }

    /**
     * Retrieve HTTP Accept header
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
     * @param bool $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpAccept($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_ACCEPT', $clean);
    }

    /**
     * Retrieve HTTP Accept-Encoding header
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3
     * @param bool $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpAcceptEncoding($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_ACCEPT_ENCODING', $clean);
    }

    /**
     * Retrieve HTTP Accept-Language header
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
     * @param bool $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpAcceptLanguage($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_ACCEPT_LANGUAGE', $clean);
    }

    /**
     * Retrieve HTTP Connection header
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.10
     * @param bool $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpConnection($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_CONNECTION', $clean);
    }

    public function getHttpReferrer($clean = true )
    {
	return $this->_getHttpCleanValue('HTTP_REFERER', $clean);
    }

    public function getHttpAcceptCharset($clean = true)
    {
	return $this->_getHttpCleanValue('HTTP_ACCEPT_CHARSET', $clean);
    }

    /**
     * Retrieve the remote client's host name
     *
     * @return string
     */
    public function getRemoteHost()
    {
        return gethostbyaddr($this->getRemoteAddr(false));
    }

    /**
     * Retrieve Client Remote Address
     *
     * @param bool $ipToLong converting IP to long format
     * @return string IPv4|long
     */
    public function getRemoteAddr($ipToLong = false)
    {
        if (is_null($this->_remoteAddr)) {
            $headers = $this->getRemoteAddrHeaders();
            foreach ($headers as $var) {
                if ($this->_getRequest()->getServer($var, false)) {
                        $ip_array=explode(',',$this->_getRequest()->getServer($var, false));

			foreach( $ip_array as $ipAddress )
			{
				//Some routers are inserting characters after the IP, trim it. -RK
				preg_match_all('/\d{1,3}/',$ipAddress,$r);
				$this->_remoteAddr = $r[0][0].'.'.$r[0][1].'.'.$r[0][2].'.'.$r[0][3];

				if( $this->_remoteAddr )
				{
					if( !$this->ip_is_private($this->_remoteAddr))
					{
						break;
					} else {
						$this->_remoteAddr = '';
					}	
				}
			}	
                }

		if( $this->_remoteAddr )
		{
			break;
		}
            }

            if (!$this->_remoteAddr) {
                $this->_remoteAddr = $this->_getRequest()->getServer('REMOTE_ADDR');
            }
        }

        if (!$this->_remoteAddr) {
            return false;
        }

        return $ipToLong ? inet_pton($this->_remoteAddr) : $this->_remoteAddr;
    }

    /**
     * Determine if IP Address is of a private subnet
     *
     * @param IPv4 IP Address
     * @return bool
     */
    private function ip_is_private($ip)
    {
        $privateAddresses = [ '10.0.0.0|10.255.255.255', '172.16.0.0|172.31.255.255', '192.168.0.0|192.168.255.255', '169.254.0.0|169.254.255.255', '127.0.0.0|127.255.255.255' ];
        $long_ip = ip2long($ip);
        if($long_ip != -1)
        {
            foreach($privateAddresses as $pri_addr)
            {
                list($start, $end) = explode('|', $pri_addr);
                // IF IS PRIVATE
                if($long_ip >= ip2long($start) && $long_ip <= ip2long($end))
                {
                        return true;
                }
            }
        }
        return false;
    }

    /**
     * return a string representation of the given cookie array
     * @return string
     */
    public function getCookiesString()
    {
        $cookies = $this->_cookie->get();
        return implode(self::COOKIES_DELIMITER, array_map(function ($key, $value) {
            return "$key=$value";
        }, array_keys($cookies), $cookies));
    }

    /**
     * get url to our JavaScript
     * @return string
     */
    public function getJscUrl()
    {
        return $this->_jscUrl;
    }

    /**
     * Find the generated JS data from the given request's POST data. This uses
     * a known form field in the POST data, self::JSC_FIELD_NAME, to find the
     * form field populated by the JS collector. As the form field populated is
     * selected at random, this mapping is the only way to find the data
     * populated by the collector.
     * @param  Mage_Core_Controller_Request_Http
     * @return string
     */
    public function getJavaScriptFraudData()
    {
        $request = $this->_getRequest();
        return $request->getPost($request->getPost(static::JSC_FIELD_NAME, ''), '');
    }

    /**
     * Get all header data.
     *
     * @return array
     */
     public function getHeaderData()
     {
	$headers = array();
	foreach ($_SERVER as $name => $value) {
		if (substr($name, 0, 5) == 'HTTP_') {
			$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
		}
	}
	return $headers;
     }
}
