<?php
namespace Entity\Components;

use Entity\Component;
use Entity\Movie;
use Render\MovieRenderComponent;

/**
 * Class VideoComponent
 * @package Entity\Components
 * @Entity
 */
class MovieComponent extends Component
{
    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @OneToOne(targetEntity="Entity\Movie", cascade={"persist"})
     */
    private $movie;

    public function __construct(Movie $movie)
    {
        $this->setMovie($movie);
    }

    /**
     * @param mixed $movie
     */
    public function setMovie($movie)
    {
        $this->movie = $movie;
    }

    /**
     * @return mixed
     */
    public function getMovie()
    {
        return $this->movie;
    }

    public function getTitleComponent()
    {
        return $this->getTitle();
    }

    public function getTitle()
    {
        return "vidéo";
    }

    public function setId($id)
    {
        $this->id=$id;
    }

    public function getRenderComponent()
    {
        return new MovieRenderComponent($this);
    }


}