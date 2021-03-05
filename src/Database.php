<?php


namespace App;


class Database
{

    private $connection;

    public function __construct()
    {
        $this->connection = pg_connect(getenv('DATABASE_URL'));
    }

    /**
     * @return resource|false
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Gets movie by id.
     *
     * @param int $id
     * @return array|false
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
        WHERE m.id = $1
        ';

        pg_prepare($this->getConnection(), 'get_movie', $query);
        $result = pg_execute($this->getConnection(), 'get_movie', [$id]);

        return pg_fetch_row($result, 0, PGSQL_ASSOC);
    }

    /**
     * Removes movie by id.
     *
     * @param int $id
     * @return array|false
     */
    public function removeMovie(int $id)
    {
        return pg_delete($this->getConnection(), 'movies', ['id' => $id]);
    }


    /**
     * Add movie to database.
     *
     * @param array $data
     * @return bool
     */
    public function addMovie(array $data)
    {
        $result = pg_insert($this->getConnection(), 'movies', $data);
        return ($result) ? true : false;
    }


    /**
     * Update movie in database.
     *
     * @param array $data
     * @return bool
     */
    public function updateMovie(array $data)
    {
        return pg_update($this->getConnection(), 'movies', $data, ['id' => $data['id']]);
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
        if ($order['title']) {
            if ($order['title'] == 'desc') {
                $query .= 'ORDER BY m.title DESC';
            } else {
                $query .= 'ORDER BY m.title ASC';
            }
        } else {
            $query .= 'ORDER BY m.id ASC';
        }

        // Execute query.
        pg_prepare($this->getConnection(), 'get_movies', $query);
        $result = pg_execute($this->getConnection(), 'get_movies', $parameters);
        $movies = pg_fetch_all($result, PGSQL_ASSOC);

        // Get actors for every fetched movie.
        foreach ($movies as &$movie) {
            $query = '
            SELECT
                a.id,
                a.first_name,
                a.last_name,
                a.birth_date
            FROM actors AS a 
            INNER JOIN movie_actors AS m_a ON a.id = m_a.actor_id
            WHERE m_a.movie_id = $1
            ';

            pg_prepare($this->getConnection(), 'get_actors', $query);
            $result = pg_execute($this->getConnection(), 'get_actors', [$movie['id']]);
            $actors = pg_fetch_all($result, PGSQL_ASSOC);
            $movie['actors'] = $actors;
        }

        return $movies;
    }

}