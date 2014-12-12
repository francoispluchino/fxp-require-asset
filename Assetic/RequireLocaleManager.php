<?php

/*
 * This file is part of the Fxp Require Asset package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\RequireAsset\Assetic;

use Fxp\Component\RequireAsset\Assetic\Util\Utils;

/**
 * Require Locale Manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class RequireLocaleManager implements RequireLocaleManagerInterface
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
        $this->assets = array();
        $this->mapAssets = array();
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $this->formatLocale($locale);

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
        $this->fallback = $this->formatLocale($locale);

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

        return isset($this->assets[$locale][Utils::formatName($asset)]);
    }

    /**
     * {@inheritdoc}
     */
    public function addLocaliszedAsset($asset, $locale, $localizedAsset)
    {
        $name = Utils::formatName($asset);
        $locale = $this->formatLocale($locale);
        $this->assets[$locale][$name] = (array) $localizedAsset;
        $this->mapAssets[$name][$locale] = true;
        unset($this->cache[$this->getCacheKey($locale, $name)]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeLocaliszedAsset($asset, $locale)
    {
        $name = Utils::formatName($asset);
        $locale = $this->formatLocale($locale);

        $this->cleanArray('assets', $locale, $name);
        $this->cleanArray('mapAssets', $name, $locale);
        unset($this->cache[$this->getCacheKey($locale, $name)]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizedAsset($asset, $locale = null)
    {
        $name = Utils::formatName($asset);
        $locale = $this->getCurrentLocale($locale);
        $cacheKey = $this->getCacheKey($locale, $name);

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $this->cache[$cacheKey] = $this->findLocalizedAsset($locale, $name);

        return $this->cache[$cacheKey];
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
            $name = Utils::formatName($asset);

            return isset($this->mapAssets[$name])
                ? array_keys($this->mapAssets[$name])
                : array();
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

        return array();
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
            ? $this->formatLocale($locale)
            : $this->getLocale();
    }

    /**
     * Format the locale.
     *
     * @param string|null $locale
     *
     * @return string
     */
    protected function formatLocale($locale)
    {
        if (is_string($locale)) {
            $locale = strtolower($locale);
            $locale = str_replace('-', '_', $locale);
        }

        return $locale;
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
