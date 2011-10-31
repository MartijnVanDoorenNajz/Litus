<?php

namespace Litus\Repository\General;

use Doctrine\ORM\EntityRepository;

/**
 * Config
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Config extends EntityRepository
{
    public function getConfigValue($key)
    {
        $config = $this->find($key);

        if($config === null)
            throw new \RuntimeException('Configuration entry ' . $key . ' not found');
        return $config->getValue();
    }

    public function findAllByPrefix($prefix)
    {
        $configs = $this->_em
            ->createQuery('SELECT c FROM Litus\Entity\General\Config c WHERE c.key LIKE \'' . $prefix . '.%\'')
            ->getResult();

        $result = array();
        foreach ($configs as $config) {
            $key = $config->getKey();
            $value = $config->getValue();

            $key = str_replace($prefix . '.','', $key);

            $result[$key] = $value;
        }

        return $result;
    }
}