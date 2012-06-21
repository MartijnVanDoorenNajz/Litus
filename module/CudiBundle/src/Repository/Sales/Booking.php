<?php

namespace CudiBundle\Repository\Sales;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Component\Mail\Booking as BookingMail,
    CudiBundle\Entity\Sales\Article as ArticleEntity,
    CudiBundle\Entity\Stock\Period,
    DateTime,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr\Join,
    Zend\Mail\Transport;

/**
 * Booking
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Booking extends EntityRepository
{
    public function findAllActiveByPeriod(Period $period)
    {
    	$query = $this->_em->createQueryBuilder();
    	$query->select('b')
    		->from('CudiBundle\Entity\Sales\Booking', 'b')
    		->where(
    		    $query->expr()->andX(
        		    $query->expr()->orX(
        		        $query->expr()->eq('b.status', '\'booked\''),
        		        $query->expr()->eq('b.status', '\'assigned\'')
        		    ),
        		    $query->expr()->gt('b.bookDate', ':startDate'),
        		    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
        		)
    		)
    		->setParameter('startDate', $period->getStartDate());
    		
    		if (!$period->isOpen())
    		    $query->setParameter('endDate', $period->getEndDate());
    		
		$resultSet = $query->orderBy('b.bookDate', 'DESC')
    		->getQuery()
    		->getResult();
    		
    	return $resultSet;
    }
    
    public function findAllByPersonAndPeriod(Person $person, Period $period)
    {
    	$query = $this->_em->createQueryBuilder();
    	$query->select('b')
    		->from('CudiBundle\Entity\Sales\Booking', 'b')
    		->where(
    		    $query->expr()->andX(
    		        $query->expr()->eq('b.person', ':person'),
        		    $query->expr()->gt('b.bookDate', ':startDate'),
        		    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
        		)
    		)
    		->setParameter('person', $person->getId())
    		->setParameter('startDate', $period->getStartDate());
    		
    		if (!$period->isOpen())
    		    $query->setParameter('endDate', $period->getEndDate());
    		
    	$resultSet = $query->orderBy('b.bookDate', 'DESC')
    		->getQuery()
    		->getResult();
    		
    	return $resultSet;
    }
    
    public function findAllInactiveByPeriod(Period $period)
    {
    	$query = $this->_em->createQueryBuilder();
    	$query->select('b')
    		->from('CudiBundle\Entity\Sales\Booking', 'b')
    		->where(
    		    $query->expr()->andX(
    		        $query->expr()->not(
            		    $query->expr()->orX(
            		        $query->expr()->eq('b.status', '\'booked\''),
            		        $query->expr()->eq('b.status', '\'assigned\'')
            		    )
        		    ),
        		    $query->expr()->gt('b.bookDate', ':startDate'),
        		    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
        		)
    		)
    		->setParameter('startDate', $period->getStartDate());
    		
    		if (!$period->isOpen())
    		    $query->setParameter('endDate', $period->getEndDate());
    		
    	$resultSet = $query->orderBy('b.bookDate', 'DESC')
    		->getQuery()
    		->getResult();
    		
    	return $resultSet;
    }
    
    public function findAllByPersonNameAndTypeAndPeriod($person, $type, Period $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('b')
        	->from('CudiBundle\Entity\Sales\Booking', 'b')
        	->innerJoin('b.person', 'p', Join::WITH,
        	    $query->expr()->orX(
        			$query->expr()->like(
        				$query->expr()->concat(
        					$query->expr()->lower($query->expr()->concat('p.firstName', "' '")),
        					$query->expr()->lower('p.lastName')
        				),
        				':name'
        			),
        			$query->expr()->like(
        				$query->expr()->concat(
        					$query->expr()->lower($query->expr()->concat('p.lastName', "' '")),
        					$query->expr()->lower('p.firstName')
        				),
        				':name'
        			)
        		)
        	)
        	->where(
        	    $query->expr()->andX(
        	        $query->expr()->orX(
        	            $query->expr()->andX(
        	                $query->expr()->eq('\'active\'', ':type'),
        	                $query->expr()->orX(
        	                    $query->expr()->eq('b.status', '\'booked\''),
        	                    $query->expr()->eq('b.status', '\'assigned\'')
        	                )
        	            ),
        	            $query->expr()->andX(
        	                $query->expr()->eq('\'inactive\'', ':type'),
        	                $query->expr()->not(
            	                $query->expr()->orX(
            	                    $query->expr()->eq('b.status', '\'booked\''),
            	                    $query->expr()->eq('b.status', '\'assigned\'')
            	                )
            	            )
        	            )
        	        ),
        		    $query->expr()->gt('b.bookDate', ':startDate'),
        		    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
        		)
        	)
			->setParameter('name', '%'.strtolower($person).'%')
        	->setParameter('type', $type)
        	->setParameter('startDate', $period->getStartDate());
        	
    	if (!$period->isOpen())
    	    $query->setParameter('endDate', $period->getEndDate());
        	
        $resultSet = $query->orderBy('b.bookDate', 'DESC')
        	->getQuery()
        	->getResult();
        	
        return $resultSet;
    }
    
    public function findAllByArticleAndTypeAndPeriod($article, $type, Period $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('b')
        	->from('CudiBundle\Entity\Sales\Booking', 'b')
        	->innerJoin('b.article', 'a')
        	->innerJoin('a.mainArticle', 'm', Join::WITH,
        	    $query->expr()->like($query->expr()->lower('m.title'), ':article')
        	)
        	->where(
        	    $query->expr()->andX(
        	        $query->expr()->orX(
        	            $query->expr()->andX(
        	                $query->expr()->eq('\'active\'', ':type'),
        	                $query->expr()->orX(
        	                    $query->expr()->eq('b.status', '\'booked\''),
        	                    $query->expr()->eq('b.status', '\'assigned\'')
        	                )
        	            ),
        	            $query->expr()->andX(
        	                $query->expr()->eq('\'inactive\'', ':type'),
        	                $query->expr()->not(
            	                $query->expr()->orX(
            	                    $query->expr()->eq('b.status', '\'booked\''),
            	                    $query->expr()->eq('b.status', '\'assigned\'')
            	                )
            	            )
        	            )
        	        ),
        		    $query->expr()->gt('b.bookDate', ':startDate'),
        		    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
        		)
        	)
    		->setParameter('article', '%'.strtolower($article).'%')
        	->setParameter('type', $type)
        	->setParameter('startDate', $period->getStartDate());
        	
    	if (!$period->isOpen())
    	    $query->setParameter('endDate', $period->getEndDate());
        	
        $resultSet = $query->orderBy('b.bookDate', 'DESC')
        	->getQuery()
        	->getResult();
        	
        return $resultSet;
    }
    
    public function findAllByStatusAndTypeAndPeriod($status, $type, Period $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('b')
        	->from('CudiBundle\Entity\Sales\Booking', 'b')
        	->where(
        	    $query->expr()->andX(
        	        $query->expr()->orX(
        	            $query->expr()->andX(
        	                $query->expr()->eq('\'active\'', ':type'),
        	                $query->expr()->orX(
        	                    $query->expr()->eq('b.status', '\'booked\''),
        	                    $query->expr()->eq('b.status', '\'assigned\'')
        	                )
        	            ),
        	            $query->expr()->andX(
        	                $query->expr()->eq('\'inactive\'', ':type'),
        	                $query->expr()->not(
            	                $query->expr()->orX(
            	                    $query->expr()->eq('b.status', '\'booked\''),
            	                    $query->expr()->eq('b.status', '\'assigned\'')
            	                )
            	            )
        	            )
        	        ),
            	    $query->expr()->like($query->expr()->lower('b.status'), ':status'),
        		    $query->expr()->gt('b.bookDate', ':startDate'),
        		    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
        		)
        	)
    		->setParameter('status', '%'.strtolower($status).'%')
        	->setParameter('type', $type)
        	->setParameter('startDate', $period->getStartDate());
        	
    	if (!$period->isOpen())
    	    $query->setParameter('endDate', $period->getEndDate());
        	
        $resultSet = $query->orderBy('b.bookDate', 'DESC')
        	->getQuery()
        	->getResult();
        	
        return $resultSet;
    }
    
    public function findAllBookedArticles()
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
            
        $query = $this->_em->createQueryBuilder();
        $query->select('b')
        	->from('CudiBundle\Entity\Sales\Booking', 'b')
        	->where(
        	    $query->expr()->andX(
        		    $query->expr()->eq('b.status', '\'booked\''),
        		    $query->expr()->gt('b.bookDate', ':startDate'),
        		    $period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
        		)
        	)
        	->setParameter('startDate', $period->getStartDate());
        	
        	if (!$period->isOpen())
        	    $query->setParameter('endDate', $period->getEndDate());
        	
        $resultSet = $query->orderBy('b.bookDate', 'DESC')
        	->getQuery()
        	->getResult();
        	
        $articles = array();
        foreach($resultSet as $booking)
            $articles[$booking->getArticle()->getId()] = $booking->getArticle();
        	
        return $articles;
    }
    
    public function findAllBookedByArticleAndPeriod(ArticleEntity $article, Period $period)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('b')
        	->from('CudiBundle\Entity\Sales\Booking', 'b')
        	->where(
                $query->expr()->andX(
        			$query->expr()->eq('b.article', ':article'),
        			$query->expr()->eq('b.status', '\'booked\''),
        			$query->expr()->gt('b.bookDate', ':startDate'),
        			$period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
        		)
        	)
        	->setParameter('article', $article->getId())
    		->setParameter('startDate', $period->getStartDate());
    		
    	if (!$period->isOpen())
    	    $query->setParameter('endDate', $period->getEndDate());
    		
    	$resultSet = $query->orderBy('b.bookDate', 'ASC')
    	    ->getQuery()
        	->getResult();
        	
        return $resultSet;
    }
    
    public function findAllAssignedByPerson(Person $person)
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
            
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('b')
        	->from('CudiBundle\Entity\Sales\Booking', 'b')
        	->where(
        	    $query->expr()->andX(
        			$query->expr()->eq('b.person', ':person'),
        			$query->expr()->eq('b.status', '\'assigned\''),
        			$query->expr()->gt('b.bookDate', ':startDate'),
        			$period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
        		)
        	)
        	->setParameter(':person', $person->getId())
    		->setParameter('startDate', $period->getStartDate());
    		
    	if (!$period->isOpen())
    	    $query->setParameter('endDate', $period->getEndDate());
    		
    	$resultSet = $query->getQuery()
        	->getResult();
        	
        return $resultSet;
    }
    
    public function findAllOpenByPerson(Person $person)
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
            
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('b')
        	->from('CudiBundle\Entity\Sales\Booking', 'b')
        	->where($query->expr()->andX(
        			$query->expr()->eq('b.person', ':person'),
        			$query->expr()->neq('b.status', '\'sold\''),
        			$query->expr()->neq('b.status', '\'expired\''),
        			$query->expr()->gt('b.bookDate', ':startDate'),
        			$period->isOpen() ? '1=1' : $query->expr()->lt('b.bookDate', ':endDate')
        		)
        	)
        	->setParameter(':person', $person->getId())
    		->setParameter('startDate', $period->getStartDate());
    		
    	if (!$period->isOpen())
    	    $query->setParameter('endDate', $period->getEndDate());
    		
    	$resultSet = $query->getQuery()
        	->getResult();
        	
        return $resultSet;
    }
    
    public function findOneSoldByPersonAndArticle(Person $person, ArticleEntity $article)
    {
    	$query = $this->_em->createQueryBuilder();
    	$resultSet = $query->select('b')
    		->from('CudiBundle\Entity\Sales\Booking', 'b')
    		->where(
    		    $query->expr()->andX(
    				$query->expr()->eq('b.person', ':person'),
    				$query->expr()->eq('b.article', ':article'),
    				$query->expr()->eq('b.status', '\'sold\'')
    			)
    		)
    		->setParameter('person', $person->getId())
    		->setParameter('article', $article->getId())
    		->setMaxResults(1)
    		->getQuery()
    		->getResult();
    	
    	if (isset($resultSet[0]))
    		return $resultSet[0];
    	
    	return null;
    }
    
    public function assignAll(Transport $mailTransport)
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
            
        $period->setEntityManager($this->getEntityManager());
            
        $articles = $this->getEntityManager()
        	->getRepository('CudiBundle\Entity\Sales\Booking')
        	->findAllBookedArticles();

        $counter = 0;
        
        $persons = array();
        
        foreach($articles as $article) {
            $available = $article->getStockValue() - $period->getNbAssigned($article);
        	if ($available <= 0)
        		break;
        		
        	$bookings = $this->getEntityManager()
        		->getRepository('CudiBundle\Entity\Sales\Booking')
        		->findAllBookedByArticleAndPeriod($article, $period);
        		
        	foreach($bookings as $booking) {
        	    $counter++;
        	    
        		if ($available < $booking->getNumber()) {
        		    $new = new Booking(
        		    	$this->getEntityManager(),
        		    	$booking->getPerson(),
        		    	$booking->getArticle(),
        		    	'booked',
        		    	$booking->getNumber() - $available
        		    );
        		    
        		    $booking->setNumber($available);
        		}
        		
   		        $booking->setStatus('assigned');
        		
        		if (!isset($persons[$booking->getPerson()->getId()]))
        			$persons[$booking->getPerson()->getId()] = array('person' => $booking->getPerson(), 'bookings' => array());
        		
        		$persons[$booking->getPerson()->getId()]['bookings'][] = $booking;
        	}
        }
        $this->getEntityManager()->flush();
        
        $message = $this->_em
        	->getRepository('CommonBundle\Entity\General\Config')
        	->getConfigValue('cudi.booking_assigned_mail');
        	
        $subject = $this->_em
        	->getRepository('CommonBundle\Entity\General\Config')
        	->getConfigValue('cudi.booking_assigned_mail_subject');
        	
        $mailAddress = $this->_em
        	->getRepository('CommonBundle\Entity\General\Config')
        	->getConfigValue('cudi.mail');
        	
        $mailName = $this->_em
        	->getRepository('CommonBundle\Entity\General\Config')
        	->getConfigValue('cudi.mail_name');
        
        foreach($persons as $person)
            BookingMail::sendMail($mailTransport, $person['bookings'], $person['person'], $message, $subject, $mailAddress, $mailName);
        
        return $counter;
    }
    
    public function expireBookings()
    {
        $query = $this->_em->createQueryBuilder();
        $bookings = $query->select('b')
        	->from('CudiBundle\Entity\Sales\Booking', 'b')
        	->where(
        	    $query->expr()->andX(
        		    $query->expr()->eq('b.status', '\'assigned\''),
        		    $query->expr()->lt('b.expirationDate', ':now')
        		)
        	)
        	->setParameter('now', new DateTime())
        	->getQuery()
        	->getResult();
                
        foreach($bookings as $booking) {
       		$booking->setStatus('expired');
        }
    }
}