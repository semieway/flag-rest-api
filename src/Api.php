<?php


namespace App;


use mysql_xdevapi\Exception;

class Api
{
    /**
     * Url path.
     * @var string
     */
    private string $path;

    /**
     * Url query.
     * @var string
     */
    private string $query;

    /**
     * Http request method.
     * @var string
     */
    private string $method;

    /**
     * PDO database connection.
     * @var Database
     */
    private Database $db;

    /**
     * POST data array.
     * @var array
     */
    private array $postData;

    /**
     * Success status indicator.
     * @var bool
     */
    private bool $success = true;

    /**
     * Response status code.
     * @var int
     */
    private int $statusCode = 200;

    /**
     * Response status message.
     * @var string
     */
    private string $statusMessage = 'Success';

    /**
     * Api constructor.
     *
     * @param string $requestUri
     * @param string $method
     * @param Database $db
     * @param array $post
     */
    public function __construct(string $requestUri, string $method, Database $db, array $post)
    {
        $url = parse_url($requestUri);

        $this->setPath($url['path']);
        $this->setQuery($url['query'] ?? '');
        $this->setMethod($method);
        $this->setDb($db);
        $this->setPostData($post);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery(string $query): void
    {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @return Database
     */
    public function getDb(): Database
    {
        return $this->db;
    }

    /**
     * @param Database $db
     */
    public function setDb(Database $db): void
    {
        $this->db = $db;
    }

    /**
     * @return array
     */
    public function getPostData(): array
    {
        return $this->postData;
    }

    /**
     * @param array $postData
     */
    public function setPostData(array $postData): void
    {
        $this->postData = $postData;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     */
    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return string
     */
    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    /**
     * @param string $statusMessage
     */
    public function setStatusMessage(string $statusMessage): void
    {
        $this->statusMessage = $statusMessage;
    }

    /**
     * @return array
     */
    public function getResponseStatus(): array
    {
        return [
            'success' => $this->isSuccess(),
            'status_code' => $this->getStatusCode(),
            'status_message' => $this->getStatusMessage()
        ];
    }

    /**
     * Returns json encoded response.
     *
     * @return string
     */
    public function getJsonResponse()
    {
        return json_encode($this->getResponse());
    }

    /**
     * Return response array.
     *
     * @return array
     */
    public function getResponse()
    {
        $response = [];
        $response['status'] = $this->getResponseStatus();
        $response['response'] = $this->generateResponse();

        return $response;
    }

    /**
     * Generates response.
     *
     * @return array
     * @throws \Exception
     */
    public function generateResponse(): array
    {
        $response['response'] = '';
        $isCollection = preg_match('/^\/api\/movies/', $this->getPath());
        $isElement = preg_match('/^\/api\/movie\/\d/', $this->getPath());

        if ($isElement) {
            preg_match('/^\/api\/movie\/(\d)/', $this->getPath(), $matches);
            $id = $matches[1];
        }
        $pathException = new \Exception('Invalid request path.');

        switch($this->getMethod()) {
            case 'GET':
                if ($isCollection) {
                    $query = [];
                    parse_str($this->getQuery(), $query);

                    $response['response'] = $this->getDb()->getMovies($query);
                }
                elseif ($isElement) {
                    $response['response'] = $this->getDb()->getMovie($id);
                }
                break;

            case 'POST':
                if ($isCollection && $id = $this->getDb()->addMovie($this->getPostData())) {
                    $response['response'] = $this->getDb()->getMovie($id);
                }
                break;

            case 'PUT':
                if ($isElement && $movie = $this->getDb()->updateMovie($this->getPostData())) {
                    $response['response'] = $movie;
                }
                break;

            case 'PATCH':
                if ($isElement && $movie = $this->getDb()->updateMovie($this->getPostData())) {
                    $response['response'] = $movie;
                }
                break;

            case 'DELETE':
                if ($isElement && $this->getDb()->removeMovie($id)) {
                    $response['response'] = $this->getDb()->getMovie($id);
                }
                break;
        }

        return $response;
    }

}