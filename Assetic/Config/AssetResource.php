<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Config;

use Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResourceInterface;
use Fxp\Component\RequireAsset\Assetic\Util\Utils;
use Fxp\Component\RequireAsset\Exception\InvalidConfigurationException;

/**
 * Interface of asset resource configuration (for asset manager).
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetResource implements AssetResourceInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $prettyName;

    /**
     * @var string
     */
    protected $classname;

    /**
     * @var string
     */
    protected $loader;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var int|null
     */
    protected $namePosition;

    /**
     * @param string   $name         The require asset name
     * @param string   $classname    The classname of resource
     * @param string   $loader       The assetic loader of resource
     * @param array    $arguments    The arguments of class constructor
     * @param int|null $namePosition The position of asset name in arguments
     */
    public function __construct($name, $classname, $loader, array $arguments, $namePosition = null)
    {
        $this->name = Utils::formatName($name);
        $this->prettyName = $name;
        $this->classname = $classname;
        $this->loader = $loader;
        $this->arguments = $arguments;
        $this->namePosition = $namePosition;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrettyName()
    {
        return $this->prettyName;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassname()
    {
        return $this->classname;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getArgumentNamePosition()
    {
        return $this->namePosition;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $ref = new \ReflectionClass($this->getClassname());
        $instance = $ref->newInstanceArgs($this->getValidArguments());

        if (!$instance instanceof RequireAssetResourceInterface) {
            throw new InvalidConfigurationException(sprintf('The "%s" class must extends the "Fxp\Component\RequireAsset\Assetic\Factory\Resource\RequireAssetResourceInterface" interface', $this->getClassname()));
        }

        return $instance;
    }

    /**
     * Get the arguments with new configuration.
     *
     * @return array
     */
    protected function getValidArguments()
    {
        $args = $this->getArguments();

        if (is_int($this->getArgumentNamePosition())) {
            $args[$this->getArgumentNamePosition()] = $this->getPrettyName();
        }

        return $args;
    }
}
