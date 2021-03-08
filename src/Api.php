<?php


namespace App;


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
     * Request data array.
     * @var array
     */
    private array $requestData;

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
     * @param array $requestData
     */
    public function __construct(string $requestUri, string $method, Database $db, array $requestData = [])
    {
        $url = parse_url($requestUri);

        $this->setPath($url['path']);
        $this->setQuery($url['query'] ?? '');
        $this->setMethod($method);
        $this->setDb($db);
        $this->setRequestData($requestData);
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
    public function getRequestData(): array
    {
        return $this->requestData;
    }

    /**
     * @param array $requestData
     */
    public function setRequestData(array $requestData): void
    {
        $this->requestData = $requestData;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param int $statusCode
     */
    public function setSuccess(int $statusCode): void
    {
        if (in_array($statusCode, [
            200,
            201
        ])) {
            $this->success = true;
        } else {
            $this->success = false;
        }
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
    public function getJsonResponse(): string
    {
        return json_encode($this->getResponse());
    }

    /**
     * Return response array.
     *
     * @return array
     */
    public function getResponse(): array
    {
        $response = [];
        $body = [];

        try {
            $body = $this->generateResponse();
        } catch (\Exception $e) {
            $this->setSuccess($e->getCode());
            $this->setStatusCode($e->getCode());
            $this->setStatusMessage($e->getMessage());
        }
        $response['status'] = $this->getResponseStatus();
        $response['response'] = $body;

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
        $isCollection = preg_match('/^\/api\/movies$/', $this->getPath());
        $isElement = preg_match('/^\/api\/movie\/\d+$/', $this->getPath());

        if (!$isCollection && !$isElement) {
            throw new \Exception('Invalid request path.', 501);
        }

        if ($isElement) {
            preg_match('/^\/api\/movie\/(\d+)$/', $this->getPath(), $matches);
            $id = $matches[1];
        }

        switch($this->getMethod()) {
            case 'GET':
                if ($isCollection) {
                    $query = [];
                    parse_str($this->getQuery(), $query);

                    return $this->getDb()->getMovies($query);
                }
                elseif ($isElement) {
                    return $this->getDb()->getMovie($id);
                }
                break;

            case 'POST':
                if ($isCollection
                    && $this->isDataValid($this->getRequestData())
                    && $id = $this->getDb()->addMovie($this->getRequestData())) {
                    return $this->getDb()->getMovie($id);
                }
                break;

            case 'PUT':
                if ($isElement
                    && $this->isDataValid($this->getRequestData())
                    && $this->getDb()->updateMovie($id, $this->getRequestData(), false)
                ) {
                    return $this->getDb()->getMovie($id);
                }
                break;

            case 'PATCH':
                if ($isElement && $this->getDb()->updateMovie($id, $this->getRequestData())) {
                    return $this->getDb()->getMovie($id);
                }
                break;

            case 'DELETE':
                if ($isElement && $this->getDb()->removeMovie($id)) {
                    throw new \Exception('The item was deleted successfully.', 200);
                }
                break;
        }
    }

    /**
     * Checks if posted data is valid.
     *
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function isDataValid(array $data):bool
    {
        if (isset($data['title'])
            && isset($data['year'])
            && is_string($data['title'])
            && is_numeric($data['year'])
        ) {
            return true;
        }
        throw new \Exception('Invalid request data.', 422);
    }

}