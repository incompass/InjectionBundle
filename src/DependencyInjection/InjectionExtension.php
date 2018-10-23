<?php declare(strict_types=1);

namespace InjectionBundle\DependencyInjection;

use Doctrine\Common\Annotations\AnnotationReader;
use InjectionBundle\InjectProcessor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Finder\Finder;

/**
 * Class InjectionExtension
 *
 * @author  Joe Mizzi <themizzi@me.com>
 */
class InjectionExtension extends Extension
{
    private $processor;
    private $finder;
    private $reader;

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration([], $container);
        $config = $this->processConfiguration($configuration, $configs);
        $finder = $this->getFinder();
        $reader = $this->getReader();
        $projectDir = $container->getParameter('kernel.project_dir');
        foreach ($config['paths'] as $path => $prefix) {
            foreach ($finder->in($projectDir.'/'.$path)->name('*.php') as $file => $info) {
                $pathLen = \strlen($projectDir.'/'.$path);
                $classPath = str_replace('/', '\\', substr($file, $pathLen + 1, -4));
                $class = $prefix.$classPath;
                $reflectionClass = new \ReflectionClass($class);
                $processor = $this->getProcessor();

                foreach ($reader->getClassAnnotations($reflectionClass) as $annotation) {
                    $processor->process($annotation, $class, $container);
                }
            }
        }
    }

    /**
     * @return InjectProcessor
     * @codeCoverageIgnore
     */
    public function getProcessor(): InjectProcessor
    {
        if (!$this->processor) {
            $this->processor = new InjectProcessor();
        }

        return $this->processor;
    }

    /**
     * @param InjectProcessor $processor
     * @codeCoverageIgnore
     */
    public function setProcessor(InjectProcessor $processor): void
    {
        $this->processor = $processor;
    }

    /**
     * @return Finder
     * @codeCoverageIgnore
     */
    public function getFinder(): Finder
    {
        if (!$this->finder) {
            $this->finder = new Finder();
        }

        return $this->finder;
    }

    /**
     * @param Finder $finder
     * @codeCoverageIgnore
     */
    public function setFinder(Finder $finder): void
    {
        $this->finder = $finder;
    }

    /**
     * @return AnnotationReader
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @codeCoverageIgnore
     */
    public function getReader(): AnnotationReader
    {
        if (!$this->reader) {
            $this->reader = new AnnotationReader();
        }

        return $this->reader;
    }

    /**
     * @param AnnotationReader $reader
     * @codeCoverageIgnore
     */
    public function setReader(AnnotationReader $reader): void
    {
        $this->reader = $reader;
    }
}