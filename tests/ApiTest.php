<?php


namespace App\Tests;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        if (!isset($this->client)) {
            $this->client = new Client([
                'base_uri' => 'https://flag-rest-api.herokuapp.com/api/',
                'defaults' => [
                    'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json']
                ]
            ]);
        }
    }

    /**
     * POST movie test.
     *
     * @return mixed
     */
    public function testMovieCanBePosted()
    {
        try {
            $response = $this->client->request('POST', 'movies', [
                'json' => ['title' => 'Fight Club', 'year' => 1999, 'genre' => 'drama']
            ]);
        } catch (GuzzleException $e) {
            $this->fail('Request failed.');
        }
        $response = json_decode($response->getBody(), true);
        $responseStatus = $response['status'];
        $responseBody = $response['response'];

        $this->assertTrue($responseStatus['success']);
        $this->assertEquals(200, $responseStatus['status_code']);

        $this->assertArrayHasKey('id', $responseBody);
        $this->assertEquals('Fight Club', $responseBody['title']);
        $this->assertEquals(1999, $responseBody['year']);
        $this->assertEquals('drama', $responseBody['genre']);

        return $response['response'];
    }

    /**
     * GET movie by id.
     * Uses the same item from previous test.
     *
     * @depends testMovieCanBePosted
     * @param array $movie
     * @return array
     */
    public function testGetMovieWithId(array $movie): array
    {
        try {
            $response = $this->client->request('GET', 'movie/'.$movie['id']);
        } catch (GuzzleException $e) {
            $this->fail('Request failed.');
        }
        $response = json_decode($response->getBody(), true);
        $responseStatus = $response['status'];
        $responseBody = $response['response'];

        $this->assertTrue($responseStatus['success']);
        $this->assertEquals(200, $responseStatus['status_code']);

        $this->assertEquals($movie['title'], $responseBody['title']);
        $this->assertEquals($movie['year'], $responseBody['year']);
        $this->assertEquals($movie['genre'], $responseBody['genre']);

        return $movie;
    }

    /**
     * PUT movie test.
     *
     * @depends testGetMovieWithId
     * @param array $movie
     * @return array
     */
    public function testMovieCabBeChanged(array $movie): array
    {
        try {
            $response = $this->client->request('PUT', 'movie/'.$movie['id'], [
                'json' => ['title' => 'Forrest Gump', 'year' => 1994]
            ]);
        } catch (GuzzleException $e) {
            $this->fail('Request failed.');
        }
        $response = json_decode($response->getBody(), true);
        $responseBody = $response['response'];

        $this->assertTrue($response['status']['success']);
        $this->assertEquals('Forrest Gump', $responseBody['title']);
        $this->assertEquals(1994, $responseBody['year']);

        return $movie;
    }

    /**
     * DELETE movie test.
     * Removes the item that was created in the previous test.
     *
     * @depends testMovieCabBeChanged
     * @param array $movie
     */
    public function testMovieCanBeDeleted(array $movie)
    {
        try {
            $response = $this->client->request('DELETE', 'movie/' . $movie['id']);
        } catch (GuzzleException $e) {
            $this->fail('Request failed.');
        }
        $response = json_decode($response->getBody(), true);
        $responseStatus = $response['status'];

        $this->assertTrue($responseStatus['success']);
        $this->assertEquals(200, $responseStatus['status_code']);
        $this->assertEquals('The item was deleted successfully.', $response['status']['status_message']);
    }

    /**
     * GET movie test with non-existed identifier.
     */
    public function test404MovieNotFound()
    {
        try {
            $response = $this->client->request('GET', 'movie/0');
        } catch (GuzzleException $e) {
            $this->fail('Request failed.');
        }
        $response = json_decode($response->getBody(), true);

        $this->assertFalse($response['status']['success']);
        $this->assertEquals(404, $response['status']['status_code']);
    }

    /**
     * POST movie test with invalid request data.
     */
    public function testMovieInvalidRequestData()
    {
        try {
            $response = $this->client->request('POST', 'movies', [
                'json' => ['title' => 'Fight Club']
            ]);
        } catch (GuzzleException $e) {
            $this->fail('Request failed.');
        }
        $response = json_decode($response->getBody(), true);

        $this->assertFalse($response['status']['success']);
        $this->assertEquals(422, $response['status']['status_code']);
        $this->assertEquals('Invalid request data.', $response['status']['status_message']);
    }
}
