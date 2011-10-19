<?php

namespace Admin;

use Doctrine\ORM\EntityManager;

use \Admin\Form\Sale;

use \Litus\Entity\Cudi\Sales\SaleSession;

/**
 *
 * This class controlls management and adding of sale sessions
 * @author Alan
 *
 */
class SaleController extends \Litus\Controller\Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('manage');
    }
    
    public function manageAction() {
        
        $em = $this->getEntityManager();
        $this->view->sessions = $em->getRepository('Litus\Entity\Cudi\Sales\SaleSession')->findAll();
        
    }

    public function newAction() {

        $form = new Form\Sale\CashRegister( array(  '500p'=>0, '200p'=>0, '100p'=>0,
                                                    '50p'=>0, '20p'=>0, '10p'=>0,
                                                    '5p'=>0, '2p'=>0, '1p'=>0,
                                                    '0p5'=>0, '0p2'=>0, '0p1'=>0,
                                                    '0p05'=>0, '0p02'=>0, '0p01'=>0,
                                                    'Bank_Device_1'=>'0.0',
                                                    'Bank_Device_2'=>'0.0') );
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {

                $em = $this->getEntityManager();

                // create object from formData
                $saleSession = new SaleSession();
         //       $saleSession->manager = $em->getRepository('Litus\Entity\Users\Person')->getById($formData['manager']);
                $saleSession->setOpenDate( date_create( date('Y-m-d H:i:s', time() ) ) ); // now
                $saleSession->setCloseDate( date_create( date('Y-m-d H:i:s', 0 ) ) ); // 1 jan 1970

		$saleSession->setCloseAmount( 0 );

                $am = 0;
            //    $am += $formData['500'] * 50000;
            //    $am += $formData['200'] * 20000;
            //    $am += $formData['100'] * 10000;
            //    $am += $formData['50'] * 5000;
            //    $am += $formData['20'] * 2000;
            //    $am += $formData['10'] * 1000;
            //    $am += $formData['5'] * 500;
            //    $am += $formData['2'] * 200;
            //    $am += $formData['1'] * 100;
            //    $am += $formData['0p5'] * 50;
            //    $am += $formData['0p2'] * 20;
            //    $am += $formData['0p1'] * 10;
            //    $am += $formData['0p05'] * 5;
            //    $am += $formData['0p02'] * 2;
            //    $am += $formData['0p01'] * 1;
            //    $am += $formData['Bank_Device_1'] * 100;
            //    $am += $formData['Bank_Device_2'] * 100;

                $saleSession->setOpenAmount( $am );


                // persist object into database
                $em->persist( $saleSession );

                // bounce to sale interface
		$this->_forward('manage');

            }

        }

    }
}
