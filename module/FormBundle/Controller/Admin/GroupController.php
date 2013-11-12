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

namespace FormBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    FormBundle\Entity\Node\Group,
    FormBundle\Entity\Node\Group\Mapping,
    FormBundle\Entity\Node\Translation\Group as GroupTranslation,
    FormBundle\Form\Admin\Group\Add as AddForm,
    FormBundle\Form\Admin\Group\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * GroupController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GroupController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Group')
                ->findAllActive(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Group')
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
                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                $formData = $form->getFormData($formData);

                $group = new Group($this->getAuthentication()->getPersonObject());

                $this->getEntityManager()->persist($group);

                foreach($languages as $language) {
                    if ('' != $formData['title_' . $language->getAbbrev()] && '' != $formData['introduction_' . $language->getAbbrev()]) {
                        $translation = new GroupTranslation(
                            $group,
                            $language,
                            $formData['title_' . $language->getAbbrev()],
                            $formData['introduction_' . $language->getAbbrev()]
                        );

                        $this->getEntityManager()->persist($translation);
                    }
                }

                $startForm = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Form')
                    ->findOneById($formData['start_form']);

                $this->getEntityManager()->persist(new Mapping($startForm, $group, 1));

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The group was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'form_admin_group',
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

    public function editAction()
    {
        if (!($group = $this->_getGroup()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $group);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                $formData = $form->getFormData($formData);

                foreach($languages as $language) {
                    if ('' != $formData['title_' . $language->getAbbrev()] && '' != $formData['introduction_' . $language->getAbbrev()]) {
                        $translation = $group->getTranslation($language, false);

                        if (null === $translation) {
                            $translation = new GroupTranslation(
                                $group,
                                $language,
                                $formData['title_' . $language->getAbbrev()],
                                $formData['introduction_' . $language->getAbbrev()]
                            );
                        } else {
                            $translation->setTitle($formData['title_' . $language->getAbbrev()])
                                ->setIntroduction($formData['introduction_' . $language->getAbbrev()]);
                        }

                        $this->getEntityManager()->persist($translation);
                    } else {
                        $translation = $group->getTranslation($language, false);

                        if ($translation !== null) {
                            $this->getEntityManager()->remove($translation);
                        }
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The group was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'form_admin_group',
                    array(
                        'action' => 'edit',
                        'id' => $group->getId(),
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

    private function _getGroup()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the group!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group')
            ->findOneById($this->getParam('id'));

        if (null === $group) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No group with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $group;
    }
}