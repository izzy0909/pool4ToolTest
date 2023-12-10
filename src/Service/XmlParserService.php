<?php
/**
 * Created by PhpStorm.
 * User: izzy
 * Date: 14.1.19.
 * Time: 22.29
 */

namespace App\Service;

use App\Repository\CronJobsRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XMLReader;

class XmlParserService
{
    private $cronJobsRepository;
    private $validator;

    const SEARCH_SEGMENT = 'ZZ_JD_MATLIEFNRNUM';

    public function __construct(
        CronJobsRepository $cronJobsRepository,
        ValidatorInterface $validator
    )
    {
        $this->cronJobsRepository = $cronJobsRepository;
        $this->validator = $validator;
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public function parseXML($url)
    {
        $xmlReader = new XMLReader();
        $xmlReader->open($url.'/xmldata.xml');

        $segmentCount = 0;
        $segmentData = [];

        while($xmlReader->read())
        {
            if (self::SEARCH_SEGMENT === $xmlReader->name) {
                if ($xmlReader->nodeType === XMLReader::ELEMENT) {
                    $segmentCount++;
                    $segmentData[] = $xmlReader->readInnerXml();
                }
            }
        }

        return [
            'segmentCount' => $segmentCount,
            'segmentData'  => $segmentData,
        ];
    }
}
