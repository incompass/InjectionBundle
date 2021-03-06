<?php declare(strict_types=1);

namespace Incompass\InjectionBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Argument
 *
 * @author  Joe Mizzi <themizzi@me.com>
 *
 * @Annotation
 * @Target("ANNOTATION")
 *
 * @codeCoverageIgnore
 */
class Argument
{
    /** @Required() */
    public $name;

    /** @Required() */
    public $value;
}