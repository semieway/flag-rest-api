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

    public function __construct()
    {
        $parts = parse_url(getenv('DATABASE_URL'));
        $this->connection = new PDO("pgsql:host={$parts['host']};port={$parts['port']};dbname={$parts['dbname']}", $parts['user'], $parts['password'], [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

        $this->idException = new \Exception('Invalid identifier: id is invalid or not found.');
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
        INNER JOIN genres AS g ON m.genre_id = g.id
        WHERE m.id = :id
        ';

        if (!$this->isMovieExist($id)) {
            throw $this->getIdException();
        }

        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute(['id' => $id]);
        $movie = $stmt->fetch();

        $actors = $this->getMovieActors($id);
        $movie['actors'] = ($actors) ? $actors : [];

        return $movie;
    }

    /**
     * Removes movie by id.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function removeMovie(int $id)
    {
        if ($this->isMovieExist($id)) {
            $query = 'DELETE FROM movies WHERE id = :id';
            $stmt = $this->getConnection()->prepare($query);
            $stmt->execute(['id' => $id]);

            return boolval($stmt->fetch());
        }

        throw $this->getIdException();
    }

    /**
     * Adds movie to database.
     *
     * @param array $data
     * @return int|bool
     */
    public function addMovie(array $data)
    {
        $query = '
            INSERT INTO movies AS m (m.title, m.year, m.genre_id, m.overview, m.runtime)
            VALUES(
                :title,
                :year,  
                (SELECT id FROM genres WHERE name = :genre),
                :overview,
                :runtime  
        )';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute([
            'title' => $data['title'],
            'year' => $data['year'],
            'genre' => $data['genre'],
            'overview' => $data['overview'],
            'runtime' => $data['runtime']
        ]);

        return ($stmt->fetch()) ? $this->getConnection()->lastInsertId() : false;
    }

    /**
     * Replaces movie in database.
     *
     * @param array $data
     * @return array|bool
     */
    public function replaceMovie(array $data): bool
    {
        if (!$this->isMovieExist($data['id'])) {
            return false;
        }

        $query = '
            UPDATE movies SET
                title = :title,
                year = :year,
                genre_id = (SELECT id FROM genres WHERE name = :genre),
                overview = :overview,
                runtime = :runtime
            WHERE id = :id
        ';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute([
            'id' => $data['id'],
            'title' => $data['title'],
            'year' => $data['year'],
            'genre' => $data['genre'],
            'overview' => $data['overview'],
            'runtime' => $data['runtime']
        ]);

        return $stmt->fetch();
    }

    /**
     * Gets all movies.
     *
     * @param array $options
     * @return array|false
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
            INNER JOIN genres AS g ON m.genre_id = g.id 
        ';
        $parameters = [];

        // Apply filters and sorting.
        if ($options['actor']) {
            $query .= '
            INNER JOIN movie_actors AS m_a ON m.id = m_a.movie_id
            INNER JOIN actors AS a ON m_a.actor_id = a.id
            WHERE LOWER(a.first_name||a.last_name) LIKE LOWER($1) 
            ';
            $parameters[] = '%'.$options['actor'].'%';
        }

        if ($options['genre']) {
            if ($options['actor']) {
                $query .= 'AND g.name = $2 ';
            } else {
                $query .= 'WHERE g.name = $1 ';
            }
            $parameters[] = $options['genre'];
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
        $stmt->execute($parameters);
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
        $stmt->execute(['id' => $id]);

        return $stmt->fetchAll();
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