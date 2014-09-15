<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic\Factory\Resource;

use Assetic\Factory\Resource\ResourceInterface;

/**
 * Require asset resource.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireAssetResource implements ResourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function isFresh($timestamp)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return 'require_asset_resource';
    }
}
