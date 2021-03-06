<?php declare(strict_types = 1);

namespace Tests\Cases;

use DateTime;
use FastyBird\DateTimeFactory;
use FastyBird\Metadata\Entities as MetadataEntities;
use FastyBird\Metadata\Types as MetadataTypes;
use FastyBird\RedisDbExchangePlugin\Client;
use FastyBird\RedisDbExchangePlugin\Publishers;
use Mockery;
use Nette\Utils;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class PublisherTest extends BaseMockeryTestCase
{

	public function testPublish(): void
	{
		$now = new DateTime();

		$client = Mockery::mock(Client\IClient::class);
		$client
			->shouldReceive('publish')
			->withArgs(function ($data) use ($now): bool {
				Assert::same(Utils\Json::encode([
					'sender_id'   => 'redis_client_identifier',
					'source'      => MetadataTypes\ModuleSourceType::SOURCE_MODULE_DEVICES,
					'routing_key' => MetadataTypes\RoutingKeyType::ROUTE_DEVICE_ENTITY_UPDATED,
					'created'     => $now->format(DATE_ATOM),
					'data'        => [
						'action'         => MetadataTypes\PropertyActionType::ACTION_SET,
						'property'       => '60d754c2-4590-4eff-af1e-5c45f4234c7b',
						'expected_value' => 10,
						'device'         => '593397b2-fd40-4da2-a66a-3687ca50761b',
						'channel'        => '06a64596-ca03-478b-ad1e-4f53731e66a5',
					],
				]), $data);

				return true;
			})
			->andReturn(true)
			->times(1)
			->getMock()
			->shouldReceive('getIdentifier')
			->withNoArgs()
			->andReturn('redis_client_identifier')
			->times(1);

		$dateTimeFactory = Mockery::mock(DateTimeFactory\DateTimeFactory::class);
		$dateTimeFactory
			->shouldReceive('getNow')
			->withNoArgs()
			->andReturn($now)
			->times(1);

		$publisher = new Publishers\Publisher($client, $dateTimeFactory);

		$publisher->publish(
			MetadataTypes\ModuleSourceType::get(MetadataTypes\ModuleSourceType::SOURCE_MODULE_DEVICES),
			MetadataTypes\RoutingKeyType::get(MetadataTypes\RoutingKeyType::ROUTE_DEVICE_ENTITY_UPDATED),
			new MetadataEntities\Actions\ActionChannelPropertyEntity(
				MetadataTypes\PropertyActionType::ACTION_SET,
				'593397b2-fd40-4da2-a66a-3687ca50761b',
				'06a64596-ca03-478b-ad1e-4f53731e66a5',
				'60d754c2-4590-4eff-af1e-5c45f4234c7b',
				10,
				20,
				true
			),
		);
	}

}

$test_case = new PublisherTest();
$test_case->run();
