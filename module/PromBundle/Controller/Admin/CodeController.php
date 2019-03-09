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

namespace PromBundle\Controller\Admin;

use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use PromBundle\Entity\Bus\ReservationCode\Academic as AcademicCode;
use PromBundle\Entity\Bus\ReservationCode\External as ExternalCode;
use PromBundle\Entity\Bus\ReservationCode;
use Zend\Http\Headers;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message;

/**
 * CodeController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class CodeController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                ->getAllCodesByAcademicYear($this->getCurrentAcademicYear()),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $academicForm = $this->getForm('prom_reservationCode_academic');
        $externalForm = $this->getForm('prom_reservationCode_external');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $academicForm->setData($formData);
            $externalForm->setData($formData);

            $code = null;

            if (isset($formData['academic_add']) && $academicForm->isValid()) {
                $code = $academicForm->hydrateObject(
                    new AcademicCode($this->getCurrentAcademicYear())
                );
            } elseif (isset($formData['external_add']) && $externalForm->isValid()) {
                $code = $externalForm->hydrateObject(
                    new ExternalCode($this->getCurrentAcademicYear())
                );
            }

            if($code !== null){
                $this->getEntityManager()->persist($code);
                $this->getEntityManager()->flush();

                $this->sendReservationCodeMail($code);

                $this->flashMessenger()->success(
                    'Success',
                    'The bus code was successfully created!'
                );

                $this->redirect()->toRoute(
                    'prom_admin_code',
                    array(
                        'action' => 'add'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'academicForm' => $academicForm,
                'externalForm' => $externalForm
            )
        );
    }

    public function expireAction()
    {
        $this->initAjax();

        $code = $this->getReservationCodeEntity();
        if ($code === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($code);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function exportAction()
    {
        $entries = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
            ->getAllCodesByAcademicYear($this->getCurrentAcademicYear());

        $file = new CsvFile();
        $heading = array('Code');

        $results = array();
        foreach ($entries as $entry) {
            $results[] = array(
                $entry->getCode(),
            );
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="codes.csv"',
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $codes = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($codes as $code) {
            $item = (object) array();
            $item->id = $code->getId();
            $item->code = $code->getCode();
            $item->firstName = $code->getFirstName();
            $item->lastName = $code->getLastName();
            $item->used = $code->isUsed();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function mailAction()
    {
        $this->initAjax();

        $code = $this->getReservationCodeEntity();
        $this->sendReservationCodeMail($code);

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'code':
                return $this->getEntityManager()
                    ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                    ->findAllByCodeQuery($this->getParam('string'));
            case 'username':
                return $this->getEntityManager()
                    ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                    ->findAllByUniversityIdentificationQuery($this->getParam('string'));
        }
    }

    public function viewAction()
    {
        $code = $this->getReservationCodeEntity();
        if ($code === null) {
            return new ViewModel();
        }

        if ($code->isUsed()) {
            $passenger = $this->getEntityManager()
                ->getRepository('PromBundle\Entity\Bus\Passenger')
                ->findPassengerByCode($code);
        } else {
            $passenger = null;
        }

        return new ViewModel(
            array(
                'passenger' => $passenger[0],
                'code'      => $code,
            )
        );
    }

    /**
     * @return ReservationCode|null
     */
    private function getReservationCodeEntity()
    {
        $code = $this->getEntityById('PromBundle\Entity\Bus\ReservationCode');

        if (!($code instanceof ReservationCode)) {
            $this->flashMessenger()->error(
                'Error',
                'No code was found!'
            );

            $this->redirect()->toRoute(
                'prom_admin_code',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $code;
    }

    private function sendReservationCodeMail($code)
    {
        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('prom.reservation_mail')
            );
        $mail = new Message();
        $mail->addTo($code->getEmail())
            ->setEncoding('UTF-8')
            ->setBody(str_replace('{{ firstName }}', $code->getFirstName(), $mailData['body']))
            ->setBody(str_replace('{{ lastName }}', $code->getLastName(), $mailData['body']))
            ->setBody(str_replace('{{ reservationCode }}', $code->getCode(), $mailData['body']))
            ->setFrom($mailData['from'])
            ->addBcc($mailData['from'])
            ->setSubject($mailData['subject']);
        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }
}
