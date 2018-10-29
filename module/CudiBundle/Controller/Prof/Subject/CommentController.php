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

namespace CudiBundle\Controller\Prof\Subject;

use SyllabusBundle\Entity\Subject;
use SyllabusBundle\Entity\Subject\Comment;
use SyllabusBundle\Entity\Subject\ProfMap;
use SyllabusBundle\Entity\Subject\Reply;
use Zend\View\Model\ViewModel;

/**
 * CommentController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CommentController extends \CudiBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        $subject = $this->getSubjectEntity();
        if ($subject === null) {
            return new ViewModel();
        }

        $comments = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
            ->findBySubjectAndType($subject, 'external');

        $commentForm = $this->getForm('cudi_prof_comment_add');
        $replyForm = $this->getForm('cudi_prof_comment_reply');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($formData['reply']) {
                $replyForm->setData($formData);

                if ($replyForm->isValid()) {
                    $formData = $replyForm->getData();

                    $comment = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Subject\Comment')
                        ->findOneById($formData['comment']);

                    $comment->setReadBy(null);

                    $reply = new Reply(
                        $this->getAuthentication()->getPersonObject(),
                        $comment,
                        $formData['reply']
                    );

                    $this->getEntityManager()->persist($reply);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'The reply was successfully created!'
                    );

                    $this->redirect()->toRoute(
                        'cudi_prof_subject_comment',
                        array(
                            'action'   => 'manage',
                            'id'       => $subject->getId(),
                            'language' => $this->getLanguage()->getAbbrev(),
                        )
                    );

                    return new ViewModel();
                }
            } else {
                $commentForm->setData($formData);

                if ($commentForm->isValid()) {
                    $formData = $commentForm->getData();

                    $comment = new Comment(
                        $this->getAuthentication()->getPersonObject(),
                        $subject,
                        $formData['text'],
                        'external'
                    );

                    $this->getEntityManager()->persist($comment);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'The comment was successfully created!'
                    );

                    $this->redirect()->toRoute(
                        'cudi_prof_subject_comment',
                        array(
                            'action'   => 'manage',
                            'id'       => $subject->getId(),
                            'language' => $this->getLanguage()->getAbbrev(),
                        )
                    );

                    return new ViewModel();
                }
            }
        }

        return new ViewModel(
            array(
                'subject'     => $subject,
                'commentForm' => $commentForm,
                'replyForm'   => $replyForm,
                'comments'    => $comments,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $comment = $this->getCommentEntity();
        if ($comment === null) {
            return new ViewModel();
        }

        if ($comment->getPerson()->getId() != $this->getAuthentication()->getPersonObject()->getId()) {
            return array(
                'result' => (object) array('status' => 'error'),
            );
        }

        $this->getEntityManager()->remove($comment);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @param  integer|null $id
     * @return Subject|null
     */
    private function getSubjectEntity($id = null)
    {
        $academicYear = $this->getCurrentAcademicYear();
        if ($academicYear === null) {
            return;
        }

        $id = $id ?? $this->getParam('id');

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
            ->findOneBySubjectIdAndProfAndAcademicYear(
                $id,
                $this->getAuthentication()->getPersonObject(),
                $academicYear
            );

        if (!($mapping instanceof ProfMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No subject was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_subject',
                array(
                    'action'   => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $mapping->getSubject();
    }

    /**
     * @return Comment|null
     */
    private function getCommentEntity()
    {
        $comment = $this->getEntityById('SyllabusBundle\Entity\Subject\Comment');

        if (!($comment instanceof Comment) || $this->getSubjectEntity($comment->getSubject()->getId()) === null) {
            $this->flashMessenger()->error(
                'Error',
                'No comment was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_article',
                array(
                    'action'   => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $comment;
    }
}
