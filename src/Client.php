<?php

declare(strict_types=1);

namespace Bookingcom\Client;

use GuzzleHttp;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleRetry\GuzzleRetryMiddleware;
use Bookingcom\Client\Exception\InvalidArgumentException;
use Bookingcom\Client\Exception\RuntimeException;
use Bookingcom\Client\Result\ChangedHotels;
use Bookingcom\Client\Result\Cities;
use Bookingcom\Client\Result\FacilityTypes;
use Bookingcom\Client\Result\HotelDescriptions;
use Bookingcom\Client\Result\HotelFacilities;
use Bookingcom\Client\Result\HotelFacilityTypes;
use Bookingcom\Client\Result\Hotels;
use Bookingcom\Client\Result\HotelTypes;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Client
{
    private const BASE_URI = 'https://distribution-xml.booking.com/2.3/json/';
    private const CONNECT_TIMEOUT = 10;
    private const TIMEOUT = 30;

    private HttpClient $httpClient;

    public function __construct(string $username, string $password, ?LoggerInterface $logger = null)
    {
        if ('' === $username || '' === $password) {
            throw new InvalidArgumentException('Credentials cannot be blank.');
        }

        $this->httpClient = new HttpClient([
            'base_uri' => self::BASE_URI,
            'connect_timeout' => self::CONNECT_TIMEOUT,
            'timeout' => self::TIMEOUT,
            'auth' => [$username, $password],
            'handler' => self::createHandlerStack($logger ?: new NullLogger()),
        ]);
    }

    /**
     * Returns all hotel ids which has closed or data has changed since the given timestamp.
     *
     * @see https://developers.booking.com/api/technical.html?version=2.3#!/Static/changedHotels
     *
     * @param LastChange $lastChange
     *
     * @return PromiseInterface
     */
    public function getChangedHotels(LastChange $lastChange): PromiseInterface
    {
        return $this->getAsync('changedHotels', [
            'last_change' => $lastChange->toString(),
        ])->then(static function (ResponseInterface $response) {
            return ChangedHotels::fromResponse($response);
        });
    }

    /**
     * Returns the hotel and room data.
     *
     * @see https://developers.booking.com/api/technical.html?version=2.3#!/Static/hotels
     *
     * @param array $hotelIds
     *
     * @throws InvalidArgumentException
     *
     * @return PromiseInterface
     */
    public function getHotelsByIds(array $hotelIds): PromiseInterface
    {
        if (!$hotelIds) {
            throw new InvalidArgumentException('The list of hotel ids is empty.');
        }

        $query = [
            'hotel_ids' => implode(',', $hotelIds),
            'extras' => 'hotel_info,hotel_facilities,hotel_photos,room_info',
        ];

        return $this->getAsync('hotels', $query)->then(static function (ResponseInterface $response) {
            return Hotels::fromResponse($response);
        });
    }

    /**
     * Returns the hotel descriptions for the provided languages.
     *
     * @see https://developers.booking.com/api/technical.html?version=2.3#!/Static/hotels
     *
     * @param int   $hotelId
     * @param array $langIso2Codes
     *
     * @return PromiseInterface
     */
    public function getHotelDescriptions(int $hotelId, array $langIso2Codes): PromiseInterface
    {
        $promises = [];
        foreach ($langIso2Codes as $code) {
            $promises[$code] = $this->getAsync('hotels', [
                'hotel_ids' => $hotelId,
                'language' => $code,
                'extras' => 'hotel_description',
            ]);
        }

        return Promise\all($promises)->then(static function (array $responses) {
            return HotelDescriptions::fromResponses($responses);
        });
    }

    /**
     * Returns the hotel facilities for the provided languages.
     *
     * @see https://developers.booking.com/api/technical.html?version=2.3#!/Static/hotels
     *
     * @param int   $hotelId
     * @param array $langIso2Codes
     *
     * @return PromiseInterface
     */
    public function getHotelFacilities(int $hotelId, array $langIso2Codes): PromiseInterface
    {
        $promises = [];
        foreach ($langIso2Codes as $code) {
            $promises[$code] = $this->getAsync('hotels', [
                'hotel_ids' => $hotelId,
                'language' => $code,
                'extras' => 'hotel_facilities',
            ]);
        }

        return Promise\all($promises)->then(static function (array $responses) {
            return HotelFacilities::fromResponses($responses);
        });
    }

    /**
     * Returns hotel types names and their translations.
     *
     * @see https://developers.booking.com/api/technical.html?version=2.3#!/Static/hotelTypes
     *
     * @param array $langIso2Codes
     *
     * @return PromiseInterface
     */
    public function getHotelTypes(array $langIso2Codes): PromiseInterface
    {
        $query = [
            'languages' => implode(',', $langIso2Codes),
        ];

        return $this->getAsync('hotelTypes', $query)->then(static function (ResponseInterface $response) {
            return HotelTypes::fromResponse($response);
        });
    }

    /**
     * Returns hotel facility types names and their translations.
     *
     * @see https://developers.booking.com/api/technical.html?version=2.3#!/Static/hotelFacilityTypes
     *
     * @param array $langIso2Codes
     *
     * @return PromiseInterface
     */
    public function getHotelFacilityTypes(array $langIso2Codes): PromiseInterface
    {
        $query = [
            'languages' => implode(',', $langIso2Codes),
        ];

        return $this->getAsync('hotelFacilityTypes', $query)->then(static function (ResponseInterface $response) {
            return HotelFacilityTypes::fromResponse($response);
        });
    }

    /**
     * Returns facility types names and their translations.
     *
     * @see https://developers.booking.com/api/technical.html?version=2.3#!/Static/facilityTypes
     *
     * @param array $langIso2Codes
     *
     * @return PromiseInterface
     */
    public function getFacilityTypes(array $langIso2Codes): PromiseInterface
    {
        $query = [
            'languages' => implode(',', $langIso2Codes),
        ];

        return $this->getAsync('facilityTypes', $query)->then(static function (ResponseInterface $response) {
            return FacilityTypes::fromResponse($response);
        });
    }

    /**
     * Returns a list of cities where Booking.com offers hotels.
     *
     * @see https://developers.booking.com/api/technical.html?version=2.3#!/Static/cities
     *
     * @param array $langIso2Codes
     * @param int   $limit
     * @param int   $offset
     *
     * @return PromiseInterface
     */
    public function getCities(array $langIso2Codes, int $limit, int $offset = 0): PromiseInterface
    {
        $query = [
            'languages' => implode(',', $langIso2Codes),
            'rows' => $limit,
            'offset' => $offset,
            'extras' => 'location',
        ];

        return $this->getAsync('cities', $query)->then(static function (ResponseInterface $response) {
            return Cities::fromResponse($response);
        });
    }

    private function getAsync(string $action, array $query = []): PromiseInterface
    {
        return $this->httpClient->requestAsync('GET', $action, ['query' => $query])
            ->otherwise(static function (\Throwable $e) {
                if (!$e instanceof RequestException || !$response = $e->getResponse()) {
                    throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
                }

                try {
                    $result = GuzzleHttp\json_decode((string) $response->getBody(), true);
                } catch (\InvalidArgumentException $jsonError) {
                    // ignore non-json responses errors, e.g. 401 Unauthorized
                }

                throw new RuntimeException($result['errors'][0]['message'] ?? $e->getMessage(), $e->getCode(), $e);
            });
    }

    private static function createHandlerStack(LoggerInterface $logger): HandlerStack
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push(GuzzleRetryMiddleware::factory(['retry_on_timeout' => true]));

        // convert "Connection reset by peer" error to ConnectException (errno 28),
        // so that it can be caught and retried by GuzzleRetryMiddleware
        $handlerStack->push(static function (callable $handler) {
            return static function (RequestInterface $request, array $options) use ($handler) {
                return $handler($request, $options)->otherwise(static function (\Throwable $reason) use ($request) {
                    if (false === strpos($reason->getMessage(), 'reset by peer')) {
                        return Promise\rejection_for($reason);
                    }

                    throw new ConnectException($reason->getMessage(), $request, $reason, ['errno' => 28]);
                });
            };
        });

        $handlerStack->push(Middleware::log($logger, new MessageFormatter(MessageFormatter::DEBUG)));

        return $handlerStack;
    }
}
