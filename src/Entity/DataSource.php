<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DataSourceRepository")
 */
class DataSource
{
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero
     */
    private $ttl;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastReadAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $dataSource;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DataFlow", mappedBy="dataSource")
     */
    private $dataFlows;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotBlank
     *
     * @var array
     */
    private $dataSourceOptions;

    public function __construct()
    {
        $this->dataFlows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDataSource(): ?string
    {
        return $this->dataSource;
    }

    public function setDataSource(string $dataSource): self
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function getDataSourceOptions(): ?array
    {
        return $this->dataSourceOptions;
    }

    public function setDataSourceOptions(array $dataSourceOptions): self
    {
        $this->dataSourceOptions = $dataSourceOptions;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    public function setTtl(int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function getLastReadAt(): ?\DateTimeInterface
    {
        return $this->lastReadAt;
    }

    public function setLastReadAt(?\DateTimeInterface $lastReadAt): self
    {
        $this->lastReadAt = $lastReadAt;

        return $this;
    }

    /**
     * @return Collection|DataFlow[]
     */
    public function getDataFlows(): Collection
    {
        return $this->dataFlows;
    }

    public function addDataFlow(DataFlow $dataFlow): self
    {
        if (!$this->dataFlows->contains($dataFlow)) {
            $this->dataFlows[] = $dataFlow;
            $dataFlow->setDataSource($this);
        }

        return $this;
    }

    public function removeDataFlow(DataFlow $dataFlow): self
    {
        if ($this->dataFlows->contains($dataFlow)) {
            $this->dataFlows->removeElement($dataFlow);
            // set the owning side to null (unless already changed)
            if ($dataFlow->getDataSource() === $this) {
                $dataFlow->setDataSource(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name ?? static::class;
    }
}
