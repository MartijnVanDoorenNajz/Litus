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

namespace QuizBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a quiz round.
 *
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\Round")
 * @ORM\Table(name="quiz_rounds")
 */
class Round
{
    /**
     * @var integer The entry's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the round
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var Quiz The quiz this round belongs to
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\Quiz")
     * @ORM\JoinColumn(name="quiz", referencedColumnName="id")
     */
    private $quiz;

    /**
     * @var integer The order of the round
     *
     * @ORM\Column(name="round_order", type="smallint")
     */
    private $order;

    /**
     * @var integer The max points of the round
     *
     * @ORM\Column(name="max_points", type="smallint")
     */
    private $maxPoints;

    /**
     * @var ArrayCollection The points in this round
     *
     * @ORM\OneToMany(targetEntity="QuizBundle\Entity\Point", mappedBy="round", cascade="remove")
     */
    private $points;

    /**
     * @param Quiz $quiz
     */
    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
        $this->points = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Quiz
     */
    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param  integer $order
     * @return self
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return integer
     */
    public function getMaxPoints()
    {
        return $this->maxPoints;
    }

    /**
     * @param  integer $maxPoints
     * @return self
     */
    public function setMaxPoints($maxPoints)
    {
        $this->maxPoints = $maxPoints;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPoints()
    {
        return $this->points;
    }
}
