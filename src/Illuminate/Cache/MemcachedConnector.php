<?php namespace Illuminate\Cache;

use Memcached;

class MemcachedConnector {

	/**
	 * Create a new Memcached connection.
	 *
	 * @param  array  $servers
	 * @return \Memcached
	 *
	 * @throws \RuntimeException
	 */
	public function connect(array $servers)
	{
		$memcached = $this->getMemcached();

		// For each server in the array, we'll just extract the configuration and add
		// the server to the Memcached connection. Once we have added all of these
		// servers we'll verify the connection is successful and return it back.
		foreach ($servers as $server)
		{

			// use timeout from config or default of 100ms
			$timeout = (empty($server['timeout']) == false)? 
				floatval($server['timeout']) / 1000 : 100 / 1000; 

			// actually test server availability by opening a port
			@$fp = fsockopen($server['host'], $server['port'],  $errno,  $errstr,  $timeout);

			if (is_resource($fp))
			{
			  // connection successful
			  fclose($fp);

				$memcached->addServer(
					$server['host'], $server['port'], $server['weight']
				);
			}

			
		}

		if ($memcached->getVersion() === false)
		{
			throw new \RuntimeException("Could not establish Memcached connection.");
		}

		return $memcached;
	}

	/**
	 * Get a new Memcached instance.
	 *
	 * @return \Memcached
	 */
	protected function getMemcached()
	{
		return new Memcached;
	}

}
