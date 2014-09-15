<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl\Composer;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Intl\Exception\RuntimeException;
use Symfony\Component\Intl\Intl;

/**
 * Decompresses the resource data.
 *
 * The method {@link decompressData()} should be called after installing the
 * component.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ScriptHandler
{
    /**
     * Decompresses the ICU data.
     */
    public static function decompressData()
    {
        self::decompressDataForFormat(Intl::JSON);
        self::decompressDataForFormat(Intl::RB_V2);
    }

    /**
     * Decompresses the ICU data in a given format.
     *
     * @param string $format
     *
     * @throws RuntimeException
     */
    private static function decompressDataForFormat($format)
    {
        $filesystem = new Filesystem();
        $archive = Intl::getDataDirectory().'/'.$format.'.zip';
        $targetDir = Intl::getDataDirectory().'/'.$format;

        if (!file_exists($archive)) {
            throw new RuntimeException(sprintf(
                'The zip file "%s" could not be found.',
                $archive
            ));
        }

        if (file_exists($targetDir)) {
            $filesystem->remove($targetDir);
            $filesystem->mkdir($targetDir);
        }

        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive();

            if (true !== ($status = $zip->open($archive))) {
                throw new RuntimeException(self::getReadableZipArchiveStatus($status));
            }

            if (!$zip->extractTo($targetDir)) {
                throw new RuntimeException(sprintf(
                    'The extraction of the file "%s" failed.',
                    $archive
                ));
            }

            return;
        }

        // Test whether "unzip" exists on the shell
        exec('unzip -h', $output, $status);

        if (0 === $status) {
            $command = sprintf('unzip -d %s %s', escapeshellarg($targetDir), escapeshellarg($archive));

            exec($command, $output, $status);

            if (0 !== $status) {
                throw new RuntimeException(sprintf(
                    'The extraction of the file "%s" failed. Output:%s',
                    $archive,
                    "\n".implode("\n", $output)
                ));
            }

            return;
        }

        throw new RuntimeException(sprintf(
            'Could not find a mechanism to decompress the archive "%s".',
            $archive
        ));
    }

    /**
     * Returns a readable version of the given {@link \ZipArchive} status.
     *
     * @param int $status The status code
     *
     * @return string The status message
     *
     * @see http://de2.php.net/manual/en/class.ziparchive.php#108601
     */
    private static function getReadableZipArchiveStatus($status)
    {
        switch ((int) $status) {
            case \ZipArchive::ER_OK : return 'No error';
            case \ZipArchive::ER_MULTIDISK : return 'Multi-disk zip archives not supported';
            case \ZipArchive::ER_RENAME : return 'Renaming temporary file failed';
            case \ZipArchive::ER_CLOSE : return 'Closing zip archive failed';
            case \ZipArchive::ER_SEEK : return 'Seek error';
            case \ZipArchive::ER_READ : return 'Read error';
            case \ZipArchive::ER_WRITE : return 'Write error';
            case \ZipArchive::ER_CRC : return 'CRC error';
            case \ZipArchive::ER_ZIPCLOSED : return 'Containing zip archive was closed';
            case \ZipArchive::ER_NOENT : return 'No such file';
            case \ZipArchive::ER_EXISTS : return 'File already exists';
            case \ZipArchive::ER_OPEN : return 'Can\'t open file';
            case \ZipArchive::ER_TMPOPEN : return 'Failure to create temporary file';
            case \ZipArchive::ER_ZLIB : return 'Zlib error';
            case \ZipArchive::ER_MEMORY : return 'Malloc failure';
            case \ZipArchive::ER_CHANGED : return 'Entry has been changed';
            case \ZipArchive::ER_COMPNOTSUPP : return 'Compression method not supported';
            case \ZipArchive::ER_EOF : return 'Premature EOF';
            case \ZipArchive::ER_INVAL : return 'Invalid argument';
            case \ZipArchive::ER_NOZIP : return 'Not a zip archive';
            case \ZipArchive::ER_INTERNAL : return 'Internal error';
            case \ZipArchive::ER_INCONS : return 'Zip archive inconsistent';
            case \ZipArchive::ER_REMOVE : return 'Can\'t remove file';
            case \ZipArchive::ER_DELETED : return 'Entry has been deleted';

            default: return sprintf('Unknown status %s', $status );
        }
    }

    /**
     * Should not be instantiated.
     */
    private function __construct()
    {
    }
}
