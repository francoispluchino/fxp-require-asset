<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Twig\Extension;

use Fxp\Component\RequireAsset\Exception\Twig\RequireTagException;

/**
 * Require Asset Extension Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class RequireAssetExtensionTest extends AbstractAssetExtensionTest
{
    public function getRequireTwigTemplates()
    {
        $tests = [];

        foreach ($this->getRequireTwigTags() as $arguments) {
            foreach ($this->getRequireTwigTemplateConfigs() as $configArguments) {
                $tests[] = array_merge($arguments, $configArguments);
            }
        }

        return $tests;
    }

    /**
     * @dataProvider getRequireTwigTemplates
     *
     * @param string $tag
     * @param string $testFile
     * @param string $exceptionClass
     * @param string $exceptionMessage
     */
    public function testTwigTags($tag, $testFile, $exceptionClass = null, $exceptionMessage = null): void
    {
        if (null !== $exceptionClass) {
            $this->expectException($exceptionClass);
        }

        if (null !== $exceptionMessage) {
            if (0 === strpos($exceptionMessage, '/')) {
                $this->expectExceptionMessageRegExp($exceptionMessage);
            } else {
                $this->expectExceptionMessage($exceptionMessage);
            }
        }

        $this->replacementManager->addReplacement('@virtual_asset/js/asset.js', '@webpack/asset');
        $this->replacementManager->addReplacement('@virtual_asset/css/asset.css', '@webpack/asset');

        $this->doValidTagTest($tag, $testFile);
    }

    protected function getRequireTwigTemplateConfigs()
    {
        return [
            ['test'],
            ['test_multi_asset'],
            ['test_without_asset', '\Twig_Error_Syntax', '/The twig tag "(\w+)" require a lest one asset/'],
            ['test_replacement_asset'],
            ['test_optional_asset'],
            ['invalid_webpack_asset', RequireTagException::class, 'is not managed by the Webpack Require Asset Manager'],
        ];
    }
}
