<?php


namespace App;

/**
 * Defines the properties of the Movie entity.
 */
class Movie
{

    private int $id;

    private string $title;

    private int $year;

    private ?int $genreId;

    private ?string $overview;

    private ?int $runtime;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return int|null
     */
    public function getGenreId(): ?int
    {
        return $this->genreId;
    }

    /**
     * @param int|null $genreId
     */
    public function setGenreId(?int $genreId): void
    {
        $this->genreId = $genreId;
    }

    /**
     * @return string|null
     */
    public function getOverview(): ?string
    {
        return $this->overview;
    }

    /**
     * @param string|null $overview
     */
    public function setOverview(?string $overview): void
    {
        $this->overview = $overview;
    }

    /**
     * @return int|null
     */
    public function getRuntime(): ?int
    {
        return $this->runtime;
    }

    /**
     * @param int|null $runtime
     */
    public function setRuntime(?int $runtime): void
    {
        $this->runtime = $runtime;
    }

}