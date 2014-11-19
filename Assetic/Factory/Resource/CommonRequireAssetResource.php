<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic\Factory\Resource;

use Fxp\Component\RequireAsset\Assetic\Util\Utils;

/**
 * Common require asset resource.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class CommonRequireAssetResource extends AbstractRequireAssetResource
{
    /**
     * @var array
     */
    protected $inputs;

    /**
     * Constructor.
     *
     * @param string $name       The asset name
     * @param array  $inputs     The require assets
     * @param string $targetPath The asset target path
     * @param array  $filters    The asset filters
     * @param array  $options    The asset filters
     */
    public function __construct($name, array $inputs, $targetPath, array $filters = array(), array $options = array())
    {
        parent::__construct($name, $targetPath, $filters, $options);

        $this->inputs = array();

        foreach ($inputs as $input) {
            $this->inputs[] = '@' . Utils::formatName($input);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getInputs()
    {
        return $this->inputs;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFixedOptions()
    {
        return array(
            'fxp_require_common_asset' => true,
        );
    }
}
