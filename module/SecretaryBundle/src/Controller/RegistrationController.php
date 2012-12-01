<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace SecretaryBundle\Controller;

use CommonBundle\Component\Authentication\Authentication,
    CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\Users\People\Academic,
    CommonBundle\Entity\Users\Statuses\Organization as OrganizationStatus,
    CommonBundle\Entity\Users\Statuses\University as UniversityStatus,
    CudiBundle\Entity\Sales\Booking,
    DateTime,
    Imagick,
    SecretaryBundle\Entity\Organization\MetaData,
    SecretaryBundle\Entity\Registration,
    SecretaryBundle\Entity\Syllabus\StudyEnrollment,
    SecretaryBundle\Entity\Syllabus\SubjectEnrollment,
    SecretaryBundle\Form\Registration\Add as AddForm,
    SecretaryBundle\Form\Registration\Edit as EditForm,
    SecretaryBundle\Form\Registration\Subject\Add as SubjectForm,
    Zend\File\Transfer\Transfer as FileTransfer,
    Zend\View\Model\ViewModel;

/**
 * RegistrationController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RegistrationController extends \SecretaryBundle\Component\Controller\RegistrationController
{
    public function addAction()
    {
        if (null !== $this->getParam('identification')) {
            $academic = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Users\People\Academic')
                ->findOneByUniversityIdentification($this->getParam('identification'));
        } else {
            $academic = null;
        }

        try {
            $terms_and_conditions = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.terms_and_conditions_' . $this->getLanguage()->getAbbrev());
        } catch(\Exception $e) {
            $terms_and_conditions = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.terms_and_conditions_' . \Locale::getDefault());
        }

        if (null !== $academic) {
            $authentication = new Authentication(
                new ShibbolethAdapter(
                    $this->getEntityManager(),
                    'CommonBundle\Entity\Users\People\Academic',
                    'universityIdentification'
                ),
                $this->getServiceLocator()->get('authentication_doctrineservice')
            );

            $code = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Users\Shibboleth\Code')
                ->findLastByUniversityIdentification($this->getParam('identification'));

            $this->getEntityManager()->remove($code);
            $this->getEntityManager()->flush();

            $authentication->authenticate(
                $this->getParam('identification'), '', true
            );

            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'edit',
                )
            );

            return new ViewModel();
        }

        if ($this->getRequest()->isPost()) {
            if ($this->_isValidCode()) {
                $form = new AddForm($this->getCache(), $this->getEntityManager(), $this->getParam('identification'));

                $formData = $this->getRequest()->getPost();
                $formData['university_identification'] = $this->getParam('identification');
                $form->setData($formData);

                if ($form->isValid()) {
                    $formData = $form->getFormData($formData);

                    $roles = array(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName('guest'),
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName('student')
                    );

                    $universityEmail = preg_replace('/[^a-z0-9\.@]/i', '', iconv("UTF-8", "US-ASCII//TRANSLIT", $formData['university_email'])) . '@student.kuleuven.be';

                    $academic = new Academic(
                        $this->getParam('identification'),
                        $roles,
                        $formData['first_name'],
                        $formData['last_name'],
                        $formData['primary_email'] ? $formData['personal_email'] : $universityEmail,
                        $formData['phone_number'],
                        $formData['sex'],
                        $this->getParam('identification')
                    );

                    if ($formData['primary_address_address_city'] != 'other') {
                        $primaryCity = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Address\City')
                            ->findOneById($formData['primary_address_address_city']);
                        $primaryCityName = $primaryCity->getName();
                        $primaryPostal = $primaryCity->getPostal();
                        $primaryStreet = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Address\Street')
                            ->findOneById($formData['primary_address_address_street_' . $formData['primary_address_address_city']])
                            ->getName();
                    } else {
                        $primaryCityName = $formData['primary_address_address_city_other'];
                        $primaryStreet = $formData['primary_address_address_street_other'];
                        $primaryPostal = $formData['primary_address_address_postal_other'];
                    }

                    $academic->setBirthday(DateTime::createFromFormat('d/m/Y H:i', $formData['birthday'] . ' 00:00'))
                        ->addUniversityStatus(
                            new UniversityStatus(
                                $academic,
                                'student',
                                $this->getCurrentAcademicYear()
                            )
                        )
                        ->setPersonalEmail($formData['personal_email'])
                        ->setUniversityEmail($universityEmail)
                        ->setPrimaryAddress(
                            new Address(
                                $primaryStreet,
                                $formData['primary_address_address_number'],
                                $formData['primary_address_address_mailbox'],
                                $primaryPostal,
                                $primaryCityName,
                                'BE'
                            )
                        )
                        ->setSecondaryAddress(
                            new Address(
                                $formData['secondary_address_address_street'],
                                $formData['secondary_address_address_number'],
                                $formData['secondary_address_address_mailbox'],
                                $formData['secondary_address_address_postal'],
                                $formData['secondary_address_address_city'],
                                $formData['secondary_address_address_country']
                            )
                        );

                    $filePath = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('common.profile_path');

                    $file = new FileTransfer();
                    if ($file->receive()) {
                        $image = new Imagick($file->getFileName());
                        $image->cropThumbnailImage(320, 240);

                        if ($academic->getPhotoPath() != '' || $academic->getPhotoPath() !== null) {
                            $fileName = $academic->getPhotoPath();
                        } else {
                            $fileName = '';
                            do{
                                $fileName = sha1(uniqid());
                            } while (file_exists($filePath . '/' . $fileName));
                        }
                        $image->writeImage($filePath . '/' . $fileName);
                        $academic->setPhotoPath($fileName);
                    }

                    if ($formData['become_member']) {
                        $metaData = new MetaData(
                            $academic,
                            $this->getCurrentAcademicYear(),
                            $formData['become_member'],
                            $formData['irreeel'],
                            $formData['bakske'],
                            $formData['tshirt_size']
                        );

                        $registrationArticles = unserialize(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('cudi.registration_articles')
                        );

                        // Add the T-shirt too
                        $tshirts = unserialize(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('cudi.tshirt_article')
                        );
                        $registrationArticles[] = $tshirts[$formData['tshirt_size']];

                        $enableAssignment = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('cudi.enable_automatic_assignment');
                        $currentPeriod = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Stock\Period')
                            ->findOneActive();
                        $currentPeriod->setEntityManager($this->getEntityManager());

                        foreach ($registrationArticles as $registrationArticle) {
                            $booking = new Booking(
                                $this->getEntityManager(),
                                $academic,
                                $this->getEntityManager()
                                    ->getRepository('CudiBundle\Entity\Sales\Article')
                                    ->findOneById($registrationArticle),
                                'booked',
                                1,
                                true
                            );
                            $this->getEntityManager()->persist($booking);

                            if ($enableAssignment == '1') {
                                $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
                                if ($available > 0) {
                                    if ($available >= $booking->getNumber()) {
                                        $booking->setStatus('assigned', $this->getEntityManager());
                                    }
                                }
                            }
                        }

                    } else {
                        $metaData = new MetaData(
                            $academic,
                            $this->getCurrentAcademicYear(),
                            $formData['become_member'],
                            false,
                            $formData['bakske'],
                            null
                        );
                    }

                    $this->getEntityManager()->persist($metaData);

                    $academic->activate(
                        $this->getEntityManager(),
                        $this->getMailTransport()
                    );

                    $this->getEntityManager()->persist($academic);

                    $registration = new Registration(
                        $academic,
                        $this->getCurrentAcademicYear()
                    );
                    $this->getEntityManager()->persist($registration);

                    $this->getEntityManager()->flush();

                    $authentication = new Authentication(
                        new ShibbolethAdapter(
                            $this->getEntityManager(),
                            'CommonBundle\Entity\Users\People\Academic',
                            'universityIdentification'
                        ),
                        $this->getServiceLocator()->get('authentication_doctrineservice')
                    );

                    $code = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Users\Shibboleth\Code')
                        ->findLastByUniversityIdentification($this->getParam('identification'));

                    $this->getEntityManager()->remove($code);
                    $this->getEntityManager()->flush();

                    $authentication->authenticate(
                        $this->getParam('identification'), '', true
                    );

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'You are succesfully registered!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'secretary_registration',
                        array(
                            'action' => 'studies',
                        )
                    );

                    return new ViewModel();
                }

                return new ViewModel(
                    array(
                        'form' => $form,
                        'terms_and_conditions' => $terms_and_conditions,
                    )
                );
            }
        } else {
            if ($this->_isValidCode()) {
                $form = new AddForm($this->getCache(), $this->getEntityManager(), $this->getParam('identification'));

                return new ViewModel(
                    array(
                        'form' => $form,
                        'terms_and_conditions' => $terms_and_conditions,
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'registerShibbolethUrl' => $this->_getRegisterhibbolethUrl(),
            )
        );
    }

    public function editAction()
    {
        if (!($academic = $this->_getAcademic())) {
            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add',
                )
            );

            return new ViewModel();
        }

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        try {
            $terms_and_conditions = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.terms_and_conditions_' . $this->getLanguage()->getAbbrev());
        } catch(\Exception $e) {
            $terms_and_conditions = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.terms_and_conditions_' . \Locale::getDefault());
        }

        $form = new EditForm(
            $academic,
            $this->getCurrentAcademicYear(),
            $metaData,
            $this->getCache(),
            $this->getEntityManager(),
            $this->getParam('identification')
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $formData['university_identification'] = $this->getParam('identification');
            if ($metaData && $metaData->becomeMember())
                $formData['become_member'] = true;
            else
                $formData['become_member'] = isset($formData['become_member']) ? $formData['become_member'] : false;
            $form->setData($formData);

            $universityEmail = preg_replace('/[^a-z0-9\.@]/i', '', iconv("UTF-8", "US-ASCII//TRANSLIT", $formData['university_email'])) . '@student.kuleuven.be';

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $academic->setFirstName($formData['first_name'])
                    ->setLastName($formData['last_name'])
                    ->setEmail($formData['primary_email'] ? $formData['personal_email'] : $universityEmail)
                    ->setPhoneNumber($formData['phone_number'])
                    ->setSex($formData['sex'])
                    ->setBirthday(DateTime::createFromFormat('d/m/Y H:i', $formData['birthday'] . ' 00:00'))
                    ->setPersonalEmail($formData['personal_email'])
                    ->setUniversityEmail($universityEmail);

                if ($formData['primary_address_address_city'] != 'other') {
                    $primaryCity = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Address\City')
                        ->findOneById($formData['primary_address_address_city']);
                    $primaryCityName = $primaryCity->getName();
                    $primaryPostal = $primaryCity->getPostal();
                    $primaryStreet = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Address\Street')
                        ->findOneById($formData['primary_address_address_street_' . $formData['primary_address_address_city']])
                        ->getName();
                } else {
                    $primaryCityName = $formData['primary_address_address_city_other'];
                    $primaryStreet = $formData['primary_address_address_street_other'];
                    $primaryPostal = $formData['primary_address_address_postal_other'];
                }

                if (null !== $academic->getPrimaryAddress()) {
                    $academic->getPrimaryAddress()
                        ->setStreet($primaryStreet)
                        ->setNumber($formData['primary_address_address_number'])
                        ->setMailbox($formData['primary_address_address_mailbox'])
                        ->setPostal($primaryPostal)
                        ->setCity($primaryCityName)
                        ->setCountry('BE');
                } else {
                    $academic->setPrimaryAddress(
                        new Address(
                            $primaryStreet,
                            $formData['primary_address_address_number'],
                            $formData['primary_address_address_mailbox'],
                            $primaryPostal,
                            $primaryCityName,
                            'BE'
                        )
                    );
                }

                if (null !== $academic->getSecondaryAddress()) {
                    $academic->getSecondaryAddress()
                        ->setStreet($formData['secondary_address_address_street'])
                        ->setNumber($formData['secondary_address_address_number'])
                        ->setMailbox($formData['secondary_address_address_mailbox'])
                        ->setPostal($formData['secondary_address_address_postal'])
                        ->setCity($formData['secondary_address_address_city'])
                        ->setCountry($formData['secondary_address_address_country']);
                } else {
                    $academic->setSecondaryAddress(
                        new Address(
                            $formData['secondary_address_address_street'],
                            $formData['secondary_address_address_number'],
                            $formData['secondary_address_address_mailbox'],
                            $formData['secondary_address_address_postal'],
                            $formData['secondary_address_address_city'],
                            $formData['secondary_address_address_country']
                        )
                    );
                }

                if ($academic->canHaveUniversityStatus($this->getCurrentAcademicYear())) {
                    $status = new UniversityStatus(
                        $academic,
                        'student',
                        $this->getCurrentAcademicYear()
                    );
                    $academic->addUniversityStatus($status);
                }

                $filePath = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path');

                $file = new FileTransfer();
                if ($file->receive()) {
                    $image = new Imagick($file->getFileName());
                    $image->cropThumbnailImage(320, 240);

                    if ($academic->getPhotoPath() != '' || $academic->getPhotoPath() !== null) {
                        $fileName = $academic->getPhotoPath();
                    } else {
                        $fileName = '';
                        do{
                            $fileName = sha1(uniqid());
                        } while (file_exists($filePath . '/' . $fileName));
                    }
                    $image->writeImage($filePath . '/' . $fileName);
                    $academic->setPhotoPath($fileName);
                }

                $tshirts = unserialize(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.tshirt_article')
                );

                if (null !== $metaData) {
                    if (null !== $metaData->getTshirtSize()) {
                        $booking = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sales\Booking')
                            ->findOneAssignedByArticleAndPerson(
                                $this->getEntityManager()
                                    ->getRepository('CudiBundle\Entity\Sales\Article')
                                    ->findOneById($tshirts[$metaData->getTshirtSize()]),
                                $academic
                            );

                        if (null !== $booking)
                            $this->getEntityManager()->remove($booking);
                    }

                    $becomeMember = $metaData->becomeMember() ? true : $formData['become_member'];

                    if ($becomeMember) {
                        $metaData->setBecomeMember($becomeMember)
                            ->setReceiveIrReeelAtCudi($formData['irreeel'])
                            ->setBakskeByMail($formData['bakske'])
                            ->setTshirtSize($formData['tshirt_size']);
                    } else {
                        $metaData->setBakskeByMail($formData['bakske']);
                    }
                } else {
                    if ($formData['become_member']) {
                        $metaData = new MetaData(
                            $academic,
                            $this->getCurrentAcademicYear(),
                            $formData['become_member'],
                            $formData['irreeel'],
                            $formData['bakske'],
                            $formData['tshirt_size']
                        );
                    } else {
                        $metaData = new MetaData(
                            $academic,
                            $this->getCurrentAcademicYear(),
                            $formData['become_member'],
                            false,
                            $formData['bakske'],
                            null
                        );
                    }

                    $this->getEntityManager()->persist($metaData);
                }

                if ($metaData->becomeMember()) {
                    $hasShirt = false;
                    foreach ($tshirts as $tshirt) {
                        $booking = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sales\Booking')
                            ->findOneSoldOrAssignedOrBookedByArticleAndPerson(
                                $this->getEntityManager()
                                    ->getRepository('CudiBundle\Entity\Sales\Article')
                                    ->findOneById($tshirt),
                                $academic
                            );

                        if (null !== $booking) {
                            $hasShirt = true;
                            break;
                        }
                    }

                    $enableAssignment = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.enable_automatic_assignment');
                    $currentPeriod = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Stock\Period')
                        ->findOneActive();
                    $currentPeriod->setEntityManager($this->getEntityManager());

                    // Only make a new booking if no tshirt has been sold before
                    if (!$hasShirt) {
                        $booking = new Booking(
                            $this->getEntityManager(),
                            $academic,
                            $this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Sales\Article')
                                ->findOneById($tshirts[$formData['tshirt_size']]),
                            'booked',
                            1,
                            true
                        );

                        $this->getEntityManager()->persist($booking);

                        if ($enableAssignment == '1') {
                            $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
                            if ($available > 0) {
                                if ($available >= $booking->getNumber()) {
                                    $booking->setStatus('assigned', $this->getEntityManager());
                                }
                            }
                        }
                    }

                    // Book the other articles that should be booked on registration
                    $registrationArticles = unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('cudi.registration_articles')
                    );

                    foreach ($registrationArticles as $registrationArticle) {
                        $booking = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sales\Booking')
                            ->findOneSoldByArticleAndPerson(
                                $this->getEntityManager()
                                    ->getRepository('CudiBundle\Entity\Sales\Article')
                                    ->findOneById($registrationArticle),
                                $academic
                            );

                        // Already got this article, continue
                        if (null !== $booking)
                            continue;

                        $booking = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sales\Booking')
                            ->findOneAssignedByArticleAndPerson(
                                $this->getEntityManager()
                                    ->getRepository('CudiBundle\Entity\Sales\Article')
                                    ->findOneById($registrationArticle),
                                $academic
                            );

                        // Already booked this article, continue
                        if (null !== $booking)
                            continue;

                        $booking = new Booking(
                            $this->getEntityManager(),
                            $academic,
                            $this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Sales\Article')
                                ->findOneById($registrationArticle),
                            'booked',
                            1,
                            true
                        );
                        $this->getEntityManager()->persist($booking);

                        if ($enableAssignment == '1') {
                            $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
                            if ($available > 0) {
                                if ($available >= $booking->getNumber()) {
                                    $booking->setStatus('assigned', $this->getEntityManager());
                                }
                            }
                        }
                    }
                }

                $academic->activate(
                    $this->getEntityManager(),
                    $this->getMailTransport()
                );

                $registration = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());
                if (null === $registration) {
                    $registration = new Registration(
                        $academic,
                        $this->getCurrentAcademicYear()
                    );
                    $this->getEntityManager()->persist($registration);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'Your registration was succesfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'secretary_registration',
                    array(
                        'action' => 'studies',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'terms_and_conditions' => $terms_and_conditions,
            )
        );
    }

    public function studiesAction()
    {
        if (!($academic = $this->_getAcademic())) {
            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add',
                )
            );

            return new ViewModel();
        }

        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllParentsByAcademicYear($this->getCurrentAcademicYear());

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $studyIds = array();
        foreach($enrollments as $enrollment)
            $studyIds[] = $enrollment->getStudy()->getId();

        return new ViewModel(
            array(
                'studies' => $studies,
                'enrollments' => $studyIds,
            )
        );
    }

    public function saveStudiesAction()
    {
        $this->initAjax();

        $data = $this->getRequest()->getPost();

        if (!($academic = $this->_getAcademic())) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        foreach($enrollments as $enrollment)
            $this->getEntityManager()->remove($enrollment);

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        foreach($enrollments as $enrollment)
            $this->getEntityManager()->remove($enrollment);

        if (!empty($data['studies'])) {
            foreach($data['studies'] as $id) {
                $study = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($id);
                $this->getEntityManager()->persist(new StudyEnrollment($academic, $this->getCurrentAcademicYear(), $study));

                $subjects = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByStudyAndAcademicYear($study, $this->getCurrentAcademicYear());

                foreach($subjects as $subject) {
                    if ($subject->isMandatory())
                        $this->getEntityManager()->persist(new SubjectEnrollment($academic, $this->getCurrentAcademicYear(), $subject->getSubject()));
                }
            }
        }
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function subjectsAction()
    {
        if (!($academic = $this->_getAcademic())) {
            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add',
                )
            );

            return new ViewModel();
        }

        $form = new SubjectForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $this->getEntityManager()->persist(
                    new SubjectEnrollment(
                        $academic,
                        $this->getCurrentAcademicYear(),
                        $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Subject')
                            ->findOneById($formData['subject_id'])
                    )
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The subject was succesfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'account',
                    array(
                        'action' => 'subjects',
                    )
                );

                return new ViewModel(
                    array(
                        'currentAcademicYear' => $this->getCurrentAcademicYear(),
                    )
                );
            }
        }

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $mappings = array();
        $studySubjects = array();
        foreach($enrollments as $enrollment) {
            $subjects = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                ->findAllByStudyAndAcademicYear($enrollment->getStudy(), $this->getCurrentAcademicYear());
            $mappings[] = array(
                'enrollment' => $enrollment,
                'subjects' => $subjects,
            );
            foreach($subjects as $subject)
                $studySubjects[] = $subject->getSubject()->getId();
        }

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $subjectIds = array();
        $otherSubjects = array();
        foreach($enrollments as $enrollment) {
            $subjectIds[] = $enrollment->getSubject()->getId();

            if (!in_array($enrollment->getSubject()->getId(), $studySubjects))
                $otherSubjects[] = $enrollment->getSubject();
        }

        return new ViewModel(
            array(
                'form' => $form,
                'mappings' => $mappings,
                'enrollments' => $subjectIds,
                'currentAcademicYear' => $this->getCurrentAcademicYear(),
                'otherSubjects' => $otherSubjects,
            )
        );
    }

    public function saveSubjectsAction()
    {
        $this->initAjax();

        $data = $this->getRequest()->getPost();

        if (!($academic = $this->_getAcademic())) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }
        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        foreach($enrollments as $enrollment)
            $this->getEntityManager()->remove($enrollment);

        if (!empty($data['subjects'])) {
            foreach($data['subjects'] as $id) {
                $subject = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Subject')
                    ->findOneById($id);
                $this->getEntityManager()->persist(new SubjectEnrollment($academic, $this->getCurrentAcademicYear(), $subject));
            }
        }
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function completeAction()
    {
        if (!($academic = $this->_getAcademic())) {
            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add',
                )
            );

            return new ViewModel();
        }

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $mappings = array();
        foreach($studies as $enrollment) {
            $mappings[] = array(
                'enrollment' => $enrollment,
                'subjects' => $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByStudyAndAcademicYear($enrollment->getStudy(), $this->getCurrentAcademicYear())
            );
        }

        $subjects = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $subjectIds = array();
        foreach($subjects as $enrollment)
            $subjectIds[] = $enrollment->getSubject()->getId();

        return new ViewModel(
            array(
                'academicYear' => $this->getCurrentAcademicYear(),
                'academic' => $academic,
                'metaData' => $metaData,
                'studies' => $mappings,
                'subjects' => $subjectIds,
            )
        );
    }

    private function _getAcademic()
    {
        return $this->getAuthentication()->getPersonObject();
    }

    private function _isValidCode()
    {
        $code = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Shibboleth\Code')
            ->findLastByUniversityIdentification($this->getParam('identification'));

        if (null !== $code)
            return true;

        return false;
    }

    private function _getRegisterhibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        if ('%2F' != substr($shibbolethUrl, 0, -3))
            $shibbolethUrl .= '%2F';

        return $shibbolethUrl . '?source=register';
    }
}
