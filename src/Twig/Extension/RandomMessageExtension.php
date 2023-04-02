<?php

namespace Fabricio872\RandomMessageBundle\Twig\Extension;

use App\Twig\Runtime\RandomMessageExtensionRuntime;
use Fabricio872\RandomMessageBundle\RandomMessage;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RandomMessageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('random_messge', [RandomMessage::class, 'getMessage']),
        ];
    }
}
