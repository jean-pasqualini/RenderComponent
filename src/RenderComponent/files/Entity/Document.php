<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adibox
 * Date: 12/09/13
 * Time: 10:48
 * To change this template use File | Settings | File Templates.
 */

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Document
 * @package Entity
 * @Table(name="document")
 * @Entity
 * @HasLifecycleCallbacks
 */
class Document extends DocumentBase {

    const SECURITY_PUBLIC = "public";

    const SECURITY_PROTECTED = "protected";

    const SECURITY_PRIVATE = "private";

    CONST POSITION_WATERMARK_LEFT = 1;

    CONST POSITION_WATERMARK_RIGHT = 2;

    /**
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function getId()
    {
        return $this->id;
    }

}