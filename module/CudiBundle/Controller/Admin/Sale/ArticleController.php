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

namespace CudiBundle\Controller\Admin\Sale;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Form\Admin\Sales\Article\Add as AddForm,
    CudiBundle\Form\Admin\Sales\Article\Edit as EditForm,
    CudiBundle\Form\Admin\Sales\Article\Mail as MailForm,
    CudiBundle\Entity\Log\Sale\ProfVersion as ProfVersionLog,
    CudiBundle\Entity\Sale\Article as SaleArticle,
    CudiBundle\Entity\Sale\Article\History,
    CudiBundle\Entity\Sale\SaleItem,
    CudiBundle\Entity\Log\Article\Sale\Bookable as BookableLog,
    CudiBundle\Entity\Log\Article\Sale\Unbookable as UnbookableLog,
    DateTime,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYear();
        $semester = $this->_getSemester();

        if (null !== $this->getParam('field'))
            $articles = $this->_search($academicYear, $semester);

        if (!isset($articles)) {
            $articles = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findAllByAcademicYear($academicYear, $semester);
        }

        $paginator = $this->paginator()->createFromArray(
            $articles,
            $this->getParam('page')
        );

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'currentSemester' => $semester,
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
                'currentAcademicYear' => $this->getCurrentAcademicYear(),
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $article->setEntityManager($this->getEntityManager());

        $currentAcademicYear = $this->getCurrentAcademicYear();
        $previousAcademicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStart(
                new DateTime(
                    str_replace(
                        '{{ year }}',
                        $currentAcademicYear->getStartDate()->format('Y') - 1,
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('start_organization_year')
                    )
                )
            );

        if (null !== $article->getSaleArticle($previousAcademicYear)) {
            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'activate',
                    'id' => $article->getSaleArticle($previousAcademicYear)->getId(),
                )
            );

            return new ViewModel();
        }

        $form = new AddForm($this->getEntityManager());

        $precalculatedSellPrice = $article->isInternal() ? $article->precalculateSellPrice($this->getEntityManager()) : 0;
        $precalculatedPurchasePrice = $article->isInternal() ? $article->precalculatePurchasePrice($this->getEntityManager()) : 0;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $supplier = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Supplier')
                    ->findOneById($formData['supplier']);

                $saleArticle = new SaleArticle(
                    $article,
                    $formData['barcode'],
                    $formData['purchase_price'],
                    $formData['sell_price'],
                    $formData['bookable'],
                    $formData['unbookable'],
                    $formData['sellable'],
                    $supplier,
                    $formData['can_expire']
                );

                $this->getEntityManager()->persist($saleArticle);

                if (isset($formData['bookable']))
                    $this->getEntityManager()->persist(new BookableLog($this->getAuthentication()->getPersonObject(), $saleArticle));

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The sale article was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article',
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
                'article' => $article,
                'precalculatedSellPrice' => $precalculatedSellPrice,
                'precalculatedPurchasePrice' => $precalculatedPurchasePrice,
            )
        );
    }

    public function editAction()
    {
        if (!($saleArticle = $this->_getSaleArticle()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $saleArticle);

        $precalculatedSellPrice = $saleArticle->getMainArticle()->isInternal() ? $saleArticle->getMainArticle()->precalculateSellPrice($this->getEntityManager()) : 0;
        $precalculatedPurchasePrice = $saleArticle->getMainArticle()->isInternal() ? $saleArticle->getMainArticle()->precalculatePurchasePrice($this->getEntityManager()) : 0;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $history = new History($saleArticle);
                $this->getEntityManager()->persist($history);

                $supplier = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Supplier')
                    ->findOneById($formData['supplier']);

                $saleArticle->setBarcode($formData['barcode'])
                    ->setPurchasePrice($formData['purchase_price'])
                    ->setSellPrice($formData['sell_price'])
                    ->setIsBookable($formData['bookable'])
                    ->setIsUnbookable($formData['unbookable'])
                    ->setIsSellable($formData['sellable'])
                    ->setSupplier($supplier)
                    ->setCanExpire($formData['can_expire']);

                $article = $saleArticle->getMainArticle();
                if ($article->isInternal()) {
                    $cachePath = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.front_page_cache_dir');
                    if (null !== $article->getFrontPage() && file_exists($cachePath . '/' . $article->getFrontPage())) {
                        unlink($cachePath . '/' . $article->getFrontPage());
                        $article->setFrontPage();
                    }
                }

                if ($formData['bookable'] && !$history->getPrecursor()->isBookable())
                    $this->getEntityManager()->persist(new BookableLog($this->getAuthentication()->getPersonObject(), $saleArticle));
                elseif (!$formData['bookable'] && $history->getPrecursor()->isBookable())
                    $this->getEntityManager()->persist(new UnbookableLog($this->getAuthentication()->getPersonObject(), $saleArticle));

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The sale article was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article',
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
                'article' => $saleArticle,
                'precalculatedSellPrice' => $precalculatedSellPrice,
                'precalculatedPurchasePrice' => $precalculatedPurchasePrice,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($saleArticle = $this->_getSaleArticle()))
            return new ViewModel();

        $saleArticle->setIsHistory(true);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $semester = $this->_getSemester();
        $articles = $this->_search($this->getAcademicYear(), $semester);

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($articles, $numResults);

        $result = array();
        foreach($articles as $article) {
            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getMainArticle()->getTitle();
            $item->author = $article->getMainArticle()->getAuthors();
            $item->barcode = $article->getBarcode();
            $item->publisher = $article->getMainArticle()->getPublishers();
            $item->sellPrice = number_format($article->getSellPrice()/100, 2);
            $item->stockValue = $article->getStockValue();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function historyAction()
    {
        if (!($article = $this->_getSaleArticle()))
            return new ViewModel();

        $history = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article\History')
            ->findByArticle($article);

        return new ViewModel(
            array(
                'history' => $history,
                'current' => $article,
            )
        );
    }

    public function sellProfAction()
    {
        if (!($saleArticle = $this->_getSaleArticle()))
            return new ViewModel();

        $saleItem = new SaleItem(
            $saleArticle,
            1,
            0,
            null,
            $this->getEntityManager()
        );
        $this->getEntityManager()->persist($saleItem);

        $this->getEntityManager()->persist(new ProfVersionLog($this->getAuthentication()->getPersonObject(), $saleArticle));

        $saleArticle->setStockValue($saleArticle->getStockValue() - 1);

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The article is successfully sold to a prof'
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }

    public function typeaheadAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYear();

        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByTitleAndAcademicYearTypeAhead($this->getParam('string'), $academicYear);

        $result = array();
        foreach($articles as $article) {
            $item = (object) array();
            $item->id = $article->getId();
            $item->value = $article->getMainArticle()->getTitle() . ' - ' . $article->getBarcode();
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
        if (!($saleArticle = $this->_getSaleArticle()))
            return new ViewModel();

        $form = new MailForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $persons = array();

                $mailAddress = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.mail');

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.mail_name');

                foreach($formData['to'] as $status) {
                    $bookings = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Booking')
                        ->findAllByStatusAndArticleAndPeriod($status, $saleArticle, $this->getActiveStockPeriod());

                    foreach($bookings as $booking) {
                        if (isset($persons[$booking->getPerson()->getId()]))
                            continue;

                        $persons[$booking->getPerson()->getId()] = true;

                        $mail = new Message();
                        $mail->setBody($formData['message'])
                            ->setFrom($mailAddress, $mailName)
                            ->addTo($booking->getPerson()->getEmail(), $booking->getPerson()->getFullName())
                            ->setSubject($formData['subject']);

                        if ('development' != getenv('APPLICATION_ENV'))
                            $this->getMailTransport()->send($mail);
                     }
                }

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The email was successfully send to ' . sizeof($persons) . ' academics!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article',
                    array(
                        'action' => 'edit',
                        'id' => $saleArticle->getId(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'article' => $saleArticle,
                'form' => $form,
            )
        );
    }

    private function _search(AcademicYear $academicYear, $semester)
    {
        switch($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByTitleAndAcademicYear($this->getParam('string'), $academicYear, $semester);
            case 'author':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByAuthorAndAcademicYear($this->getParam('string'), $academicYear, $semester);
            case 'publisher':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByPublisherAndAcademicYear($this->getParam('string'), $academicYear, $semester);
            case 'barcode':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByBarcodeAndAcademicYear($this->getParam('string'), $academicYear, $semester);
        }
    }

    private function _getSaleArticle()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the article!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($this->getParam('id'));

        if (null === $article) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No article with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $article;
    }

    private function _getArticle()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the article!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneById($this->getParam('id'));

        if (null === $article) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No article with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $article;
    }

    private function _getSemester()
    {
        $semester = $this->getParam('semester');

        if ($semester == 1 || $semester == 2 || $semester == 3)
            return $semester;
        return 0;
    }
}
