<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Asset\Util;

/**
 * Asset Locale Utils.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class LocaleUtils
{
    /**
     * Format the locale.
     *
     * @param string|null $locale The locale
     *
     * @return string
     */
    public static function formatLocale($locale)
    {
        if (\is_string($locale)) {
            $locale = strtolower($locale);
            $locale = str_replace('-', '_', $locale);
        }

        return $locale;
    }
}
