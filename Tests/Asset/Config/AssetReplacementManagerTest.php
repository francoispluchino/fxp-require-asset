<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Tests\Asset\Config;

use Fxp\Component\RequireAsset\Asset\Config\AssetReplacementManager;
use PHPUnit\Framework\TestCase;

/**
 * Require Locale Manager Tests.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 *
 * @internal
 */
final class AssetReplacementManagerTest extends TestCase
{
    public function testBasic(): void
    {
        $arm = new AssetReplacementManager();
        $replacements = [
            '@asset1/path1.ext' => '@asset5/path1.ext',
            '@asset1/path2.ext' => '@asset5/path2.ext',
            '@asset2/path1.ext' => '@asset5/path3.ext',
        ];

        $this->assertFalse($arm->hasReplacement('@asset1/path1.ext'));

        $arm->addReplacement('@asset1/path1.ext', '@asset5/path1.ext');
        $this->assertTrue($arm->hasReplacement('@asset1/path1.ext'));

        $arm->addReplacements($replacements);
        $this->assertSame($replacements, $arm->getReplacements());

        $arm->removeReplacement('@asset1/path1.ext');
        $this->assertFalse($arm->hasReplacement('@asset1/path1.ext'));

        $this->assertSame('@asset5/path2.ext', $arm->getReplacement('@asset1/path2.ext'));
    }

    public function testInvalidGetReplacement(): void
    {
        $this->expectException(\Fxp\Component\RequireAsset\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "@asset1/path1.ext" asset replacement does not exist in require asset manager');

        $arm = new AssetReplacementManager();
        $arm->getReplacement('@asset1/path1.ext');
    }
}
