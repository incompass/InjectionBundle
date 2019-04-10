<?php declare(strict_types=1);

namespace Incompass\InjectionBundle\Annotation;

/**
 * Class Inject
 *
 * @author  Joe Mizzi <themizzi@me.com>
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @codeCoverageIgnore
 */
class Inject
{
    public $id;
    public $class;
    public $decoratedService;
    public $parent;
    public $factory;

    public $aliases = [];
    public $arguments = [];
    public $environments = [];
    public $environmentGroups = [];
    public $methodCalls = [];
    public $tags = [];

    /**
     * @Enum({"exclude", "include"})
     * @var string
     */
    public $environmentStrategy = 'exclude';

    public $abstract = false;
    public $autoconfigured = true;
    public $autowired = true;
    public $lazy = false;
    public $public = false;
    public $shared = true;
}