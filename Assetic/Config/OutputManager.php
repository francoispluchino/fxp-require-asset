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

use Symfony\Component\Finder\Glob;

/**
 * Config output manager.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class OutputManager implements OutputManagerInterface
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var array
     */
    protected $patterns;

    /**
     * Constructor.
     *
     * @param string $prefix The assetic output prefix
     */
    public function __construct($prefix = '')
    {
        $this->prefix = trim($prefix, '/');
        $this->patterns = array();
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputPattern($pattern)
    {
        return isset($this->patterns[$pattern]);
    }

    /**
     * {@inheritdoc}
     */
    public function addOutputPattern($pattern, $outputPattern)
    {
        $this->patterns[$pattern] = $outputPattern;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOutputPatterns(array $patterns)
    {
        foreach ($patterns as $pattern => $outputPattern) {
            $this->addOutputPattern($pattern, $outputPattern);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeOutputPattern($pattern)
    {
        unset($this->patterns[$pattern]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputPatterns()
    {
        return $this->patterns;
    }

    /**
     * {@inheritdoc}
     */
    public function convertOutput($output)
    {
        foreach ($this->patterns as $pattern => $outputPattern) {
            if (preg_match(Glob::toRegex($pattern), $output)) {
                $output = $this->convertPattern($output, $pattern, $outputPattern);
            }
        }

        return $this->prefix . '/' . $output;
    }

    /**
     * Convert the output path with the new pattern path.
     *
     * @param string $output        The output path
     * @param string $pattern       The pattern
     * @param array  $outputPattern The new output pattern
     *
     * @return string The converted output path
     */
    protected function convertPattern($output, $pattern, $outputPattern)
    {
        $output = str_replace('\\', '/', $output);
        $start = 0 === strpos($pattern, '*') ? '' : '^';
        $end = (strlen($pattern) - 1) === strrpos($pattern, '*') ? '' : '$';
        $pattern = trim($pattern, '*');
        $pattern = '/' . $start . str_replace('/', '\\/', $pattern) . $end . '/';

        $output = preg_replace($pattern, trim($outputPattern, '*'), $output);

        return $this->replacePattern($output, $pattern, $outputPattern);
    }

    /**
     * Replace the pattern variables.
     *
     * @param string $output        The output path
     * @param string $pattern       The pattern
     * @param array  $outputPattern The new output pattern
     *
     * @return string
     */
    protected function replacePattern($output, $pattern, $outputPattern)
    {
        preg_match_all(str_replace('*', '(.*)', $pattern), $output, $matches, PREG_SET_ORDER);

        if (empty($matches) || !preg_match('/\$[0-9]+/', $outputPattern)) {
            return $output;
        }

        $matches = $matches[0];
        array_shift($matches);

        return $this->doReplacePattern($matches, $output, $outputPattern);
    }

    /**
     * Do replace the pattern variables.
     *
     * @param array  $matches       The matches
     * @param string $output        Theoutput path
     * @param array  $outputPattern The new output pattern
     *
     * @return string
     */
    protected function doReplacePattern(array $matches, $output, $outputPattern)
    {
        if (!empty($matches)) {
            $output = $outputPattern;

            foreach ($matches as $i => $match) {
                $output = str_replace('$' . $i, $match, $outputPattern);
            }
        }

        return $output;
    }
}
