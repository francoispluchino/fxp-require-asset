<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Twig\Asset;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Interface of twig asset configuration with container service.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
interface TwigContainerAwareInterface extends TwigAssetInterface, ContainerAwareInterface
{
}
