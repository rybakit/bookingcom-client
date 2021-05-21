# Booking.com API client

## Usage example

```php
use Bookingcom\Client\Client;
use Bookingcom\Client\Result\ChangedHotels;

$client = new Client('<username>', '<password>');
$client->getChangedHotels('-2 hours')->then(function (ChangedHotels $changedHotels) {
    foreach ($changedHotels->getClosedHotelIds() as $hotelId) {
        // do something
    }
    
    // or
    foreach ($changedHotels->getChanges() as $hotelId => $changes) {
        // do something
    }
})->wait();
```

To send multiple requests asynchronously:

```php
use GuzzleHttp\Promise;
use Bookingcom\Client\Client;

$client = new Client('<username>', '<password>');

$results = Promise\all([
    'descriptions' => $client->getHotelDescriptions(42, ['en', 'nl']),
    'facilities' => $client->getHotelFacilities(42, ['en', 'nl']),
])->wait();

foreach ($results as $type => $result) {
    // ...
}
```


## Tests

Before running tests, copy [phpunit.xml.dist](phpunit.xml.dist) to `phpunit.xml` 
and set `BOOKINGCOM_API_USERNAME` and `BOOKINGCOM_API_PASSWORD` environment variables.

Then run the tests:

```bash
vendor/bin/phpunit
```


## License

The library is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.
