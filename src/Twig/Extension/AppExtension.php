<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('min_to_hour', [$this, 'minutesToHours']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }

    public function minutesToHours($value)
    {
        if( $value < 60 || !$value ) return $value;

        $hours = floor( $value / 60 );
        $minutes = $value % 60;

        if( $minutes < 10 ) $minutes = "0" . $minutes;

        $time = sprintf("%sh%s" , $hours , $minutes);

        return $time;
    }
}
