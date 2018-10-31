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

use CommonBundle\Component\Util\File\TmpFile;
use MailBundle\Component\Archive\Generator\MailingList\Zip;
use RuntimeException;
use Zend\Http\Headers;
use Zend\View\Model\ViewModel;

/**
 * MailController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class MailController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function aliasesAction()
    {
        $aliases = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Alias')
            ->findAll();

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Type' => 'text/plain',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'aliases' => $aliases,
            )
        );
    }

    public function getAliasesAction()
    {
        return $this->aliasesAction();
    }

    public function listsAction()
    {
        $lists = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList')
            ->findAll();

        $data = array();

        foreach ($lists as $list) {
            $entries = $this->getEntityManager()
                ->getRepository('MailBundle\Entity\MailingList\Entry')
                ->findByList($list);

            $addresses = array_map(
                function ($entry) {
                    return $entry->getEmailAddress();
                },
                $entries
            );
            $addressesString = implode(', ', $addresses);

            if (count($addresses) > 0) {
                $data[] = array('name' => $list->getName(), 'addresses' => $addressesString);
            }
        }

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Type' => 'text/plain',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    public function getListsAction()
    {
        return $this->listsAction();
    }

    public function listsArchiveAction()
    {
        throw new RuntimeException('The listsArchive endpoint has been deprecated');
    }

    public function getListsArchiveAction()
    {
        return $this->listsArchiveAction();
    }
}
