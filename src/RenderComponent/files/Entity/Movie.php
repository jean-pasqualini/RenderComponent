<?php

namespace Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;

/**
* Class Movie
* @package Entity
* @Table(name="movie")
* @Entity
* @HasLifecycleCallbacks
*/
class Movie extends DocumentBase
{
    /**
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
    * @Column(name="webm", type="string")
    */
    private $webm = "";

    /**
    * @Column(name="h264", type="string")
    */
    private $h264 = "";

    public function getWebWebm()
    {
        return null === $this->webm ? null : $this->getWebUploadDir()."/".$this->webm;
    }

    public function getAbsoluteWebm()
    {
        return null === $this->webm ? null : $this->getUploadRootDir()."/".$this->webm;
    }

    public function getWebH264()
    {
        return null === $this->h264 ? null : $this->getWebUploadDir()."/".$this->h264;
    }

    public function getAbsoluteH264()
    {
        return null === $this->h264 ? null : $this->getUploadRootDir()."/".$this->h264;
    }

    public function setH264($file)
    {
        $this->h264 = $file;
    }

    public function setWebm($file)
    {
        $this->webm = $file;
    }

    public function upload(UploadedFile $file = null)
    {
        $this->file = $file;

        $this->setOriginalName($file->getClientOriginalName());

        $this->path = uniqid().".".$file->getClientOriginalExtension();

        $this->file->move($this->getUploadRootDir(), $this->path);
    }

    public function generatePictureMovie(UploadedFile $file)
    {
        $this->picture = uniqid().".".$file->getClientOriginalExtension();

        $file->move($this->getUploadRootDir(), $this->picture);
    }

    public function getId()
    {
        return $this->id;
    }

    public function toArray($group = "public")
    {
        return array_merge(
            parent::toArray(),
            array(
                "webh264" => $this->getWebH264(),
                "webwebm" => $this->getWebWebm(),
            )
        );
    }

}