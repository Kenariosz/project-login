<?php
/**
 * @author: Kenariosz
 * @date  : 2016-10-19
 */

namespace App\Services\Authentication\Utilities;

/**
 * Class IPAddress
 * @package App\Services\Authentication\Utilities
 */
class IPAddress {

	/**
	 * @param $ip
	 *
	 * @return string
	 */
	public static function getCIDR($ip)
	{
		return static::CIDRConverter($ip);
	}

	/**
	 * Convert ip to CIDR.
	 *
	 * @param $ip
	 *
	 * @return string
	 */
	private static function CIDRConverter($ip)
	{
		$start = strtok($ip, "/");
		$n = 3 - substr_count($ip, ".");
		if($n > 0)
		{
			for($i = $n; $i > 0; $i--)
			{
				$start .= ".0";
			}
		}
		$bits1 = str_pad(decbin(ip2long($start)), 32, "0", STR_PAD_LEFT);
		$ip = (1 << (32 - substr(strstr($ip, "/"), 1))) - 1;
		$bits2 = str_pad(decbin($ip), 32, "0", STR_PAD_LEFT);
		$final = "";
		for($i = 0; $i < 32; $i++)
		{
			if($bits1[$i] == $bits2[$i])
			{
				$final .= $bits1[$i];
			}
			if($bits1[$i] == 1 and $bits2[$i] == 0)
			{
				$final .= $bits1[$i];
			}
			if($bits1[$i] == 0 and $bits2[$i] == 1)
			{
				$final .= $bits2[$i];
			}
		}

		return long2ip(bindec($final));
	}

}