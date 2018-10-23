<?php declare(strict_types=1);

namespace InjectionBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Tag
 *
 * @author  Joe Mizzi <themizzi@me.com>
 *
 * @Annotation
 * @Target("ANNOTATION")
 */
class Tag
{
    /** @Required()
     */
    public $name;

    public $attributes = [];
}