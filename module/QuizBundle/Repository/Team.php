<?php

namespace QuizBundle\Repository;

use QuizBundle\Entity\Quiz as QuizEntity,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Team
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Team extends EntityRepository
{
    /**
     * Gets all teams belonging to a quiz
     * @param QuizBundle\Entity\Quiz $quiz The team the rounds must belong to
     */
    public function findAllByQuizQuery(QuizEntity $quiz)
    {
        $query = $this->_em->createQueryBuilder();

        $resultSet = $query->select('t')
            ->from('QuizBundle\Entity\Team', 't')
            ->where(
                $query->expr()->eq('t.quiz', ':quiz')
            )
            ->orderBy('t.number', 'ASC')
            ->setParameter('quiz', $quiz->getId())
            ->getQuery();

        return $resultSet;
    }

    /**
     * Gets the number for the next team in the quiz
     * @param \QuizBundle\Entity\Quiz $quiz
     * @return int
     */
    public function getNextTeamNumberForQuiz(QuizEntity $quiz)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('MAX(t.number)')
            ->from('QuizBundle\Entity\Team', 't')
            ->where(
                $query->expr()->eq('t.quiz', ':quiz')
            )
            ->orderBy('t.number', 'DESC')
            ->setParameter('quiz', $quiz->getId())
            ->getQuery()
            ->getSingleScalarResult();

        if($resultSet === null)
            return 1;

        return $resultSet + 1;
    }
}
