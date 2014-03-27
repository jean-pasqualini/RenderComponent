<?php

namespace Entity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/** @MappedSuperclass */
class DocumentBase
{
    /**
     * @var string $security
     * @Column(name="security", type="string")
     */
    protected $security = "public";

    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * @var $originalName
     * @Column(name="original_name", type="string")
     */
    protected $originalName;

    /**
     * @Column(name="path", type="string")
     */
    protected $path = "";

    /**
     * @Column(name="minipath", type="string")
     */
    protected $miniPath = "";

    /**
     * @Column(name="picture", type="string")
     */
    protected $picture = "";

    public function __construct()
    {

    }



    public function getPath()
    {
        return $this->path;
    }

    public function getUploadRootDir()
    {
        return __DIR__."/../../".$this->getUploadDir();
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir()."/".$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getWebUploadDir()."/".$this->path;
    }

    public function getType()
    {
        return strtoupper(__CLASS__);
    }

    public function is($type)
    {
        return strtoupper($type) == $this->getType();
    }

    public function getAbsoluteMiniPath()
    {
        return null === $this->path ? null : $this->getUploadRootDir()."/".$this->miniPath;
    }

    public function getWebMiniPath()
    {
        return null === $this->path ? null : $this->getWebUploadDir()."/".$this->miniPath;
    }

    public function getAbsolutePicture()
    {
        return null === $this->picture ? null : $this->getUploadRootDir()."/".$this->picture;
    }

    public function getWebPicture()
    {
        return null === $this->picture ? null : $this->getWebUploadDir()."/".$this->picture;
    }

    public function getUploadDir()
    {
        return "uploads/documents/".$this->getSecurity();
    }

    public function getWebUploadDir()
    {
        switch($this->getSecurity())
        {
            case Document::SECURITY_PUBLIC:

                return "uploads/documents/public";

            break;

            default:

                return "documentsecureread";

            break;
        }
    }

    /**
     * @param string $security
     */
    public function setSecurity($security)
    {
        $this->security = $security;
    }

    /**
     * @return string
     */
    public function getSecurity()
    {
        return $this->security;
    }



    /**
     * @param mixed $originalName
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;
    }

    /**
     * @return mixed
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    public function getFallbackOriginalName()
    {
        return iconv('UTF-8', 'ASCII//TRANSLIT', $this->getOriginalName());
    }


    /**
     * @param mixed $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @PreRemove
     */
    public function onRemove()
    {
        // When document removed from database files attache on filesysteme equal removed
        @unlink($this->getAbsolutePath());
        @unlink($this->getAbsoluteMiniPath());
        @unlink($this->getAbsolutePicture());
    }

    public function removeWatermark($position)
    {
        $image = new \Imagick($this->getAbsolutePicture());

        $width = $image->getimagewidth();

        $height = $image->getimageheight();

        $widthWatermark = 55;

        switch($position)
        {
            case Document::POSITION_WATERMARK_LEFT:
                $image->cropimage($width - $widthWatermark, $height, $widthWatermark, 0);
            break;

            case Document::POSITION_WATERMARK_RIGHT:
                $image->cropimage($width - $widthWatermark, $height, 0, 0);
            break;
        }

        $file = "cropDocument".$this->getId().".jpg";

        $filePath = APP_DIRECTORY.DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR.$file;

        $image->writeimage(APP_DIRECTORY.DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR.$file);

        $image->clear();

        $image->destroy();

        $fileUpload = new UploadedFile($filePath, $file, null, null, UPLOAD_ERR_OK, true);

        $this->upload($fileUpload);
    }

    public function upload(UploadedFile $file = null)
    {
        if($file !== null) $this->file = $file;
        if(null === $this->file) throw new \Exception("Error upload file parameters null");

        /**
        if(!in_array($this->file->getClientOriginalExtension(), array("pdf", "jpg", "png", "jpeg", "dox", "docx", "xls"))
        {
            throw new \Exception("Error upload file extension ".$this->file->getClientOriginalExtension()." not allowed");
        }
        */

        $this->setOriginalName($this->file->getClientOriginalName());

        $this->path = uniqid().".".$this->file->getClientOriginalExtension();

        $this->generatePicture($file);

        $guessExtension = $file->getClientOriginalExtension();

        $guessExtension = strtolower($guessExtension);

        if($guessExtension == "jpg" || $guessExtension == "png" || $guessExtension == "jpeg")
        {
            $cmd = "convert -unsharp 2x2 -brightness-contrast 15x20 '".realpath($this->getAbsolutePicture())."' '".realpath($this->getAbsolutePicture())."'";

            passthru($cmd, $output);
        }

        $this->generateMiniPath($file);

        $fileMoved = $this->file->move($this->getUploadRootDir(), $this->path);

        $this->file = null;
    }

    public function generatePicture(UploadedFile $file = null)
    {
        if($file !== null) $this->file = $file;
        if(null === $this->file) throw new \Exception("Error upload file parameters null");

        $this->picture = uniqid()."_picture.jpg";

        $guessExtension = $file->getClientOriginalExtension();

        $guessExtension = strtolower($guessExtension);

        //\app::getInstance()->getServiceContainer()->get("logger")->log(LogLevel::DEBUG, "[UPLOAD] ".$guessExtension);

        if($guessExtension == "pdf" || $guessExtension == "jpg" || $guessExtension == "png" || $guessExtension == "jpeg")
        {
            $image = new \Imagick();

            $image->setresolution(300, 300);

            $image->readimage($file->getPathname());

            $image->setimageformat("jpeg");

            $this->optimise($image);

            $image->setcompressionquality(80);

            $image->writeimage($this->getAbsolutePicture());

            $image->clear();

            $image->destroy();
        }
        else
        {

        }
    }

    private function optimise(\Imagick $image)
    {
        $image->contrastimage(true);

        $image->modulateimage(110, 100, 100);

        //$image->setimagecompression(\Imagick::COMPRESSION_NO);

        //$image->setimagecompressionquality(100);
    }

    public function generateMiniPath(UploadedFile $file = null)
    {
        if($file !== null) $this->file = $file;
        if(null === $this->file) throw new \Exception("Error upload file parameters null");

        $this->miniPath = uniqid()."_thumbnail.jpg";

        $guessExtension = $file->getClientOriginalExtension();

        $guessExtension = strtolower($guessExtension);

        if($guessExtension == "pdf" || $guessExtension == "jpg" || $guessExtension == "png" || $guessExtension == "jpeg")
        {
            $image = new \Imagick($this->getAbsolutePicture());

            $image->thumbnailimage(200, 200, true);

            $this->optimise($image);

            $image->writeimage($this->getAbsoluteMiniPath());

            $image->clear();

            $image->destroy();
        }
        else
        {

        }
    }

    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "webpicture" => $this->getWebPicture(),
            "webminipath" => $this->getWebMiniPath()
        );
    }
}