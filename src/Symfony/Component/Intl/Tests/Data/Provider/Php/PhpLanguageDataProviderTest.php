<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl\Tests\Data\Provider\Php;

use Symfony\Component\Intl\Data\Bundle\Reader\BundleReaderInterface;
use Symfony\Component\Intl\Data\Bundle\Reader\PhpBundleReader;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Intl\Tests\Data\Provider\AbstractLanguageDataProviderTest;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @group intl-data
 */
class PhpLanguageDataProviderTest extends AbstractLanguageDataProviderTest
{
    protected function getBundleFormat()
    {
        return Intl::PHP;
    }

    /**
     * @return BundleReaderInterface
     */
    protected function createBundleReader()
    {
        return new PhpBundleReader();
    }
}
