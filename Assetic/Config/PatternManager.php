<?php

/*
 * This file is part of the Fxp RequireAssetBundle package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\RequireAssetBundle\Assetic\Config;

use Fxp\Bundle\RequireAssetBundle\Exception\BadMethodCallException;

/**
 * Config pattern manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PatternManager implements PatternManagerInterface
{
    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->defaults = array();
        $this->locked = false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDefaultPattern($pattern)
    {
        return in_array($pattern, $this->defaults);
    }

    /**
     * {@inheritdoc}
     */
    public function addDefaultPattern($pattern)
    {
        if ($this->locked) {
            throw new BadMethodCallException('PatternManager methods cannot be accessed when the manager is locked');
        }

        if (!$this->hasDefaultPattern($pattern)) {
            $this->defaults[] = $pattern;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addDefaultPatterns(array $patterns)
    {
        foreach ($patterns as $pattern) {
            $this->addDefaultPattern($pattern);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeDefaultPattern($pattern)
    {
        if ($this->locked) {
            throw new BadMethodCallException('PatternManager methods cannot be accessed when the manager is locked');
        }

        if (false !== $pos = array_search($pattern, $this->defaults)) {
            array_splice($this->defaults, $pos, 1);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultPatterns()
    {
        $this->locked = true;

        return $this->defaults;
    }
}
