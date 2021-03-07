<?php


namespace App;


use PDO;

class Database
{

    /**
     * Database connection.
     * @var PDO
     */
    private PDO $connection;

    /**
     * ID not found exception.
     * @var \Exception
     */
    private \Exception $idException;

    /**
     * Internal exception.
     * @var \Exception
     */
    private \Exception $internalException;

    public function __construct()
    {
        $this->connection = new PDO(
            sprintf("pgsql:host=%s;port=%d;dbname=%s", getenv('DB_HOST'), getenv('DB_PORT'), getenv('DB_NAME')),
            getenv('DB_USER'),
            getenv('DB_PASS'),
            [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

        $this->idException = new \Exception('Invalid identifier: id is invalid or not found.', 404);
        $this->internalException = new \Exception('Internal error. Something went wrong.', 500);
    }

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * @return \Exception
     */
    public function getIdException(): \Exception
    {
        return $this->idException;
    }

    /**
     * @return \Exception
     */
    public function getInternalException(): \Exception
    {
        return $this->internalException;
    }

    /**
     * Gets movie by id.
     *
     * @param int $id
     * @return array|false
     * @throws \Exception
     */
    public function getMovie(int $id)
    {
        $query = '
        SELECT
	        m.id,
	        m.title,
	        m.year,
	        g.name AS genre,
	        m.overview,
	        m.runtime
        FROM movies AS m
        LEFT JOIN genres AS g ON m.genre_id = g.id
        WHERE m.id = :id
        ';

        if (!$this->isMovieExist($id)) {
            throw $this->getIdException();
        }

        $stmt = $this->getConnection()->prepare($query);
        $status = $stmt->execute(['id' => $id]);
        $movie = $stmt->fetch();

        $actors = $this->getMovieActors($id);
        $movie['actors'] = ($actors) ? $actors : [];

        if ($status) {
            return $movie;
        }
        throw $this->getInternalException();
    }

    /**
     * Removes movie by id.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function removeMovie(int $id): bool
    {
        if ($this->isMovieExist($id)) {
            $query = 'DELETE FROM movies WHERE id = :id';
            $stmt = $this->getConnection()->prepare($query);
            $success = $stmt->execute(['id' => $id]);

            if ($success) {
                return true;
            }
            throw $this->getInternalException();
        }

        throw $this->getIdException();
    }

    /**
     * Adds movie to database.
     *
     * @param array $data
     * @return int|bool
     * @throws \Exception
     */
    public function addMovie(array $data)
    {
        $query = '
            INSERT INTO movies (title, year, genre_id, overview, runtime)
            VALUES(
                :title,
                :year,  
                (SELECT id FROM genres WHERE name = :genre),
                :overview,
                :runtime
        )';
        $stmt = $this->getConnection()->prepare($query);
        $success = $stmt->execute([
            'title' => $data['title'],
            'year' => $data['year'],
            'genre' => $data['genre'],
            'overview' => $data['overview'],
            'runtime' => $data['runtime']
        ]);

        if ($success) {
            return $this->getConnection()->lastInsertId();
        }
        throw $this->getInternalException();
    }

    /**
     * Updates movie in database.
     *
     * @param int $id
     * @param array $data
     * @return array|bool
     * @throws \Exception
     */
    public function updateMovie(int $id, array $data): bool
    {
        if (!$this->isMovieExist($id)) {
            throw $this->getIdException();
        }

        $values = array_map(function($key) {
            $field = $key;
            $value = ':'.$key;
            if ($key == 'genre') {
                $field = 'genre_id';
                $value = '(SELECT id FROM genres WHERE name = :genre)';
            }
            return sprintf('%s = %s', $field, $value);
            },
            array_keys($data));

        $query = 'UPDATE movies SET '
            . implode(', ', $values)
            . ' WHERE id = :id';
        $stmt = $this->getConnection()->prepare($query);
        $data['id'] = $id;
        $success = $stmt->execute($data);

        if ($success) {
            return true;
        }
        throw $this->getInternalException();
    }

    /**
     * Gets all movies.
     *
     * @param array $options
     * @return array|false
     * @throws \Exception
     */
    public function getMovies(array $options = [])
    {
        $query = '
            SELECT
	            m.id,
	            m.title,
	            m.year,
	            g.name AS genre,
	            m.overview,
	            m.runtime
            FROM movies AS m
            LEFT JOIN genres AS g ON m.genre_id = g.id 
        ';
        $parameters = [];

        // Apply filters and sorting.
        if ($options['actor']) {
            $query .= '
            INNER JOIN movie_actors AS m_a ON m.id = m_a.movie_id
            INNER JOIN actors AS a ON m_a.actor_id = a.id
            WHERE LOWER(a.first_name||a.last_name) LIKE LOWER(:actor) 
            ';
            $parameters['actor'] = '%'.$options['actor'].'%';
        }

        if ($options['genre']) {
            if ($options['actor']) {
                $query .= 'AND g.name = :genre ';
            } else {
                $query .= 'WHERE g.name = :genre ';
            }
            $parameters['genre'] = $options['genre'];
        }

        $query .= 'GROUP BY m.id, g.name ';

        $order = $options['order'];
        if (isset($order['title'])) {
            if ($order['title'] == 'desc') {
                $query .= 'ORDER BY m.title DESC';
            } else {
                $query .= 'ORDER BY m.title ASC';
            }
        } else {
            $query .= 'ORDER BY m.id ASC';
        }

        // Execute query.
        $stmt = $this->getConnection()->prepare($query);
        $success = $stmt->execute($parameters);
        if (!$success) {
            throw $this->getInternalException();
        }
        $movies = $stmt->fetchAll();

        // Get actors for every movie.
        foreach ($movies as &$movie) {
            $actors = $this->getMovieActors($movie['id']);
            $movie['actors'] = ($actors) ? $actors : [];
        }

        return $movies;
    }

    /**
     * Gets movie actors.
     *
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function getMovieActors(int $id): array
    {
        $query = '
            SELECT
                a.id,
                a.first_name,
                a.last_name,
                a.birth_date
            FROM actors AS a 
            INNER JOIN movie_actors AS m_a ON a.id = m_a.actor_id
            WHERE m_a.movie_id = :id
            ';
        $stmt = $this->getConnection()->prepare($query);
        $success = $stmt->execute(['id' => $id]);

        if ($success) {
            return $stmt->fetchAll();
        }
        throw $this->getInternalException();
    }

    /**
     * Checks if movie exist in database.
     *
     * @param int $id
     * @return bool
     */
    public function isMovieExist(int $id): bool
    {
        $query = 'SELECT id FROM movies WHERE id = :id';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute(['id' => $id]);

        return boolval($stmt->fetch());
    }

}