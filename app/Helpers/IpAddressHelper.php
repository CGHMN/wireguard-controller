<?php

namespace App\Helpers;

use Exception;

class IpAddressHelper
{
	public static function netmask2cidrmask($net_mask): string
	{
		$long = ip2long($net_mask);
		$base = ip2long('255.255.255.255');
		return 32 - log(($long ^ $base) + 1, 2);
	}

	public static function cidrmask2netmask(int $cidr_mask): string
	{
		return long2ip(-1 << (32 - $cidr_mask));
	}

	/**
	 * Gets the subnet mask from a CIDR notation
	 * @param string $cidr CIDR notation of a network address
	 * @return string Network mask in dot notation
	 */
	public static function mask_from_cidr(string $cidr): string
	{
		if (!preg_match('~/(\d+)$~', $cidr, $matches)) {
			throw new Exception("Missing CIDR mask in {$cidr}");
		}

		$cidr_mask = intval($matches[1]);

		return self::cidrmask2netmask($cidr_mask);
	}

	/**
	 * Gets the network address of a given CIDR notation
	 * @param mixed $cidr CIDR notation of a network address
	 * @return string Network address
	 */
	public static function network_address_from_cidr(string $cidr, bool $with_cidr_mask = true): string
	{
		$cidr_parts = explode('/', $cidr);

		return long2ip(
			ip2long($cidr_parts[0]) & ip2long(self::mask_from_cidr($cidr))
		) . ($with_cidr_mask? "/{$cidr_parts[1]}" : '');
	}

	public static function broadcast_address_from_cidr(string $cidr, bool $with_cidr_mask = true): string
	{
		$cidr_parts = explode('/', $cidr);

		return long2ip(
			ip2long($cidr_parts[0]) | ~ip2long(self::mask_from_cidr($cidr))
		).($with_cidr_mask ? "/{$cidr_parts[1]}" : '');
	}

	/**
	 * Returns an IP address without its CIDR netmask
	 * 
	 * @param mixed $cidr CIDR address
	 * @return void
	 */
	public static function strip_cidrmask($cidr): string
	{
		return explode('/', $cidr)[0];
	}
}