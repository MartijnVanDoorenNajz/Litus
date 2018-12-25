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

namespace CommonBundle\Controller;

use CommonBundle\Entity\User\Person\Academic;
use DateInterval;
use DateTime;
use Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class IndexController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $notifications = $this->getEntityManager()
            ->getRepository('NotificationBundle\Entity\Node\Notification')
            ->findAllActive();

        return new ViewModel(
            array(
                'bookings'       => $this->getBookings(),
                'calendarItems'  => $this->getCalendarItems(),
                'wiki'           => $this->getWiki(),
                'cudi'           => $this->getCudiInfo(),
                'shop'           => $this->getShopInfo(),
                'newsItems'      => $this->getNewsItems(),
                'notifications'  => $notifications,
                'piwik'          => $this->getPiwikInfo(),
                'sportInfo'      => $this->getSportResults(),
                'myShifts'       => $this->getMyShifts(),
                'myPocers'       => $this->getMyPocers(),
                'pocUrl'         => $this->getPocUrl(),
                'pocUrlOverview' => $this->getPocUrlOverview(),
                'profilePath'    => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
            )
        );
    }

    /**
     * @return array|null
     */
    private function getSportResults()
    {
        $showInfo = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.sport_info_on_homepage');

        if ($showInfo != '1') {
            return null;
        }

        $fileContents = @file_get_contents('data/cache/run-' . md5('run_result_page'));

        $resultPage = null;
        if ($fileContents !== false) {
            $resultPage = (array) json_decode($fileContents);
        }

        $returnArray = null;
        if ($resultPage !== null) {
            $teamId = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.run_team_id');

            $currentPlace = null;
            $teamData = null;
            foreach ($resultPage['teams'] as $place => $team) {
                if ($team->nb == $teamId) {
                    $currentPlace = $place;
                    $teamData = $team;
                }
            }

            if ($teamData !== null) {
                $behind = 0;
                if ($currentPlace !== null && $currentPlace > 0) {
                    $firstData = $resultPage['teams'][0];
                    $behind = round(($firstData->laps + $firstData->position) - ($teamData->laps + $teamData->position), 2);
                }

                $returnArray = array(
                    'nbLaps'     => $teamData->laps,
                    'position'   => round($teamData->position * 100),
                    'speed'      => round($teamData->speed, 2),
                    'behind'     => $behind,
                    'currentLap' => $this->getEntityManager()
                        ->getRepository('SportBundle\Entity\Lap')
                        ->findCurrent($this->getCurrentAcademicYear()),
                );
            }
        }

        return $returnArray;
    }

    /**
     * @return array|null
     */
    private function getBookings()
    {
        $bookings = null;
        if ($this->getAuthentication()->isAuthenticated()) {
            $bookings = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findAllOpenByPerson($this->getAuthentication()->getPersonObject());

            foreach ($bookings as $key => $booking) {
                if ($booking->getStatus() != 'assigned') {
                    unset($bookings[$key]);
                }
            }

            if (count($bookings) == 0) {
                $bookings = null;
            }
        }

        return $bookings;
    }

    /**
     * @return string|null
     */
    private function getPocUrl()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.pocUrl');
    }

    /**
     * @return string|null
     */
    private function getPocUrlOverview()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.pocUrlOverview');
    }

    /**
     * @return array
     */
    private function getNewsItems()
    {
        $maxAge = new DateTime();
        $maxAge->sub(
            new DateInterval(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('news.max_age_site')
            )
        );

        return $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Node\News')
            ->findNbSite(5, $maxAge);
    }

    /**
     * @return array
     */
    private function getCalendarItems()
    {
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive();

        $calendarItems = array();
        foreach ($events as $event) {
            $date = $event->getStartDate()->format('d-M');
            if (!isset($calendarItems[$date])) {
                $calendarItems[$date] = (object) array(
                    'date'   => $event->getStartDate(),
                    'events' => array(),
                );
            }
            $calendarItems[$date]->events[] = $event;
        }

        return $calendarItems;
    }

    /**
     * @return array
     */
    private function getCudiInfo()
    {
        $cudi = array();
        $cudi['currentOpeningHour'] = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour')
            ->findCurrent();

        $sessions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOpen();

        $saleLight = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.sale_light_version');

        if (!$saleLight && count($sessions) > 0) {
            $cudi['currentSession'] = $sessions[0];

            $cudi['currentStudents'] = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\QueueItem')
                ->findNbBySession($cudi['currentSession']);
        }

        $cudi['openingHours'] = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour')
            ->findPeriodFromNow('P14D');

        return $cudi;
    }

    /**
     * @return array|null
     */
    private function getPiwikInfo()
    {
        $enablePiwik = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.enable_piwik');

        if (getenv('APPLICATION_ENV') == 'development' || !$enablePiwik) {
            return null;
        }

        return array(
            'url' => parse_url(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.piwik_api_url'),
                PHP_URL_HOST
            ),
            'site_id' => $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.piwik_id_site'),
        );
    }

    /**
     * @return array|null
     */
    private function getMyShifts()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return null;
        }

        return $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($this->getAuthentication()->getPersonObject());
    }

    /**
     * @return array
     */
    private function getShopInfo()
    {
        return array(
            'enable' => $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shop.enable_shop_button_homepage'),
            'name' => $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shop.name'),
            'url' => $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shop.url_reservations'),
        );
    }

    /**
     * @return array
     */
    private function getWiki()
    {
        return array(
            'enable' => $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.wiki_button'),
            'url' => $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('wiki.url'),
        );
    }

    /**
     * @return array
     */
    private function getMyPocers()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return array(
                'enable' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.poc'),
                'pocItem' => null,
            );
        }

        $currentAcademicYear = $this->getCurrentAcademicYear();

        $pocers = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findPocersByAcademicAndAcademicYear($academic, $currentAcademicYear);
        $lastPocGroup = null;
        $pocGroupList = array();
        $pocItem = array();
        foreach ($pocers as $pocer) {
            $pocer->setEntityManager($this->getEntityManager());
            if ($lastPocGroup === null) {
                $pocGroupList[] = $pocer;
            } elseif ($lastPocGroup === $pocer->getGroupId()) {
                $pocGroupList[] = $pocer;
            } elseif ($lastPocGroup !== $pocer->getGroupId()) {
                $pocItem[] = array(
                    'groupId'      => $lastPocGroup,
                    'pocGroupList' => $pocGroupList,
                    'pocExample'  => $pocGroupList[0],
                );
                unset($pocGroupList);
                $pocGroupList = array();
                $pocGroupList[] = $pocer;
            }
            $lastPocGroup = $pocer->getGroupId();
        }
        if (count($pocGroupList) > 0) {
            $pocItem[] = array(
                'groupId'      => $lastPocGroup,
                'pocGroupList' => $pocGroupList,
                'pocExample'   => $pocGroupList[0],
            );
        }

        return
            array(
                'enable' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.poc'),
                'pocItem' => $pocItem,
            );
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return null;
        }

        $academic = $this->getAuthentication()->getPersonObject();

        if (!($academic instanceof Academic)) {
            return;
        }

        return $academic;
    }
}
