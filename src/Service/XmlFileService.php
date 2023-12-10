<?php
/**
 * Created by PhpStorm.
 * User: izzy
 * Date: 14.1.19.
 * Time: 22.29
 */

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class XmlFileService
{
    private $kernel;
    private $fileSystem;

    public function __construct(
        KernelInterface $kernel,
        Filesystem $fileSystem
    ) {
        $this->kernel = $kernel;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param $data
     *
     * @return string
     *
     * @throws \Exception
     */
    public function saveXmlDataToFile($data)
    {
        $projectDir = $this->kernel->getProjectDir();

        $uniqueDirName = uniqid('', true);
        $workingDir = $projectDir.'/var/xml-data/'.$uniqueDirName;
        $saveFileLocation = $workingDir.'/xmldata.xml';

        $this->fileSystem->dumpFile($saveFileLocation, $data);

        return $workingDir;
    }

    /**
     * @param string $url
     *
     * @return string
     *
     * @throws \Exception
     */
    public function removeXmlFile(string $url)
    {
        $this->fileSystem->remove($url);
    }
}
