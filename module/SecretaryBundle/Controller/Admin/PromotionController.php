<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear as AcademicYearUtil,
    CommonBundle\Entity\General\AcademicYear,
    DateTime,
    SecretaryBundle\Entity\Promotion\Academic,
    SecretaryBundle\Entity\Promotion\External,
    SecretaryBundle\Form\Admin\Promotion\Add as AddForm,
    SecretaryBundle\Form\Admin\Promotion\Mail as MailForm,
    Zend\Mail\Message,
    Zend\Validator\EmailAddress as EmailAddressValidator,
    Zend\View\Model\ViewModel;

/**
 * PromotionController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PromotionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $academicYear = $this->_getAcademicYear();

        if (null !== $this->getParam('field')) {
            $paginator = $this->paginator()->createFromArray(
                $this->_search($academicYear),
                $this->getParam('page')
            );
        }

        if (!isset($paginator)) {
            $paginator = $this->paginator()->createFromEntity(
                'SecretaryBundle\Entity\Promotion',
                $this->getParam('page'),
                array(
                    'academicYear' => $academicYear,
                ),
                array()
            );
        }

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $academicYear = $this->_getAcademicYear();

        $promotions = $this->_search($academicYear);

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($promotions, $numResults);

        $result = array();
        foreach($promotions as $promotion) {
            $item = (object) array();
            $item->id = $promotion->getId();
            $item->fullName = $promotion->getFullName();
            $item->universityIdentification = $promotion instanceof Academic ? $promotion->getAcademic()->getUniversityIdentification() : '';
            $item->email = $promotion->getEmailAddress();

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function addAction()
    {
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $academicYear = $this->_getAcademicYear();

        $form = new AddForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ($formData['academic_add']) {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneById($formData['academic_id']);

                    $promotion = $this->getEntityManager()
                        ->getRepository('SecretaryBundle\Entity\Promotion\Academic')
                        ->findOneByAcademicAndAcademicYear($academic, $academicYear);

                    if ($promotion) {
                        $this->flashMessenger()->addMessage(
                            new FlashMessage(
                                FlashMessage::WARNING,
                                'WARNING',
                                'The academic is already in this promotion list!'
                            )
                        );

                        $this->redirect()->toRoute(
                            'secretary_admin_promotion',
                            array(
                                'action' => 'manage',
                                'academicyear' => $academicYear->getCode(),
                            )
                        );

                        return new ViewModel();
                    }

                    $this->getEntityManager()->persist(new Academic($academicYear, $academic));
                } else {
                    $promotion = $this->getEntityManager()
                        ->getRepository('SecretaryBundle\Entity\Promotion\External')
                        ->findOneByEmailAndAcademicYear($formData['external_email'], $academicYear);

                    if ($promotion) {
                        $this->flashMessenger()->addMessage(
                            new FlashMessage(
                                FlashMessage::WARNING,
                                'WARNING',
                                'The email is already in this promotion list!'
                            )
                        );

                        $this->redirect()->toRoute(
                            'secretary_admin_promotion',
                            array(
                                'action' => 'manage',
                                'academicyear' => $academicYear->getCode(),
                            )
                        );

                        return new ViewModel();
                    }

                    $this->getEntityManager()->persist(
                        new External(
                            $academicYear,
                            $formData['external_first_name'],
                            $formData['external_last_name'],
                            $formData['external_email']
                        )
                    );
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The promotion was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'secretary_admin_promotion',
                    array(
                        'action' => 'manage',
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($promotion = $this->_getPromotion()))
            return new ViewModel();

        $this->getEntityManager()->remove($promotion);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function updateAction()
    {
        $academicYear = $this->_getAcademicYear();

        $promotions = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Promotion')
            ->findAllByAcademicYear($academicYear);

        foreach($promotions as $promotion)
            $this->getEntityManager()->remove($promotion);

        $studyMappings = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\AcademicYearMap')
            ->findAllByAcademicYear($academicYear);

        $academics = array();

        foreach($studyMappings as $mapping) {
            if (strpos(strtolower($mapping->getStudy()->getFullTitle()), 'master') === false || $mapping->getStudy()->getPhase() != 2)
                continue;

            $enrollments = $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                ->findAllByStudyAndAcademicYear($mapping->getStudy(), $academicYear);

            foreach($enrollments as $enrollment)
                $academics[$enrollment->getAcademic()->getId()] = $enrollment->getAcademic();
        }

        foreach($academics as $academic)
            $this->getEntityManager()->persist(new Academic($academicYear, $academic));

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The promotion list is successfully updated!'
            )
        );

        $this->redirect()->toRoute(
            'secretary_admin_promotion',
            array(
                'action' => 'manage',
                'academicyear' => $academicYear->getCode(),
            )
        );

        return new ViewModel();
    }

    public function mailAction()
    {
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $academicYear = $this->_getAcademicYear();

        $from = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.mail');

        $form = new MailForm($from);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $promotions = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Promotion')
                    ->findAllByAcademicYear($academicYear);

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.mail_name');

                $mail = new Message();
                $mail->setBody($formData['message'])
                    ->setFrom($from, $mailName)
                    ->addTo($from, $mailName)
                    ->setSubject($formData['subject']);

                $emailValidator = new EmailAddressValidator();
                $i = 0;
                foreach($promotions as $promotion) {
                    if (null !== $promotion->getEmailAddress() && $emailValidator->isValid($promotion->getEmailAddress())) {
                        $i++;
                        $mail->addBcc($promotion->getEmailAddress(), $promotion->getFullName());
                    }

                    if ($i == 500) {
                        $i = 0;
                        if ('development' != getenv('APPLICATION_ENV'))
                            $this->getMailTransport()->send($mail);

                        $mail->setBcc(array());
                    }
                }

                if ('development' != getenv('APPLICATION_ENV'))
                    $this->getMailTransport()->send($mail);

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The mail was successfully sent!'
                    )
                );

                $this->redirect()->toRoute(
                    'secretary_admin_promotion',
                    array(
                        'action' => 'manage',
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }

    private function _search(AcademicYear $academicYear)
    {
        switch($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Promotion')
                    ->findAllByName($this->getParam('string'), $academicYear);
            case 'mail':
                return $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Promotion')
                    ->findAllByEMail($this->getParam('string'), $academicYear);
        }
    }

    protected function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear')) {
            return $this->getCurrentAcademicYear();
        } else {
            $startAcademicYear = AcademicYearUtil::getDateTime($this->getParam('academicyear'));

            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                )
            );
        }
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStart($start);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $academicYear;
    }

    private function _getPromotion()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the promotion!'
                )
            );

            $this->redirect()->toRoute(
                'secretary_admin_promotion',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $promotion = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Promotion')
            ->findOneById($this->getParam('id'));

        if (null === $promotion) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No promotion with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'secretary_admin_promotion',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $promotion;
    }
}