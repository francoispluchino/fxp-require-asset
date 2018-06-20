<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Asset\Config;

use Fxp\Component\RequireAsset\Asset\Util\LocaleUtils;

/**
 * Require Locale Manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class LocaleManager implements LocaleManagerInterface
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string|null
     */
    protected $fallback;

    /**
     * @var array
     */
    protected $assets;

    /**
     * @var array
     */
    protected $mapAssets;

    /**
     * @var array
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @param string|null $locale   The current locale
     * @param string|null $fallback The fallback locale
     */
    public function __construct($locale = null, $fallback = null)
    {
        $this->setLocale(null !== $locale ? $locale : \Locale::getDefault());
        $this->setFallbackLocale($fallback);
        $this->assets = [];
        $this->mapAssets = [];
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = LocaleUtils::formatLocale($locale);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setFallbackLocale($locale)
    {
        $this->fallback = LocaleUtils::formatLocale($locale);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFallbackLocale()
    {
        return $this->fallback;
    }

    /**
     * {@inheritdoc}
     */
    public function hasLocalizedAsset($asset, $locale = null)
    {
        $locale = $this->getCurrentLocale($locale);

        return isset($this->assets[$locale][$asset]);
    }

    /**
     * {@inheritdoc}
     */
    public function addLocalizedAsset($asset, $locale, $localizedAsset)
    {
        $locale = LocaleUtils::formatLocale($locale);
        $this->assets[$locale][$asset] = (array) $localizedAsset;
        $this->mapAssets[$asset][$locale] = true;
        unset($this->cache[$this->getCacheKey($locale, $asset)]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeLocalizedAsset($asset, $locale)
    {
        $locale = LocaleUtils::formatLocale($locale);

        $this->cleanArray('assets', $locale, $asset);
        $this->cleanArray('mapAssets', $asset, $locale);
        unset($this->cache[$this->getCacheKey($locale, $asset)]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizedAsset($asset, $locale = null)
    {
        $locale = $this->getCurrentLocale($locale);
        $cacheKey = $this->getCacheKey($locale, $asset);

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $this->cache[$cacheKey] = $this->findLocalizedAsset($locale, $asset);

        return $this->cache[$cacheKey];
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizedAssets()
    {
        return $this->assets;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssetLocale($locale)
    {
        return isset($this->assets[$this->getCurrentLocale($locale)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetLocales($asset = null)
    {
        if (null !== $asset) {
            return isset($this->mapAssets[$asset])
                ? array_keys($this->mapAssets[$asset])
                : [];
        }

        return array_keys($this->assets);
    }

    /**
     * Find the localized asset.
     *
     * @param string $locale
     * @param string $asset
     *
     * @return string[]
     */
    protected function findLocalizedAsset($locale, $asset)
    {
        $localized = $this->doFindLocalizedAsset($locale, $asset);

        if (0 === count($localized)) {
            $localized = $this->doFindLocalizedAsset($this->getFallbackLocale(), $asset);
        }

        return $localized;
    }

    /**
     * Do find the localized asset.
     *
     * @param string $locale
     * @param string $asset
     *
     * @return string[]
     */
    protected function doFindLocalizedAsset($locale, $asset)
    {
        if (isset($this->assets[$locale][$asset])) {
            return $this->assets[$locale][$asset];
        }

        if (0 < $pos = strpos($locale, '_')) {
            return $this->doFindLocalizedAsset(substr($locale, 0, $pos), $asset);
        }

        return [];
    }

    /**
     * Get the current locale.
     *
     * @param string|null $locale The locale
     *
     * @return string
     */
    protected function getCurrentLocale($locale = null)
    {
        return null !== $locale
            ? LocaleUtils::formatLocale($locale)
            : $this->getLocale();
    }

    /**
     * Get the cache key of the localized asset.
     *
     * @param string $locale The locale
     * @param string $asset  The require asset
     *
     * @return string
     */
    protected function getCacheKey($locale, $asset)
    {
        return $locale.':'.$asset;
    }

    /**
     * Clean the array.
     *
     * @param string $property The property name
     * @param string $key      The first key of array
     * @param string $subKey   The sub key of array
     */
    protected function cleanArray($property, $key, $subKey)
    {
        $val = &$this->$property;
        unset($val[$key][$subKey]);

        if (array_key_exists($key, $val) && 0 === count($val[$key])) {
            unset($val[$key]);
        }
    }
}
