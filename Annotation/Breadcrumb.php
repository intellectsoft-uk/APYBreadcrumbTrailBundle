<?php

/*
 * This file is part of the APYBreadcrumbTrailBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\BreadcrumbTrailBundle\Annotation;

/**
 * @Annotation
 */
class Breadcrumb
{
    /**
     * @var string Title of the breadcrumb
     */
    private $title;

    /**
     * @var string The name of the route
     */
    private $routeName = null;

    /**
     * @var mixed An array of parameters for the route
     */
    private $routeParameters = array();
    
    /**
     * @var Boolean Whether to generate an absolute URL
     */
    private $routeAbsolute = false;

    /**
     * Constructor.
     *
     * @param Array $data An array of annotation values
     */
    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $data['title'] = $data['value'];
            unset($data['value']);
        }

        if (isset($data['route'])) {
            if (is_array($data['route'])) {
                foreach ($data['route'] as $key => $value) {
                    $method = 'setRoute'.$key;
                    if (!method_exists($this, $method)) {
                        throw new \BadMethodCallException(sprintf("Unknown property '%s' for the 'route' parameter on annotation '%s'.", $key, get_class($this)));
                    }
                    $this->$method($value);
                }
            }
            else {
                $data['routeName'] = $data['route'];
            }

            unset($data['route']);
        }

        foreach ($data as $key => $value) {
            $method = 'set'.$key;
            if (!method_exists($this, $method)) {
                throw new \BadMethodCallException(sprintf("Unknown property '%s' on annotation '%s'.", $key, get_class($this)));
            }
            $this->$method($value);
        }
    }

    /**
     * Sets the title.
     *
     * @param string $methods The title of the breadcrumb
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the name of the route
     *
     * @param string $routeName The name of the route
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;
    }

    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * Sets an array of parameters for the route
     *
     * @param mixed $routeParameters An array of parameters for the route
     */
    public function setRouteParameters($routeParameters)
    {
        $this->routeParameters = $routeParameters;
    }

    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    /**
     * Whether to generate an absolute URL
     *
     * @param Boolean $routeName Whether to generate an absolute URL
     */
    public function setRouteAbsolute($routeAbsolute)
    {
        $this->routeAbsolute = $routeAbsolute;
    }

    public function getRouteAbsolute()
    {
        return $this->routeAbsolute;
    }
}
