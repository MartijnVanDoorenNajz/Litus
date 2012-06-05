<?php
return array(
    'CudiBundle\Module'                                        => __DIR__ . '/Module.php',
    'CudiBundle\Component\Controller\ActionController'         => __DIR__ . '/src/Component/Controller/ActionController.php',
    'CudiBundle\Component\Controller\SupplierController'       => __DIR__ . '/src/Component/Controller/SupplierController.php',
    'CudiBundle\Component\Document\Generator\OrderPdf'         => __DIR__ . '/src/Component/Document/Generator/OrderPdf.php',
    'CudiBundle\Component\Document\Generator\OrderXml'         => __DIR__ . '/src/Component/Document/Generator/OrderXml.php',
    'CudiBundle\Component\Validator\ArticleBarcode'            => __DIR__ . '/src/Component/Validator/ArticleBarcode.php',
    'CudiBundle\Component\Validator\UniqueArticleBarcode'      => __DIR__ . '/src/Component/Validator/UniqueArticleBarcode.php',
    'CudiBundle\Controller\Admin\Article\CommentController'    => __DIR__ . '/src/Controller/Admin/Article/CommentController.php',
    'CudiBundle\Controller\Admin\Article\FileController'       => __DIR__ . '/src/Controller/Admin/Article/FileController.php',
    'CudiBundle\Controller\Admin\Article\SubjectMapController' => __DIR__ . '/src/Controller/Admin/Article/SubjectMapController.php',
    'CudiBundle\Controller\Admin\ArticleController'            => __DIR__ . '/src/Controller/Admin/ArticleController.php',
    'CudiBundle\Controller\Admin\InstallController'            => __DIR__ . '/src/Controller/Admin/InstallController.php',
    'CudiBundle\Controller\Admin\Sales\ArticleController'      => __DIR__ . '/src/Controller/Admin/Sales/ArticleController.php',
    'CudiBundle\Controller\Admin\Stock\DeliveryController'     => __DIR__ . '/src/Controller/Admin/Stock/DeliveryController.php',
    'CudiBundle\Controller\Admin\Stock\OrderController'        => __DIR__ . '/src/Controller/Admin/Stock/OrderController.php',
    'CudiBundle\Controller\Admin\Stock\PeriodController'       => __DIR__ . '/src/Controller/Admin/Stock/PeriodController.php',
    'CudiBundle\Controller\Admin\Stock\RetourController'       => __DIR__ . '/src/Controller/Admin/Stock/RetourController.php',
    'CudiBundle\Controller\Admin\Stock\StockController'        => __DIR__ . '/src/Controller/Admin/Stock/StockController.php',
    'CudiBundle\Controller\Admin\Supplier\SupplierController'  => __DIR__ . '/src/Controller/Admin/Supplier/SupplierController.php',
    'CudiBundle\Controller\Admin\Supplier\UserController'      => __DIR__ . '/src/Controller/Admin/Supplier/UserController.php',
    'CudiBundle\Controller\Supplier\ArticleController'         => __DIR__ . '/src/Controller/Supplier/ArticleController.php',
    'CudiBundle\Controller\Supplier\AuthController'            => __DIR__ . '/src/Controller/Supplier/AuthController.php',
    'CudiBundle\Controller\Supplier\IndexController'           => __DIR__ . '/src/Controller/Supplier/IndexController.php',
    'CudiBundle\Entity\Article'                                => __DIR__ . '/src/Entity/Article.php',
    'CudiBundle\Entity\Articles\External'                      => __DIR__ . '/src/Entity/Articles/External.php',
    'CudiBundle\Entity\Articles\History'                       => __DIR__ . '/src/Entity/Articles/History.php',
    'CudiBundle\Entity\Articles\Internal'                      => __DIR__ . '/src/Entity/Articles/Internal.php',
    'CudiBundle\Entity\Articles\Options\Binding'               => __DIR__ . '/src/Entity/Articles/Options/Binding.php',
    'CudiBundle\Entity\Articles\Options\Color'                 => __DIR__ . '/src/Entity/Articles/Options/Color.php',
    'CudiBundle\Entity\Articles\SubjectMap'                    => __DIR__ . '/src/Entity/Articles/SubjectMap.php',
    'CudiBundle\Entity\Comments\Comment'                       => __DIR__ . '/src/Entity/Comments/Comment.php',
    'CudiBundle\Entity\Comments\Mapping'                       => __DIR__ . '/src/Entity/Comments/Mapping.php',
    'CudiBundle\Entity\Files\File'                             => __DIR__ . '/src/Entity/Files/File.php',
    'CudiBundle\Entity\Files\Mapping'                          => __DIR__ . '/src/Entity/Files/Mapping.php',
    'CudiBundle\Entity\Log'                                    => __DIR__ . '/src/Entity/Log.php',
    'CudiBundle\Entity\Sales\Article'                          => __DIR__ . '/src/Entity/Sales/Article.php',
    'CudiBundle\Entity\Sales\Booking'                          => __DIR__ . '/src/Entity/Sales/Booking.php',
    'CudiBundle\Entity\Sales\Discounts\Discount'               => __DIR__ . '/src/Entity/Sales/Discounts/Discount.php',
    'CudiBundle\Entity\Sales\Discounts\Template'               => __DIR__ . '/src/Entity/Sales/Discounts/Template.php',
    'CudiBundle\Entity\Sales\History'                          => __DIR__ . '/src/Entity/Sales/History.php',
    'CudiBundle\Entity\Sales\PayDesk'                          => __DIR__ . '/src/Entity/Sales/PayDesk.php',
    'CudiBundle\Entity\Sales\QueueItem'                        => __DIR__ . '/src/Entity/Sales/QueueItem.php',
    'CudiBundle\Entity\Sales\SaleItem'                         => __DIR__ . '/src/Entity/Sales/SaleItem.php',
    'CudiBundle\Entity\Sales\Session'                          => __DIR__ . '/src/Entity/Sales/Session.php',
    'CudiBundle\Entity\Stock\Deliveries\Delivery'              => __DIR__ . '/src/Entity/Stock/Deliveries/Delivery.php',
    'CudiBundle\Entity\Stock\Deliveries\Retour'                => __DIR__ . '/src/Entity/Stock/Deliveries/Retour.php',
    'CudiBundle\Entity\Stock\Orders\Item'                      => __DIR__ . '/src/Entity/Stock/Orders/Item.php',
    'CudiBundle\Entity\Stock\Orders\Order'                     => __DIR__ . '/src/Entity/Stock/Orders/Order.php',
    'CudiBundle\Entity\Stock\Period'                           => __DIR__ . '/src/Entity/Stock/Period.php',
    'CudiBundle\Entity\Stock\PeriodValues\Delta'               => __DIR__ . '/src/Entity/Stock/PeriodValues/Delta.php',
    'CudiBundle\Entity\Stock\PeriodValues\Start'               => __DIR__ . '/src/Entity/Stock/PeriodValues/Start.php',
    'CudiBundle\Entity\Supplier'                               => __DIR__ . '/src/Entity/Supplier.php',
    'CudiBundle\Entity\Users\People\Supplier'                  => __DIR__ . '/src/Entity/Users/People/Supplier.php',
    'CudiBundle\Form\Admin\Article\Add'                        => __DIR__ . '/src/Form/Admin/Article/Add.php',
    'CudiBundle\Form\Admin\Article\Comment\Add'                => __DIR__ . '/src/Form/Admin/Article/Comment/Add.php',
    'CudiBundle\Form\Admin\Article\Edit'                       => __DIR__ . '/src/Form/Admin/Article/Edit.php',
    'CudiBundle\Form\Admin\Article\File\Add'                   => __DIR__ . '/src/Form/Admin/Article/File/Add.php',
    'CudiBundle\Form\Admin\Article\File\Edit'                  => __DIR__ . '/src/Form/Admin/Article/File/Edit.php',
    'CudiBundle\Form\Admin\Article\Mapping\Add'                => __DIR__ . '/src/Form/Admin/Article/Mapping/Add.php',
    'CudiBundle\Form\Admin\Sales\Article\Activate'             => __DIR__ . '/src/Form/Admin/Sales/Article/Activate.php',
    'CudiBundle\Form\Admin\Sales\Article\Add'                  => __DIR__ . '/src/Form/Admin/Sales/Article/Add.php',
    'CudiBundle\Form\Admin\Sales\Article\Edit'                 => __DIR__ . '/src/Form/Admin/Sales/Article/Edit.php',
    'CudiBundle\Form\Admin\Stock\Deliveries\Add'               => __DIR__ . '/src/Form/Admin/Stock/Deliveries/Add.php',
    'CudiBundle\Form\Admin\Stock\Deliveries\AddDirect'         => __DIR__ . '/src/Form/Admin/Stock/Deliveries/AddDirect.php',
    'CudiBundle\Form\Admin\Stock\Deliveries\Retour'            => __DIR__ . '/src/Form/Admin/Stock/Deliveries/Retour.php',
    'CudiBundle\Form\Admin\Stock\Orders\Add'                   => __DIR__ . '/src/Form/Admin/Stock/Orders/Add.php',
    'CudiBundle\Form\Admin\Stock\Orders\AddDirect'             => __DIR__ . '/src/Form/Admin/Stock/Orders/AddDirect.php',
    'CudiBundle\Form\Admin\Stock\Update'                       => __DIR__ . '/src/Form/Admin/Stock/Update.php',
    'CudiBundle\Form\Admin\Supplier\Add'                       => __DIR__ . '/src/Form/Admin/Supplier/Add.php',
    'CudiBundle\Form\Admin\Supplier\Edit'                      => __DIR__ . '/src/Form/Admin/Supplier/Edit.php',
    'CudiBundle\Form\Admin\Supplier\User\Add'                  => __DIR__ . '/src/Form/Admin/Supplier/User/Add.php',
    'CudiBundle\Form\Admin\Supplier\User\Edit'                 => __DIR__ . '/src/Form/Admin/Supplier/User/Edit.php',
    'CudiBundle\Repository\Article'                            => __DIR__ . '/src/Repository/Article.php',
    'CudiBundle\Repository\Articles\AcademicYearMap'           => __DIR__ . '/src/Repository/Articles/AcademicYearMap.php',
    'CudiBundle\Repository\Articles\External'                  => __DIR__ . '/src/Repository/Articles/External.php',
    'CudiBundle\Repository\Articles\History'                   => __DIR__ . '/src/Repository/Articles/History.php',
    'CudiBundle\Repository\Articles\Internal'                  => __DIR__ . '/src/Repository/Articles/Internal.php',
    'CudiBundle\Repository\Articles\Options\Binding'           => __DIR__ . '/src/Repository/Articles/Options/Binding.php',
    'CudiBundle\Repository\Articles\Options\Color'             => __DIR__ . '/src/Repository/Articles/Options/Color.php',
    'CudiBundle\Repository\Articles\SubjectMap'                => __DIR__ . '/src/Repository/Articles/SubjectMap.php',
    'CudiBundle\Repository\Comments\Comment'                   => __DIR__ . '/src/Repository/Comments/Comment.php',
    'CudiBundle\Repository\Comments\Mapping'                   => __DIR__ . '/src/Repository/Comments/Mapping.php',
    'CudiBundle\Repository\Files\File'                         => __DIR__ . '/src/Repository/Files/File.php',
    'CudiBundle\Repository\Files\Mapping'                      => __DIR__ . '/src/Repository/Files/Mapping.php',
    'CudiBundle\Repository\Log'                                => __DIR__ . '/src/Repository/Log.php',
    'CudiBundle\Repository\Sales\Article'                      => __DIR__ . '/src/Repository/Sales/Article.php',
    'CudiBundle\Repository\Sales\Booking'                      => __DIR__ . '/src/Repository/Sales/Booking.php',
    'CudiBundle\Repository\Sales\Discounts\Discount'           => __DIR__ . '/src/Repository/Sales/Discounts/Discount.php',
    'CudiBundle\Repository\Sales\Discounts\Template'           => __DIR__ . '/src/Repository/Sales/Discounts/Template.php',
    'CudiBundle\Repository\Sales\History'                      => __DIR__ . '/src/Repository/Sales/History.php',
    'CudiBundle\Repository\Sales\PayDesk'                      => __DIR__ . '/src/Repository/Sales/PayDesk.php',
    'CudiBundle\Repository\Sales\QueueItem'                    => __DIR__ . '/src/Repository/Sales/QueueItem.php',
    'CudiBundle\Repository\Sales\SaleItem'                     => __DIR__ . '/src/Repository/Sales/SaleItem.php',
    'CudiBundle\Repository\Sales\Session'                      => __DIR__ . '/src/Repository/Sales/Session.php',
    'CudiBundle\Repository\Stock\Deliveries\Delivery'          => __DIR__ . '/src/Repository/Stock/Deliveries/Delivery.php',
    'CudiBundle\Repository\Stock\Deliveries\Retour'            => __DIR__ . '/src/Repository/Stock/Deliveries/Retour.php',
    'CudiBundle\Repository\Stock\Orders\Item'                  => __DIR__ . '/src/Repository/Stock/Orders/Item.php',
    'CudiBundle\Repository\Stock\Orders\Order'                 => __DIR__ . '/src/Repository/Stock/Orders/Order.php',
    'CudiBundle\Repository\Stock\Period'                       => __DIR__ . '/src/Repository/Stock/Period.php',
    'CudiBundle\Repository\Stock\PeriodValues\Delta'           => __DIR__ . '/src/Repository/Stock/PeriodValues/Delta.php',
    'CudiBundle\Repository\Stock\PeriodValues\Start'           => __DIR__ . '/src/Repository/Stock/PeriodValues/Start.php',
    'CudiBundle\Repository\Supplier'                           => __DIR__ . '/src/Repository/Supplier.php',
    'CudiBundle\Repository\Users\People\Supplier'              => __DIR__ . '/src/Repository/Users/People/Supplier.php',
);