<?php declare(strict_types = 1);

/**
 * AfterMessageHandledEvent.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:RedisDbExchangePlugin!
 * @subpackage     Events
 * @since          0.2.0
 *
 * @date           09.10.21
 */

namespace FastyBird\RedisDbExchangePlugin\Events;

use Symfony\Contracts\EventDispatcher;

/**
 * After message handled event
 *
 * @package        FastyBird:RedisDbExchangePlugin!
 * @subpackage     Events
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class AfterMessageHandledEvent extends EventDispatcher\Event
{

	/** @var string */
	private string $payload;

	public function __construct(string $payload)
	{
		$this->payload = $payload;
	}

	/**
	 * @return string
	 */
	public function getPayload(): string
	{
		return $this->payload;
	}

}
