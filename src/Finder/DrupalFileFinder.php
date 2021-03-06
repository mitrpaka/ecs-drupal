<?php

namespace Wunderio\CodingStandards\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class DrupalFileFinder implements CustomSourceProviderInterface {

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * DrupalFileFinder constructor.
     */
    public function __construct() {
        $this->finderSanitizer = new FinderSanitizer();
    }

    /**
     * @param array $source
     * @return iterable|Finder|\SplFileInfo[]|string[]|\Symfony\Component\Finder\Finder
     */
    public function find(array $source) {
        $files = [];
        foreach ($source as $singleSource) {
            if (is_file($singleSource)) {
                $files = $this->processFile($files, $singleSource);
            }
            else {
                $newFiles = Finder::create()->files()
                    ->name('*.php')
                    ->name('*.module')
                    ->name('*.install')
                    ->name('*.inc')
                    ->in($singleSource)
                    ->exclude('vendor')
                    ->sortByName();

                $files = array_merge($files, $this->finderSanitizer->sanitize($newFiles));
            }
        }
        return $files;
    }

    /**
     * @param array $files
     * @param string $file
     * @return array
     * @throws \Symplify\PackageBuilder\Exception\FileSystem\FileNotFoundException
     */
    private function processFile(array $files, $file)
    {
        return array_merge($files, [new SmartFileInfo($file)]);
    }
}
