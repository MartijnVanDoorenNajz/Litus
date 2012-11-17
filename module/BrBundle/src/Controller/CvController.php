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

namespace BrBundle\Controller;

use BrBundle\Entity\Cv\Entry as CvEntry,
    BrBundle\Entity\Cv\Language as CvLanguage,
    BrBundle\Form\Cv\Add as AddForm,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\Users\People\Academic,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * CvController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CvController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function cvAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        $message = null;
        $languageError = null;

        if ($person === null) {
            $message = 'Please log in to add your CV.';
        } else {
            if (!($person instanceof Academic)) {
                $message = 'You must be a student to add your CV.';
            } else {

                $temp = $this->_getBadAccountMessage($person);
                if ($temp !== null && '' !== $temp)
                    $message = $temp;

                $entry = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Cv\Entry')
                    ->findOneByAcademic($person);
                if ($entry) {
                    $message = 'You can only fill in the CV Book once.';
                }
            }
        }


        $open = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cv_book_open');

        if (!$open) {
            $message = 'The CV Book is currently not accepting entries.';
        }

        if ($message) {
            return new ViewModel(
                array(
                    'message' => $message,
                )
            );
        }

        $form = new AddForm($this->getEntityManager(), $person, $this->getCurrentAcademicYear(), $this->getLanguage());

        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();
            $formData = $form->addLanguages($formData);
            $form->setData($formData);

            if ($form->isValid()) {

                $address = new Address(
                    $person->getSecondaryAddress()->getStreet(),
                    $person->getSecondaryAddress()->getNumber(),
                    $person->getSecondaryAddress()->getMailbox(),
                    $person->getSecondaryAddress()->getPostal(),
                    $person->getSecondaryAddress()->getCity(),
                    $person->getSecondaryAddress()->getCountryCode()
                );

                $entry = new CvEntry(
                    $person,
                    $this->getCurrentAcademicYear(),
                    $person->getFirstName(),
                    $person->getLastName(),
                    $person->getBirthDay(),
                    $person->getSex(),
                    $person->getPhoneNumber(),
                    $person->getPersonalEmail(),
                    $address,
                    $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Study')
                        ->findOneById($formData['degree']),
                    $formData['bachelor_start'],
                    $formData['bachelor_end'],
                    $formData['master_start'],
                    $formData['master_end'],
                    $formData['additional_diplomas'],
                    $formData['erasmus_period'],
                    $formData['erasmus_location'],
                    $formData['computer_skills'],
                    $formData['experiences'],
                    $formData['thesis_title'],
                    $formData['thesis_summary'],
                    $formData['field_of_interest'],
                    $formData['mobility_europe'],
                    $formData['mobility_world'],
                    $formData['career_expectations'],
                    $formData['hobbies'],
                    $formData['profile_about']
                );

                for ($i = 0; $i < $formData['lang_count']; $i++) {
                    if (!isset($formData['lang_name' . $i]) || '' === $formData['lang_name' . $i])
                        continue;

                    $language = new CvLanguage($entry, $formData['lang_name' . $i],
                        $formData['lang_oral' . $i],
                        $formData['lang_written' . $i]);

                    $this->getEntityManager()->persist($language);
                }

                $this->getEntityManager()->persist($entry);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'cv_index',
                    array(
                        'action' => 'complete',
                    )
                );

                return new ViewModel();
            } else {
                if (!$form->isValidLanguages($formData)) {
                    $languageError = 'The number of languages must be between 1 and 5';
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'languageError' => $languageError,
                'oral_skills' => CvLanguage::$ORAL_SKILLS,
                'written_skills' => CvLanguage::$WRITTEN_SKILLS,
            )
        );
    }

    public function completeAction()
    {
        return new ViewModel();
    }

    private function _getBadAccountMessage(Academic $person) {
        $message = '';

        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($person, $this->getCurrentAcademicYear());

        if (empty($studies))
            $message = $message . '<li>Your Studies</li>';

        $address = $person->getSecondaryAddress();
        if ($address === null || '' == $address->getStreet() || '' == $address->getNumber() 
                || '' == $address->getPostal() || '' == $address->getCity() || '' == $address->getCountryCode())
            $message = $message . '<li>Your address</li>';

        if ('' == $person->getFirstName() || '' == $person->getLastName())
            $message = $message . '<li>Your name</li>';

        if ('' == $person->getPhoneNumber())
            $message = $message . '<li>Your phone number</li>';

        if ('' == $person->getPersonalEmail())
            $message = $message . '<li>Your personal email address</li>';

        if ('' == $person->getPhotoPath())
            $message = $message . '<li>Your photo</li>';

        if (null === $person->getBirthDay())
            $message = $message . '<li>Your birthday</li>';

        if ($message) {
            $message = 'The following information in your account is incorrect:<br/><ul>' . $message .
                '</ul>Please click <a href="/account">here</a> to edit your account.';
        }

        return $message;
    }
}
