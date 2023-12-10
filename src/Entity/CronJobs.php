<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CronJobsRepository")
 */
class CronJobs
{
    const STATUS_FINISHED = 1;
    const STATUS_PENDING = 2;
    const STATUS_PROCESSING = 3;
    const STATUS_FAILED = 4;

    const STATUS_MAP = [
        self::STATUS_FINISHED   => 'Done',
        self::STATUS_PENDING    => 'Pending',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_FAILED     => 'Error',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finishedAt;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $directoryPath;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    /**
     * @param \DateTimeInterface|null $startedAt
     */
    public function setStartedAt(?\DateTimeInterface $startedAt)
    {
        $this->startedAt = $startedAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }

    /**
     * @param \DateTimeInterface|null $finishedAt
     */
    public function setFinishedAt(?\DateTimeInterface $finishedAt)
    {
        $this->finishedAt = $finishedAt;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getDirectoryPath(): ?string
    {
        return $this->directoryPath;
    }

    /**
     * @param string $directoryPath
     */
    public function setDirectoryPath(string $directoryPath)
    {
        $this->directoryPath = $directoryPath;
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @param string|null $data
     */
    public function setData(string $data)
    {
        $this->data = $data;
    }
}
