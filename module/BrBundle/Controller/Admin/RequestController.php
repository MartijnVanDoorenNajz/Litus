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

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Company\Request;
use Laminas\View\Model\ViewModel;

/**
 * RequestController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class RequestController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $vacancyRequests = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\Vacancy')
            ->findNewRequests();

        $internshipRequests = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\Internship')
            ->findNewRequests();

        $studentJobRequests = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\StudentJob')
            ->findNewRequests();

        return new ViewModel(
            array(
                'vacancyRequests'    => $vacancyRequests,
                'internshipRequests' => $internshipRequests,
                'studentJobRequests' => $studentJobRequests,
            )
        );
    }

    public function approveAction()
    {
        $request = $this->getRequestEntity();
        if ($request === null) {
            return new ViewModel();
        }

        $request->approveRequest();
        $request->handled();

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The request was succesfully approved.'
        );

        $this->redirect()->toRoute(
            'br_admin_request',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function rejectAction()
    {
        $request = $this->getRequestEntity();
        if ($request === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_admin_request_reject');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $request->rejectRequest($formData['reject_reason']);
                $request->handled();

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The request was succesfully rejected.'
                );

                $this->redirect()->toRoute(
                    'br_admin_request',
                    array(
                        'action' => 'manage',
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form'    => $form,
                'request' => $request,
            )
        );
    }

    /**
     * @return Request|null
     */
    private function getRequestEntity()
    {
        $request = $this->getEntityById('BrBundle\Entity\Company\Request');

        if (!($request instanceof Request)) {
            $this->flashMessenger()->error(
                'Error',
                'No request was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_request',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $request;
    }
}
