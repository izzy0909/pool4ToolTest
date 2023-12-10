<?php
/**
 * Created by PhpStorm.
 * User: izzy
 * Date: 14.1.19.
 * Time: 22.29
 */

namespace App\Service;

use App\Entity\CronJobs;
use App\Repository\CronJobsRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CronJobsService
{
    private $cronJobsRepository;
    private $validator;

    public function __construct(
        CronJobsRepository $cronJobsRepository,
        ValidatorInterface $validator
    ) {
        $this->cronJobsRepository = $cronJobsRepository;
        $this->validator = $validator;
    }

    /**
     * @param $url
     *
     * @return int|null
     *
     * @throws \Exception
     */
    public function addNewCronJob($url)
    {
        $cronJob = new CronJobs();
        $cronJob->setName('Parse XML');
        $cronJob->setStatus(CronJobs::STATUS_PENDING);
        $cronJob->setDirectoryPath($url);

        $this->cronJobsRepository->save($cronJob);

        return $cronJob->getId();
    }

    /**
     * @param $id
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getCronJobStatusById($id)
    {
        $this->validateInt($id);

        $cronJob = $this->cronJobsRepository->find($id);

        $this->checkIfCronJobIsFound($cronJob);

        return CronJobs::STATUS_MAP[$cronJob->getStatus()];
    }

    /**
     * @param $id
     * @param $segmentNumber
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getCronJobSegmentFound($id, $segmentNumber)
    {
        $this->validateInt($id);

        $cronJob = $this->cronJobsRepository->find($id);

        $this->checkIfCronJobIsFound($cronJob);

        return $this->checkIfSegmentFound($cronJob, $segmentNumber);
    }

    /**
     * @param $id
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getCronJobSegmentDataById($id)
    {
        $this->validateInt($id);

        $cronJob = $this->cronJobsRepository->find($id);

        $this->checkIfCronJobIsFound($cronJob);

        return $this->formatSegmentData($cronJob);
    }

    /**
     * @param $id
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getCronJobSegmentsDataById($id)
    {
        $this->validateInt($id);

        $cronJob = $this->cronJobsRepository->find($id);

        $this->checkIfCronJobIsFound($cronJob);

        return $this->formatSegmentsData($cronJob);
    }

    /**
     * @param $id
     *
     * @return string
     *
     * @throws \Exception
     */
    public function deleteCronJobById($id)
    {
        $this->validateInt($id);

        $cronJob = $this->cronJobsRepository->find($id);

        $this->checkIfCronJobIsFound($cronJob);

        $urlLocation = $cronJob->getDirectoryPath();

        $this->cronJobsRepository->remove($cronJob);

        return $urlLocation;
    }

    /**
     * @param $id
     *
     * @throws \Exception
     */
    private function validateInt($id)
    {
        $intConstraint = new Assert\Type(['type' => 'integer']);

        $errors = $this->validator->validate(
            $id,
            $intConstraint
        );

        if ($errors->count() > 0) {
            throw new \Exception($errors[0]->getMessage());
        }
    }

    /**
     * @param CronJobs|null $cronJob
     * @param int           $segmentNumber
     *
     * @return bool
     */
    private function checkIfSegmentFound(?CronJobs $cronJob, $segmentNumber)
    {
        $segmentFound = false;
        $segmentData = json_decode($cronJob->getData());

        if (CronJobs::STATUS_FINISHED === $cronJob->getStatus()) {
            $segmentFound = (isset($segmentData->segmentCount) && $segmentData->segmentCount > $segmentNumber) ? true : false;
        }

        return $segmentFound;
    }

    /**
     * @param CronJobs|null $cronJob
     *
     * @return array|mixed|string
     *
     * @throws \Exception
     */
    private function formatSegmentData(?CronJobs $cronJob)
    {
        $segmentData = json_decode($cronJob->getData());

        $this->exceptionForUnfinishedCronJob($cronJob);

        if (CronJobs::STATUS_FAILED === $cronJob->getStatus()) {
            return (isset($segmentData->error)) ? $segmentData->error : '';
        }
        if (CronJobs::STATUS_FINISHED === $cronJob->getStatus()) {
            if (isset($segmentData->segmentCount)) {
                if ($segmentData->segmentCount === 1) {
                    return $segmentData->segmentData;
                }
                if ($segmentData->segmentCount === 0) {
                    throw new \Exception('We haven\'t found any data for this segment :(');
                }
                throw new \Exception('There are multiple segments found, please query the right API endpoint ;)');
            }
        }
    }

    /**
     * @param CronJobs|null $cronJob
     *
     * @return array|mixed|string
     *
     * @throws \Exception
     */
    private function formatSegmentsData(?CronJobs $cronJob)
    {
        $segmentData = json_decode($cronJob->getData());

        $this->exceptionForUnfinishedCronJob($cronJob);

        if (CronJobs::STATUS_FAILED === $cronJob->getStatus()) {
            return (isset($segmentData->error)) ? $segmentData->error : '';
        }
        if (CronJobs::STATUS_FINISHED === $cronJob->getStatus()) {
            if (isset($segmentData->segmentCount)) {
                if ($segmentData->segmentCount > 1) {
                    return $segmentData->segmentData;
                }
                if ($segmentData->segmentCount === 0) {
                    throw new \Exception('We haven\'t found any data for this segment :(');
                }
                throw new \Exception('There is single segment found, please query the right API endpoint ;)');
            }
        }
    }

    /**
     * @param CronJobs|null $cronJob
     *
     * @throws \Exception
     */
    private function exceptionForUnfinishedCronJob(?CronJobs $cronJob): void
    {
        if (in_array($cronJob->getStatus(), [CronJobs::STATUS_PROCESSING, CronJobs::STATUS_PENDING])) {
            throw new \Exception('Your data isn\'t ready yet, but it will be ready soon :)');
        }
    }

    /**
     * @param CronJobs|null $cronJob
     */
    private function checkIfCronJobIsFound(?CronJobs $cronJob): void
    {
        if (empty($cronJob)) {
            throw new NotFoundHttpException('Cron Job not found.');
        }
    }
}
