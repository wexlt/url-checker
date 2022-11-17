<?php

namespace App\Entity;

use App\Repository\LinkLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkLogRepository::class)]
class LinkLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $log = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetime_created = null;

    #[ORM\ManyToOne(inversedBy: 'linkLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Link $link = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $response_code = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $redirects_count = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $keywords_found = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $has_error = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLog(): ?string
    {
        return $this->log;
    }

    public function setLog(string $log): self
    {
        $this->log = $log;

        return $this;
    }

    public function getDatetimeCreated(): ?\DateTimeInterface
    {
        return $this->datetime_created;
    }

    public function setDatetimeCreated(\DateTimeInterface $datetime_created): self
    {
        $this->datetime_created = $datetime_created;

        return $this;
    }

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function setLink(?Link $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getResponseCode(): ?int
    {
        return $this->response_code;
    }

    public function setResponseCode(?int $response_code): self
    {
        $this->response_code = $response_code;

        return $this;
    }

    public function getRedirectsCount(): ?int
    {
        return $this->redirects_count;
    }

    public function setRedirectsCount(int $redirects_count): self
    {
        $this->redirects_count = $redirects_count;

        return $this;
    }

    public function getKeywordsFound(): ?string
    {
        return $this->keywords_found;
    }

    public function setKeywordsFound(?string $keywords_found): self
    {
        $this->keywords_found = $keywords_found;

        return $this;
    }

    public function getHasError(): ?int
    {
        return $this->has_error;
    }

    public function setHasError(int $has_error): self
    {
        $this->has_error = $has_error;

        return $this;
    }
}
