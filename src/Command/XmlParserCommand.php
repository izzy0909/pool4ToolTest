<?php

namespace App\Command;

use App\Entity\CronJobs;
use App\Repository\CronJobsRepository;
use App\Service\XmlParserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class XmlParserCommand
 */
class XmlParserCommand extends Command
{
    private $cronJobsRepository;
    private $xmlParserService;

    /**
     * XmlParserCommand constructor.
     *
     * @param string|null        $name
     * @param CronJobsRepository $cronJobsRepository
     * @param XmlParserService   $xmlParserService
     */
    public function __construct(
        ?string $name = null,
        CronJobsRepository $cronJobsRepository,
        XmlParserService $xmlParserService
    ) {
        parent::__construct($name);
        $this->cronJobsRepository = $cronJobsRepository;
        $this->xmlParserService = $xmlParserService;
    }

    /**
     * Configuring console command, required inputs and help info.
     */
    protected function configure()
    {
        $this->setName('parse:xml')
            ->setDescription('Parse XML');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $cronJob = $this->cronJobsRepository->findOneCronJobToRun();

            if (empty($cronJob)) {
                $output->writeln('No Cron Jobs to run');

                return;
            }
            $output->writeln('Parsing XML started');

            $cronJob->setStartedAt(new \DateTime());
            $cronJob->setStatus(CronJobs::STATUS_PROCESSING);
            $this->cronJobsRepository->save($cronJob);

            $data = $this->xmlParserService->parseXML($cronJob->getDirectoryPath());

            $cronJob->setStatus(CronJobs::STATUS_FINISHED);
            $cronJob->setFinishedAt(new \DateTime());
            $cronJob->setData(json_encode($data));
            $this->cronJobsRepository->save($cronJob);

            $output->writeln('Parsing Finished');

        } catch (\Exception $exception) {
            $cronJob->setStatus(CronJobs::STATUS_FAILED);
            $cronJob->setData(json_encode(['error' => $exception->getMessage()]));

            $this->cronJobsRepository->save($cronJob);

            $output->writeln($exception->getMessage());
        }
    }
}
