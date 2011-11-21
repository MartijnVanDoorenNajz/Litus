<?php

namespace Litus\Repository\Cudi\Stock;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * StockItem
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StockItem extends EntityRepository
{
	public function findOneByBarcode($barcode)
    {
        $article = $this->getEntityManager()
			->getRepository('Litus\Entity\Cudi\Articles\StockArticles\External')
			->findOneByBarcode($barcode);
		if (null == $article) {
			$article = $this->getEntityManager()
				->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Internal')
				->findOneByBarcode($barcode);
		}
		
        return $article;
    }

	public function findAllInStock()
	{
		$query = $this->_em->createQueryBuilder();
		$resultSet = $query->select('i')
			->from('Litus\Entity\Cudi\Stock\StockItem', 'i')
			->where($query->expr()->gt('i.numberInStock', 0))
			->getQuery()
			->getResult();
			
		return $resultSet;
	}

	public function findAllByArticleTitle($title)
	{
		$query = $this->_em->createQueryBuilder();
		$resultSet = $query->select('i')
			->from('Litus\Entity\Cudi\Stock\StockItem', 'i')
			->innerJoin('i.article', 'a', Join::WITH, $query->expr()->like($query->expr()->lower('a.title'), ':title'))
			->setParameter('title', '%'.strtolower($title).'%')
			->orderBy('a.title', 'ASC')
			->getQuery()
			->getResult();
			
		return $resultSet;
	}
	
	public function findAllByArticleBarcode($barcode)
	{
		$query = $this->_em->createQueryBuilder();
		$internal = $query->select('a.id')
			->from('Litus\Entity\Cudi\Articles\StockArticles\Internal', 'a')
			->where($query->expr()->like($query->expr()->concat('a.barcode', '\'\''), ':barcode'))
			->setParameter('barcode', $barcode.'%')
			->getQuery()
			->getResult();
			
		$query = $this->_em->createQueryBuilder();
		$external = $query->select('a.id')
			->from('Litus\Entity\Cudi\Articles\StockArticles\External', 'a')
			->where($query->expr()->like($query->expr()->concat('a.barcode', '\'\''), ':barcode'))
			->setParameter('barcode', $barcode.'%')
			->getQuery()
			->getResult();
			
		$ids = array();
		foreach($external as $article)
			$ids[] = $article['id'];
		foreach($internal as $article)
			$ids[] = $article['id'];

		if (sizeof($ids) === 0)
			return array();

		$query = $this->_em->createQueryBuilder();
		$resultSet = $query->select('i')
			->from('Litus\Entity\Cudi\Stock\StockItem', 'i')
			->innerJoin('i.article', 'a')
			->where($query->expr()->in('a.id', $ids))
			->orderBy('a.title', 'ASC')
			->getQuery()
			->getResult();
			
		return $resultSet;
	}
	
	public function findAllByArticleSupplier($supplier)
	{
		$query = $this->_em->createQueryBuilder();
		$internal = $query->select('a.id')
			->from('Litus\Entity\Cudi\Articles\StockArticles\Internal', 'a')
			->innerJoin('a.supplier', 's', Join::WITH, $query->expr()->like($query->expr()->lower('s.name'), ':supplier'))
			->setParameter('supplier', '%'.strtolower($supplier).'%')
			->getQuery()
			->getResult();
			
		$query = $this->_em->createQueryBuilder();
		$external = $query->select('a.id')
			->from('Litus\Entity\Cudi\Articles\StockArticles\External', 'a')
			->innerJoin('a.supplier', 's', Join::WITH, $query->expr()->like($query->expr()->lower('s.name'), ':supplier'))
			->setParameter('supplier', '%'.strtolower($supplier).'%')
			->getQuery()
			->getResult();
			
		$ids = array();
		foreach($external as $article)
			$ids[] = $article['id'];
		foreach($internal as $article)
			$ids[] = $article['id'];

		if (sizeof($ids) === 0)
			return array();

		$query = $this->_em->createQueryBuilder();
		$resultSet = $query->select('i')
			->from('Litus\Entity\Cudi\Stock\StockItem', 'i')
			->innerJoin('i.article', 'a')
			->where($query->expr()->in('a.id', $ids))
			->orderBy('a.title', 'ASC')
			->getQuery()
			->getResult();
			
		return $resultSet;
	}

	public function assignAll()
	{
		$items = $this->getEntityManager()
			->getRepository('Litus\Entity\Cudi\Stock\StockItem')
			->findAllInStock();
		$counter = 0;
		
		$this->getEntityManager()
			->getRepository('Litus\Entity\Cudi\Sales\Booking')
			->expireBookings();
		$this->getEntityManager()->flush();
		
		foreach($items as $item) {
			$bookings = $this->getEntityManager()
				->getRepository('Litus\Entity\Cudi\Sales\Booking')
				->findAllBookedByArticle($item->getArticle(), 'ASC');
			
			$now = new \DateTime();
			foreach($bookings as $booking) {
				if ($item->getNumberAvailable() <= 0)
					break;
				
				if ($item->getNumberAvailable() < $booking->getNumber())
					continue;
				
				$counter++;
				$booking->setStatus('assigned');
				// TODO: send email
			}
		}
		
		return $counter;
	}
}