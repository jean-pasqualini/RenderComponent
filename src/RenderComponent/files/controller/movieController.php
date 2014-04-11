<?php

namespace controllers\api;

use controllers\AbstractController;
use Entity\Document;
use Entity\Movie;
use FFMpeg\Coordinate\TimeCode;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class movieController extends AbstractController
{
    public function upload()
    {
        $request = $this->getRequest();

        /** @var UploadedFile $movie */
        $movie = $request->files->get("camcorder");

        return $this->returnUpload($movie->getPathname());
    }

    private function returnUpload($moviecache)
    {
        $em = $this->getEntityManager();

        /** @var \FFMpeg\FFMpeg $ffmpeg */
        $ffmpeg = $this->getContainer()->get("ffmpeg");

        $document = new Movie();

        $document->setSecurity("protected");

        $video = $ffmpeg->open($moviecache);

        $picturefile = APP_DIRECTORY.DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR.uniqid("video_").".jpg";

        $video
            ->filters()
            ->resize(new Dimension(320, 240))
        ;

        $video
            ->frame(TimeCode::fromSeconds(1))
            ->save($picturefile)
        ;

        $uploadedFile = new UploadedFile($moviecache, basename($moviecache), "video/flv", null, UPLOAD_ERR_OK, true);

        $document->upload($uploadedFile);

        $uploadedPicture = new UploadedFile($picturefile, basename($picturefile), "image/jpeg", null, UPLOAD_ERR_OK, true);

        $document->generatePictureMovie($uploadedPicture);

        $em->persist($document);

        $em->flush();

        $applicationManager = $this->getApplicationManager();

        $taskManager = $applicationManager->getTaskManager();

        $taskManager->add(
            "app:movie:upload",
            array("movieid" => $document->getId())
        );

        $dataRender = array(
            "path" => $uploadedFile->getClientOriginalName(),
            "webminipath" => $document->getWebMiniPath(),
            "webpicture" => $document->getWebPicture(),
            "id" => $document->getId(),
            "originalname" => $uploadedFile->getClientOriginalName(),
        );

        return new Response(json_encode($dataRender, true));
    }

    public function uploadRed5($videoid)
    {
        $folder = APP_DIRECTORY.DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR."download";

        $server = "http://appartoo.com:5080/red5recorder/streams/";

        if(!file_exists($folder)) mkdir($folder, 0777);

        $source = $server.$videoid.".flv";

        $destination = $folder.DIRECTORY_SEPARATOR.uniqid("red5_").".flv";

        file_put_contents($destination, file_get_contents($source));

        if(!file_exists($destination))
        {
            exit("not getted movie");
        }

        return $this->returnUpload($destination);
    }
}