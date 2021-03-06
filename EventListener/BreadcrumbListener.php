<?php

/*
 * This file is part of the APYBreadcrumbTrailBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\BreadcrumbTrailBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

class BreadcrumbListener
{
    /**
     * @var Reader An Reader instance
     */
    protected $reader;

    /**
     *
     * @var Trail An Trail instance
     */
    protected $breadcrumbTrail;

    /**
     * Constructor.
     *
     * @param Reader $reader An Reader instance
     * @param Trail $breadcrumbTrail An Trail instance
     */
    public function __construct(Reader $reader, Trail $breadcrumbTrail)
    {
        $this->reader = $reader;
        $this->breadcrumbTrail = $breadcrumbTrail;
    }

    /**
     * @param FilterControllerEvent $event A FilterControllerEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
        }

        // Annotations from class
        $class = new \ReflectionClass($controller[0]);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class));
        }

        // Excludes duplicates of the controller breadcrumbs by using the forward()
        if (!$this->breadcrumbTrail->count()) {
            $this->addBreadcrumbsFromAnnotations($this->reader->getClassAnnotations($class));
        }

        // Annotations from method
        $method = $class->getMethod($controller[1]);
        $this->addBreadcrumbsFromAnnotations($this->reader->getMethodAnnotations($method));
    }

    /**
     * Add Breadcrumb from annotations to the trail.
     *
     * @param Array $annotations Array of Breadcrumb annotations
     */
    private function addBreadcrumbsFromAnnotations(array $annotations)
    {
        // requirements (@Breadcrumb)
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Breadcrumb) {
                $this->breadcrumbTrail->add(
                        $annotation->getTitle(),
                        $annotation->getRouteName(),
                        $annotation->getRouteParameters(),
                        $annotation->getRouteAbsolute()
                );
            }
        }
    }
}
