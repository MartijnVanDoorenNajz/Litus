<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\File;

use CudiBundle\Entity\Article\Internal as InternalArticle;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\File\File")
 * @ORM\Table(name="cudi_files_files")
 */
class File
{
    /**
     * @var integer The ID of the file
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The path to the file
     *
     * @ORM\Column(type="string")
     */
    private $path;

    /**
     * @var string The name of the file
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string The description of the file
     *
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @param EntityManager   $entityManager
     * @param string          $path          The path to the file
     * @param string          $name          The name of the file
     * @param string          $description   The description of the file
     * @param InternalArticle $article       The article of the file
     * @param boolean         $printable     Flag whether the file is the printable one or not
     */
    public function __construct(EntityManager $entityManager, $path, $name, $description, InternalArticle $article, $printable)
    {
        $this->setPath($path)
            ->setName($name)
            ->setDescription($description);

        $entityManager->persist(new ArticleMap($article, $this, $printable));
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
