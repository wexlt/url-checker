<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
class Link
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $url = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_check = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $keywords = null;

    #[ORM\ManyToOne(inversedBy: 'link')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'link', targetEntity: LinkLog::class, orphanRemoval: true)]
    private Collection $linkLogs;

    public function __construct()
    {
        $this->linkLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getLastCheck(): ?\DateTimeInterface
    {
        return $this->last_check;
    }

    public function setLastCheck(?\DateTimeInterface $last_check): self
    {
        $this->last_check = $last_check;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, LinkLog>
     */
    public function getLinkLogs(): Collection
    {
        return $this->linkLogs;
    }

    public function addLinkLog(LinkLog $linkLog): self
    {
        if (!$this->linkLogs->contains($linkLog)) {
            $this->linkLogs->add($linkLog);
            $linkLog->setLink($this);
        }

        return $this;
    }

    public function removeLinkLog(LinkLog $linkLog): self
    {
        if ($this->linkLogs->removeElement($linkLog)) {
            // set the owning side to null (unless already changed)
            if ($linkLog->getLink() === $this) {
                $linkLog->setLink(null);
            }
        }

        return $this;
    }
}
