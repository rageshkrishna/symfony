<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl\Data\Util;

/**
 * Scans a directory with text data files for locales.
 *
 * The name of each *.txt file (without suffix) in the given source directory
 * is considered a locale.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @internal
 */
class LocaleScanner
{
    /**
     * Returns all locales found in the given directory.
     *
     * @param string $sourceDir The directory with ICU files
     * @param string $extension The file extension
     *
     * @return array An array of locales. The result also contains locales that
     *               are in fact just aliases for other locales. Use
     *               {@link scanAliases()} to determine which of the locales
     *               are aliases
     */
    public function scanLocales($sourceDir, $extension = '.res')
    {
        $locales = glob($sourceDir.'/*'.$extension);

        // Remove file extension and sort
        array_walk($locales, function (&$locale) use ($extension) { $locale = basename($locale, $extension); });

        // Remove non-locales
        $locales = array_filter($locales, function ($locale) {
            return preg_match('/^[a-z]{2}(_.+)?$/', $locale);
        });

        sort($locales);

        return $locales;
    }

    /**
     * Returns all locale aliases found in the given directory.
     *
     * @param string $sourceDir The directory with ICU files
     * @param string $extension The file extension
     *
     * @return array An array with the locale aliases as keys and the aliased
     *               locales as values
     */
    public function scanAliases($sourceDir, $extension = '.res')
    {
        $locales = $this->scanLocales($sourceDir, $extension);
        $aliases = array();

        // Delete locales that are no aliases
        foreach ($locales as $locale) {
            $content = file_get_contents($sourceDir.'/'.$locale.$extension);

            // Aliases contain the text "%%ALIAS" followed by the aliased locale
            if (preg_match('/"%%ALIAS"\{"([^"]+)"\}/', $content, $matches)) {
                $aliases[$locale] = $matches[1];
            }
        }

        return $aliases;
    }

}
