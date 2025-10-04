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
		if (! preg_match('~/(\d+)$~', $cidr, $matches)) {
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
		).($with_cidr_mask ? "/{$cidr_parts[1]}" : '');
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

	/**
	 * Tries to find the routed subnet CIDR matching to a give tunnel IP
	 * @param string $tunnel_ip Tunnel IP either as standalone IPv4 address or in CIDR notation
	 * @param string $routed_subnet_cidr Base routed subnet IP address in CIDR notation
	 * @return ?string Matching routed subnet for the tunnel in CIDR notation
	 */
	public static function routed_subnet_from_tunnel_ip(string $tunnel_ip, string $routed_subnet_cidr, int $routed_subnet_size = 24): string
	{
		preg_match('~(\d+)\.(\d+)\.(\d+)\.(\d+)/?(\d+)?~', $routed_subnet_cidr, $ro);
		preg_match('~(\d+)\.(\d+)\.(\d+)\.(\d+)/?(\d+)?~', $tunnel_ip, $to);

		return "{$ro[1]}.{$ro[2]}.{$to[4]}.{$ro[4]}/{$routed_subnet_size}";
	}
}