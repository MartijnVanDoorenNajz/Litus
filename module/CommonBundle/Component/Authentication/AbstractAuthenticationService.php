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

namespace CommonBundle\Component\Authentication;

use CommonBundle\Component\Authentication\Action;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Http\Header\Cookie;
use Zend\Http\Header\SetCookie;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;

/**
 * An authentication service superclass that handles the setting and clearing of the cookie.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class AbstractAuthenticationService extends \Zend\Authentication\AuthenticationService
{
    // phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.WriteOnlyProperty
    /**
     * @var string The namespace the storage handlers will use
     */
    private $namespace = '';
    // phpcs:enable

    /**
     * @var string The name of the cookie
     */
    private $cookie = '';

    /**
     * @var integer The duration of the authentication
     */
    protected $duration = -1;

    /**
     * @var Action The action that should be taken after authentication
     */
    protected $action;

    /**
     * @var Request The request
     */
    protected $request;

    /**
     * @var Cookie The received cookies
     */
    private $cookies;

    /**
     * @var Response The HTTP response
     */
    private $response;

    /**
     * @param StorageInterface $storage      The persistent storage handler
     * @param string           $namespace    The namespace the storage handlers will use
     * @param string           $cookieSuffix The cookie suffix that is used to store the session cookie
     * @param integer          $duration     The expiration time for the cookie
     * @param Action           $action       The action that should be taken after authentication
     */
    public function __construct(StorageInterface $storage, $namespace, $cookieSuffix, $duration, Action $action)
    {
        parent::__construct($storage);

        $this->namespace = $namespace;
        $this->duration = $duration;
        $this->cookie = $namespace . '_' . $cookieSuffix;
        $this->action = $action;
    }

    /**
     * @param Action $action The action that should be taken after authentication
     *
     * @return self
     */
    public function setAction(Action $action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param  Request $request The HTTP request
     * @return self
     */
    public function setRequest(Request $request)
    {
        $this->cookies = $request->getCookie() ? $request->getCookie() : null;
        $this->request = $request;

        return $this;
    }

    /**
     * @param  Response $response The HTTP response
     * @return self
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Checks whether external sites (e.g. wiki) can access this authentication
     *
     * @return boolean
     */
    public function isExternallyVisible()
    {
        return $this->hasCookie();
    }

    /**
     * Returns the value of the cookie
     *
     * @return string
     */
    protected function getCookie()
    {
        return $this->cookies[$this->cookie];
    }

    /**
     * Checks whether the cookie has been set.
     *
     * @return boolean
     */
    protected function hasCookie()
    {
        return isset($this->cookies[$this->cookie]);
    }

    /**
     * Clear the authentication cookie.
     */
    protected function clearCookie()
    {
        if (isset($this->cookies[$this->cookie])) {
            unset($this->cookies[$this->cookie]);
        }

        $this->response->getHeaders()->addHeader(
            (new SetCookie())
                ->setName($this->cookie)
                ->setValue('deleted')
                ->setExpires(0)
                ->setMaxAge(0)
                ->setPath('/')
                ->setDomain(str_replace(array('www.', ','), '', $this->request->getServer()->get('SERVER_NAME')))
        );
    }

    /**
     * Set the authentication cookie.
     *
     * @param string $value The cookie's value
     */
    protected function setCookie($value)
    {
        $this->clearCookie();

        $this->cookies[$this->cookie] = $value;

        $servername_parts = explode('.', $this->request->getServer()->get('SERVER_NAME'));
        $domain_parts = array_slice($servername_parts, -2);
        $domain = $domain_parts[0] . '.' . $domain_parts[1];

        $this->response->getHeaders()->addHeader(
            (new SetCookie())
                ->setName($this->cookie)
                ->setValue($value)
                ->setExpires(time() + $this->duration)
                ->setMaxAge($this->duration)
                ->setPath('/')
                ->setDomain($domain)
        );
    }
}
