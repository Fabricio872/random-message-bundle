<?php

declare(strict_types=1);

namespace Fabricio872\RandomMessageBundle\Twig\Extension;

use Fabricio872\RandomMessageBundle\RandomMessage;
use Twig\Extension\AbstractExtension;
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
