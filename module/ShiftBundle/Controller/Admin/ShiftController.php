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

namespace ShiftBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    ShiftBundle\Entity\Shift,
    ShiftBundle\Form\Admin\Shift\Add as AddForm,
    ShiftBundle\Form\Admin\Shift\Edit as EditForm,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * ShiftController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ShiftController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Shift')
                ->findAllActive(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Shift')
                ->findAllOld(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic');

                $manager = ('' == $formData['manager_id'])
                    ? $repository->findOneByUsername($formData['manager']) : $repository->findOneById($formData['manager_id']);

                $shift = new Shift(
                    $this->getAuthentication()->getPersonObject(),
                    $this->getCurrentAcademicYear(),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                    $manager,
                    $formData['nb_responsibles'],
                    $formData['nb_volunteers'],
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Organization\Unit')
                        ->findOneById($formData['unit']),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Location')
                        ->findOneById($formData['location']),
                    $formData['name'],
                    $formData['description']
                );

                if ('' != $formData['event']) {
                    $shift->setEvent(
                        $this->getEntityManager()
                            ->getRepository('CalendarBundle\Entity\Node\Event')
                            ->findOneById($formData['event'])
                    );
                }

                $this->getEntityManager()->persist($shift);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The shift was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'shift_admin_shift',
                    array(
                        'action' => 'add'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($shift = $this->_getShift()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $shift);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ($shift->canEditDates()) {
                    $shift->setStartDate(DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']))
                        ->setEndDate(DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']));
                }

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic');

                $manager = ('' == $formData['manager_id'])
                    ? $repository->findOneByUsername($formData['manager']) : $repository->findOneById($formData['manager_id']);

                $shift->setManager($manager)
                    ->setNbResponsibles($formData['nb_responsibles'])
                    ->setNbVolunteers($formData['nb_volunteers'])
                    ->setUnit(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
                            ->findOneById($formData['unit'])
                    )
                    ->setLocation(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Location')
                            ->findOneById($formData['location'])
                    )
                    ->setName($formData['name'])
                    ->setDescription($formData['description']);

                if ('' != $formData['event']) {
                    $shift->setEvent(
                        $this->getEntityManager()
                            ->getRepository('CalendarBundle\Entity\Node\Event')
                            ->findOneById($formData['event'])
                    );
                } else {
                    $shift->setEvent(null);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The shift was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'shift_admin_shift',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($shift = $this->_getShift()))
            return new ViewModel();

        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.mail');

        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.mail_name');

        $language = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.shift_deleted_mail')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = $mailData[$language->getAbbrev()]['subject'];

        $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y h:i') . ' to ' . $shift->getEndDate()->format('d/m/Y h:i');

        $mail = new Message();
        $mail->setBody(str_replace('{{ shift }}', $shiftString, $message))
            ->setFrom($mailAddress, $mailName)
            ->setSubject($subject);

        $mail->addTo($mailAddress, $mailName);

        foreach ($shift->getVolunteers() as $volunteer)
            $mail->addBcc($volunteer->getPerson()->getEmail(), $volunteer->getPerson()->getFullName());

        foreach ($shift->getResponsibles() as $responsible)
            $mail->addBcc($responsible->getPerson()->getEmail(), $responsible->getPerson()->getFullName());

        if ('development' != getenv('APPLICATION_ENV'))
            $this->getMailTransport()->send($mail);

        $this->getEntityManager()->remove(
            $shift->prepareRemove()
        );

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $shifts = $this->_search();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($shifts, $numResults);

        $result = array();
        foreach($shifts as $shift) {
            $item = (object) array();
            $item->id = $shift->getId();
            $item->name = $shift->getName();
            $item->eventName = $shift->getEvent()->getName();
            $item->startDate = $shift->getStartDate();
            $item->endDate = $shift->getEndDate();
            $result[] = $item;
            
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _search()
    {
        switch($this->getParam('field')) {
            case 'shiftname':
                return $this->getEntityManager()
                    ->getRepository('ShiftBundle\Entity\Shift')
                    ->findByShiftName($this->getParam('string'));
            case 'event':
                $events = $this->getEntityManager()
                    ->getRepository('CalendarBundle\Entity\Node\Event')
                    ->findByName($this->getParam('string'));
                $result = array();
                foreach($events as $event){
                $result[] = $this->getEntityManager()
                    ->getRepository('ShiftBundle\Entity\Shift')
                    ->findAllActiveByEvent($event);
                }
                return $result;
        }
    }

    private function _getShift()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the shift!'
                )
            );

            $this->redirect()->toRoute(
                'shift_admin_shift',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $shift = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findOneById($this->getParam('id'));

        if (null === $shift) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No shift with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'shift_admin_shift',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $shift;
    }
}
