<?php declare(strict_types=1);

namespace Incompass\InjectionBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Factory
 *
 * @author James Matsumura <james@casechek.com>
 *
 * @Annotation
 * @Target("ANNOTATION")
 *
 * @codeCoverageIgnore
 */
class Factory
{
    /** @Required() */
    public $class;

    /** @Required() */
    public $method;
}