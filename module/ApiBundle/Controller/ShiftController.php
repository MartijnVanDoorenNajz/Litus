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

namespace ApiBundle\Controller;

use CommonBundle\Entity\User\Person;
use ShiftBundle\Entity\Shift;
use ShiftBundle\Entity\Shift\Responsible;
use ShiftBundle\Entity\Shift\Volunteer;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;

/**
 * ShiftController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ShiftController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function activeAction()
    {
        $this->initJson();

        $person = $this->getPersonEntity();
        if ($person === null) {
            return $this->error(401, 'The access token is not valid');
        }

        $shifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActive();

        $signedUp = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($person);

        $result = array();
        foreach ($shifts as $shift) {
            $result[] = array(
                'id' => $shift->getId(),

                'canHaveAsResponsible'  => $shift->canHaveAsResponsible($this->getEntityManager(), $person),
                'canHaveAsVolunteer'    => $shift->canHaveAsVolunteer($this->getEntityManager(), $person),
                'description'           => $shift->getDescription(),
                'currentNbResponsibles' => count($shift->getResponsibles()),
                'currentNbVolunteers'   => count($shift->getVolunteers()),
                'endDate'               => $shift->getEndDate()->format('c'),
                'signedUp'              => in_array($shift, $signedUp),
                'manager'               => $shift->getManager()->getFullName(),
                'name'                  => $shift->getName(),
                'nbResponsibles'        => $shift->getNbResponsibles(),
                'nbVolunteers'          => $shift->getNbVolunteers(),
                'startDate'             => $shift->getStartDate()->format('c'),

                'location' => array(
                    'id'        => $shift->getLocation()->getId(),
                    'latitude'  => $shift->getLocation()->getLatitude(),
                    'longitude' => $shift->getLocation()->getLongitude(),
                    'name'      => $shift->getLocation()->getName(),
                ),
                'unit' => array(
                    'id'   => $shift->getUnit()->getId(),
                    'name' => $shift->getUnit()->getName(),
                ),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result,
            )
        );
    }

    public function responsibleAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $person = $this->getPersonEntity();
        if ($person === null) {
            return $this->error(401, 'The access token is not valid');
        }

        $shift = $this->getShiftEntity();
        if ($shift === null) {
            return $this->error(404, 'The shift was not found');
        }

        if (!$shift->canHaveAsResponsible($this->getEntityManager(), $person)) {
            return $this->error(500, 'This person cannot be a responsible');
        }

        $shift->addResponsible(
            $this->getEntityManager(),
            new Responsible(
                $person,
                $this->getCurrentAcademicYear()
            )
        );

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function volunteerAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $person = $this->getPersonEntity();
        if ($person === null) {
            return $this->error(401, 'The access token is not valid');
        }

        $shift = $this->getShiftEntity();
        if ($shift === null) {
            return $this->error(404, 'The shift was not found');
        }

        if (!$shift->canHaveAsVolunteer($this->getEntityManager(), $person)) {
            return $this->error(500, 'This person cannot be a volunteer');
        }

        if ($shift->countVolunteers() >= $shift->getNbVolunteers()) {
            foreach (array_reverse($shift->getVolunteers()) as $volunteer) {
                if ($volunteer->getPerson()->isPraesidium($this->getCurrentAcademicYear())) {
                    $shift->removeVolunteer($volunteer);

                    $mailAddress = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('shift.mail');

                    $mailName = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('shift.mail_name');

                    $language = $volunteer->getPerson()->getLanguage();
                    if ($language === null) {
                        $language = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Language')
                            ->findOneByAbbrev('en');
                    }

                    $mailData = unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('shift.praesidium_removed_mail')
                    );

                    $message = $mailData[$language->getAbbrev()]['content'];
                    $subject = $mailData[$language->getAbbrev()]['subject'];

                    $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y h:i') . ' to ' . $shift->getEndDate()->format('d/m/Y h:i');

                    $mail = new Message();
                    $mail->setBody(str_replace('{{ shift }}', $shiftString, $message))
                        ->setFrom($mailAddress, $mailName)
                        ->addTo($volunteer->getPerson()->getEmail(), $volunteer->getPerson()->getFullName())
                        ->setSubject($subject);

                    if (getenv('APPLICATION_ENV') != 'development') {
                        $this->getMailTransport()->send($mail);
                    }

                    $this->getEntityManager()->remove($volunteer);
                    break;
                }
            }
        }

        $shift->addVolunteer(
            $this->getEntityManager(),
            new Volunteer(
                $person
            )
        );

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function signOutAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $person = $this->getPersonEntity();
        if ($person === null) {
            return $this->error(401, 'The access token is not valid');
        }

        $shift = $this->getShiftEntity();
        if ($shift === null) {
            return $this->error(404, 'The shift was not found');
        }

        if (!$shift->canSignout($this->getEntityManager())) {
            return $this->error(500, 'This person cannot be signed out');
        }

        $remove = $shift->removePerson($person);
        if ($remove !== null) {
            $this->getEntityManager()->remove($remove);
        }

        // TODO:  If a responsible signs out, and there's another praesidium member signed up as a volunteer, promote him

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return Person|null
     */
    private function getPersonEntity()
    {
        if ($this->getAccessToken() === null) {
            return null;
        }

        return $this->getAccessToken()->getPerson($this->getEntityManager());
    }

    /**
     * @return Shift|null
     */
    private function getShiftEntity()
    {
        if ($this->getRequest()->getPost('id') === null) {
            return null;
        }

        return $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findOneById($this->getRequest()->getPost('id'));
    }
}
