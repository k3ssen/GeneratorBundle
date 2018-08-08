<?php
declare(strict_types = 1);

namespace K3ssen\GeneratorBundle\Twig;

use Doctrine\Common\Inflector\Inflector;

class InflectorExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('tableize', [$this, 'tableize']),
            new \Twig_SimpleFilter('pluralize', [$this, 'pluralize']),
            new \Twig_SimpleFilter('singularize', [$this, 'singularize']),
            new \Twig_SimpleFilter('camelize', [$this, 'camelize']),
            new \Twig_SimpleFilter('classify', [$this, 'classify']),
        ];
    }

    public function tableize($string)
    {
        return Inflector::tableize($string);
    }

    public function pluralize($string)
    {
        return Inflector::pluralize($string);
    }

    public function singularize($string)
    {
        return Inflector::singularize($string);
    }

    public function camelize($string)
    {
        return Inflector::camelize($string);
    }

    public function classify($string)
    {
        return Inflector::classify($string);
    }
}
