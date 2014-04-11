<?php
/**
 * appartoo Trips Controller Class
 *
 * Helps to control the trips functionality
 *
 * @package		appartoo
 * @subpackage	Controllers
 * @category	Trips
 * @author		Appartoo Product Team
 * @version		Version 1.6
 * @link		http://www.Appartoo.com
 */

namespace  controllers;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class Documents extends \controllers\AbstractController {

    public function __construct()
    {


        parent::__construct();

        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('cookie');

        $this->load->library('Form_validation');

        $this->load->model('Users_model');
        $this->load->model('Email_model');
        $this->load->model('Message_model');
        $this->load->model('Trips_model');


    }

    public function secureRead($file)
    {
        $directoryDocuments = APP_DIRECTORY.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."documents".DIRECTORY_SEPARATOR."protected";

        if(!file_exists($directoryDocuments.DIRECTORY_SEPARATOR.$file))
        {
            return $this->redirect("/images/notfoundpicture.png", false);
        }

        $em = $this->getEntityManager();

        $extension = pathinfo($file, PATHINFO_EXTENSION);

        $pictureExts = array("jpg", "jpeg", "png", "gif");

        $movieExts = array("mp4", "webm", "flv");

        $allowedExts = array_merge($pictureExts, $movieExts);

        if(!in_array($extension, $allowedExts)) return new Response();

        if(in_array($extension, $movieExts))
        {
            $query = $em->createQuery("
                        SELECT movieComponent FROM Entity\\Components\\MovieComponent movieComponent
                        JOIN movieComponent.movie movie WITH movie.h264 = :path
                        WHERE :profile MEMBER OF movieComponent.visibilityByProfiles
            ");

            //                    JOIN component.movie movie WITH movie.path = :path

           $query->setParameter("path", $file);
           $query->setParameter("profile", $this->getProfile());

            $query->setMaxResults(1);

            $result = $query->getOneOrNullResult();

            if($result !== null)
            {
                $response = new BinaryFileResponse($directoryDocuments.DIRECTORY_SEPARATOR.$file);

                $response->trustXSendfileTypeHeader();

                $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file, $file);

                $response->prepare($this->getRequest());

                return $response;
            }
            else
            {
                return new Response("not alowed");
            }
        }
        else
        {
            $query = $em->createQuery("
                SELECT profile, (
                  SELECT COUNT(visite)
                  FROM Entity\\Visite visite
                  JOIN visite.contact contact
                  WHERE contact.userby = profile.user AND contact.userto = :user
                ) AS hasvisite
                FROM Entity\\Profiles profile
                JOIN profile.documents document
                WITH document.miniPath = :file OR document.path = :file OR document.picture = :file
            ");

            $query->setParameter("user", $this->getUser());

            $query->setParameter("file", $file);

            $result = $query->getOneOrNullResult();

            $profileDocument = $result[0];

            $hasVisite = $result["hasvisite"];

            if($profileDocument === null)
            {
                $query = $em->createQuery("
                        SELECT movieComponent FROM Entity\\Components\\MovieComponent movieComponent
                        JOIN movieComponent.movie movie WITH movie.picture = :path
                        WHERE :profile MEMBER OF movieComponent.visibilityByProfiles
                ");

                //                    JOIN component.movie movie WITH movie.path = :path
                $query->setParameter("path", $file);
                $query->setParameter("profile", $this->getProfile());

                $query->setMaxResults(1);

                $result = $query->getOneOrNullResult();

                if($result !== null)
                {
                    $response = new BinaryFileResponse($directoryDocuments.DIRECTORY_SEPARATOR.$file);

                    $response->trustXSendfileTypeHeader();

                    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file, $file);

                    $response->prepare($this->getRequest());

                    return $response;
                }
                else
                {
                    return new Response("not alowed");
                }
            }
            else
            {
                return new Response("not alowed");
            }

            if($this->getProfile() === null || $this->getProfile()->getId() == $profileDocument->getId() || $hasVisite > 0)
            {
                $response = new BinaryFileResponse($directoryDocuments.DIRECTORY_SEPARATOR.$file);

                $response->trustXSendfileTypeHeader();

                $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file, $file);

                $response->prepare($this->getRequest());

                return $response;
            }
            else
            {
                //return $this->redirect("/images/notfoundpicture.png", false);
            }
        }


    }

    public function upload()
    {
        $request = $this->getRequest();

        $em = $this->getEntityManager();

        $files = $request->files->get("files");

        $dataRender = array("documents" => array());

        //$user = $this->getUser();

        foreach($files as $file)
        {
            $document = new \Entity\Document();

            $document->upload($file);

            //$user->addDocument($document);

            $em->persist($document);

            //$em->persist($user);

            $em->flush($document);

            $dataReturn = array(
                "path" => $file->getClientOriginalName(),
                "webminipath" => $document->getWebMiniPath(),
                "webpicture" => $document->getWebPicture(),
                "id" => $document->getId(),
                "originalname" => $file->getClientOriginalName(),
            );

            $dataRender["documents"][] = $dataReturn;
        }

        $em->flush();

        echo json_encode($dataRender, true);
    }



}
