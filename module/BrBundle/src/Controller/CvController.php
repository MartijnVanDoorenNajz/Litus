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

use BrBundle\Form\Cv\Add as AddForm,
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
        $form = new AddForm();

        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {

                $this->redirect()->toRoute(
                    'cv_index',
                    array(
                        'action' => 'complete',
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

    public function completeAction()
    {
        return new ViewModel();
    }
}
