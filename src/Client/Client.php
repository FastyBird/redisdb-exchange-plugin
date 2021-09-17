<?php declare(strict_types = 1);

/**
 * Client.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:RedisDbExchangePlugin!
 * @subpackage     Client
 * @since          0.1.0
 *
 * @date           17.09.21
 */

namespace FastyBird\RedisDbExchangePlugin\Client;

use FastyBird\RedisDbExchangePlugin\Connections;
use Nette;
use Predis;

/**
 * Redis database client
 *
 * @package        FastyBird:RedisDbExchangePlugin!
 * @subpackage     Client
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class Client implements IClient
{

	use Nette\SmartObject;

	/** @var Connections\IConnection */
	private Connections\IConnection $connection;

	/** @var Predis\Client<mixed> */
	private Predis\Client $redis;

	public function __construct(
		Connections\IConnection $connection
	) {
		$this->connection = $connection;

		$options = [
			'scheme'   => 'tcp',
			'host'     => $connection->getHost(),
			'port'     => $connection->getPort(),
			'database' => $connection->getDatabase(),
		];

		if ($connection->getUsername() !== null) {
			$options['username'] = $connection->getUsername();
		}

		if ($connection->getPassword() !== null) {
			$options['password'] = $connection->getPassword();
		}

		$this->redis = new Predis\Client($options);
	}

	/**
	 * {@inheritDoc}
	 */
	public function publish(string $channel, string $content): bool
	{
		$response = $this->redis->publish($channel, $content);

		return $response === 1;
	}

}
