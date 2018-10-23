<?php declare(strict_types=1);

namespace InjectionBundle\Annotation;

/**
 * Class Inject
 *
 * @author  Joe Mizzi <themizzi@me.com>
 *
 * @Annotation
 * @Target("CLASS")
 */
class Inject
{
    public $id;
    public $tags = [];
    public $arguments = [];
    public $environments = [];
    public $environmentStrategy = 'exclude';
    public $aliases = [];
    public $autowired = true;
    public $autoconfigured = true;
    public $public = false;
    public $lazy = false;
    public $abstract = false;
    public $parent;
    public $shared = true;
    public $methodCalls = [];
}