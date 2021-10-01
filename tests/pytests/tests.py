#     Copyright 2021. FastyBird s.r.o.
#
#     Licensed under the Apache License, Version 2.0 (the "License");
#     you may not use this file except in compliance with the License.
#     You may obtain a copy of the License at
#
#         http://www.apache.org/licenses/LICENSE-2.0
#
#     Unless required by applicable law or agreed to in writing, software
#     distributed under the License is distributed on an "AS IS" BASIS,
#     WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#     See the License for the specific language governing permissions and
#     limitations under the License.

# Test dependencies
import datetime
import json
import unittest
import uuid
from unittest.mock import patch, Mock
from typing import Dict, List
from modules_metadata.types import ModuleOrigin
from modules_metadata.routing import RoutingKey

from redisdb_exchange_plugin.publisher import Publisher


class TestPublisher(unittest.TestCase):
    @patch('redis.Redis.publish')
    def test_publish(self, mock_redis_publish):
        identifier: uuid.UUID = uuid.uuid4()

        publisher = Publisher({}, identifier.__str__())

        message = {
            "routing_key": RoutingKey(RoutingKey.DEVICES_ENTITY_UPDATED).value,
            "origin": ModuleOrigin(ModuleOrigin.DEVICES_MODULE).value,
            "sender_id": identifier.__str__(),
            "data": {
                "key_one": "value_one",
                "key_two": "value_two",
            },
        }

        mock_redis_publish.return_value = True

        publisher.publish(
            origin=ModuleOrigin(ModuleOrigin.DEVICES_MODULE),
            routing_key=RoutingKey(RoutingKey.DEVICES_ENTITY_UPDATED),
            data={
                "key_one": "value_one",
                "key_two": "value_two",
            },
        )

        mock_redis_publish.assert_called_with("fb_exchange", json.dumps(message))


if __name__ == '__main__':
    unittest.main()