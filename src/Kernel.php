<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function __construct(string $environment, bool $debug)
    {
        $timezone = $_SERVER['APP_TIMEZONE'] ?? $_ENV['APP_TIMEZONE'] ?? 'UTC';
        if (!date_default_timezone_set($timezone)) {
            throw new InvalidArgumentException(sprintf('Le fuseau horaire "%s" est invalide.', $timezone));
        }

        parent::__construct($environment, $debug);
    }
}
