<?php
/**
 * Created by PhpStorm.
 * User: izzy
 * Date: 14.1.19.
 * Time: 22.23
 */

namespace App\Controller;

use App\Service\CronJobsService;
use App\Service\XmlFileService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController
{
    private $cronJobsService;
    private $xmlFileService;

    /**
     * UserController constructor.
     *
     * @param CronJobsService $cronJobsService
     * @param XmlFileService  $xmlFileService
     */
    public function __construct(
        CronJobsService $cronJobsService,
        XmlFileService $xmlFileService
    ) {
        $this->cronJobsService = $cronJobsService;
        $this->xmlFileService  = $xmlFileService;
    }

    /**
     * @Route("/api/cronJob/add", name="add_cron_job", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveXmlDataToFile(Request $request)
    {
        try {
            $xmlFileLocation = $this->xmlFileService->saveXmlDataToFile($request->getContent());
            $cronJobId = $this->cronJobsService->addNewCronJob($xmlFileLocation);

            return new JsonResponse(
                [
                    'cronJobId' => $cronJobId,
                ],
                JsonResponse::HTTP_OK
            );
        } catch (NotFoundHttpException $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @Route("/api/cronJob/get/status/{id}", name="get_cron_job_status", methods={"GET"})
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getCronJobStatus(int $id)
    {
        try {
            $cronJobStatus = $this->cronJobsService->getCronJobStatusById($id);

            return new JsonResponse(
                [
                    'status' => $cronJobStatus
                ],
                JsonResponse::HTTP_OK
            );
        } catch (NotFoundHttpException $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/cronJob/get/segment/found/{id}", name="get_cron_job_segment_found", methods={"GET"})
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getCronJobSegmentFound(int $id)
    {
        try {
            $segmentFound = $this->cronJobsService->getCronJobSegmentFound($id, 0);

            return new JsonResponse(
                [
                    'segmentFound' => $segmentFound
                ],
                JsonResponse::HTTP_OK
            );
        } catch (NotFoundHttpException $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/cronJob/get/segments/found/{id}", name="get_cron_job_multiple_segments_found", methods={"GET"})
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getCronJobMultipleSegmentsFound(int $id)
    {
        try {
            $segmentFound = $this->cronJobsService->getCronJobSegmentFound($id, 1);

            return new JsonResponse(
                [
                    'segmentsFound' => $segmentFound
                ],
                JsonResponse::HTTP_OK
            );
        } catch (NotFoundHttpException $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/cronJob/get/segment/value/{id}", name="get_cron_job_segment_value", methods={"GET"})
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getCronJobSegmentValue(int $id)
    {
        try {
            $segmentData = $this->cronJobsService->getCronJobSegmentDataById($id);

            return new JsonResponse(
                [
                    $segmentData
                ],
                JsonResponse::HTTP_OK
            );
        } catch (NotFoundHttpException $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/cronJob/get/segments/value/{id}", name="get_cron_job_multiple_segments_value", methods={"GET"})
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getCronJobMultipleSegmentsValue(int $id)
    {
        try {
            $segmentsData = $this->cronJobsService->getCronJobSegmentsDataById($id);

            return new JsonResponse(
                [
                    'segmentsData' => $segmentsData
                ],
                JsonResponse::HTTP_OK
            );
        } catch (NotFoundHttpException $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/cronJob/delete/{id}", name="delete_cron_job", methods={"GET"})
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function deleteCronJob(int $id)
    {
        try {
            $urlLocation = $this->cronJobsService->deleteCronJobById($id);
            $this->xmlFileService->removeXmlFile($urlLocation);

            return new JsonResponse(
                [
                    'deleted' => true,
                ],
                JsonResponse::HTTP_OK
            );
        } catch (NotFoundHttpException $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
