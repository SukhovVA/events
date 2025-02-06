<?php

namespace App\Entity;

use App\Enum\PropertyType;
use App\Repository\EventRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Event
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $startsAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $endsAt = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $academicHours = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $remoteLink = null;

    #[Gedmo\Slug(fields: ['name'], updatable: false)]
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private ?bool $active = true;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private ?MediaLink $cover = null;

    /**
     * @var Collection<int, Visit>
     */
    #[ORM\OneToMany(targetEntity: Visit::class, mappedBy: 'event', orphanRemoval: true)]
    private Collection $visits;

    /**
     * @var Collection<int, Property>
     */
    #[ORM\ManyToMany(targetEntity: Property::class)]
    private Collection $properties;

    public function __construct()
    {
        $this->visits = new ArrayCollection();
        $this->properties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartsAt(): ?DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(DateTimeImmutable $startsAt): static
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(DateTimeImmutable $endsAt): static
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    public function getAcademicHours(): ?float
    {
        return $this->academicHours;
    }

    public function setAcademicHours(?float $academicHours): static
    {
        $this->academicHours = $academicHours;

        return $this;
    }

    public function getRemoteLink(): ?string
    {
        return $this->remoteLink;
    }

    public function setRemoteLink(?string $remote_link): static
    {
        $this->remoteLink = $remote_link;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getCover(): ?MediaLink
    {
        return $this->cover;
    }

    public function setCover(?MediaLink $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @return Collection<int, Visit>
     */
    public function getVisits(): Collection
    {
        return $this->visits;
    }

    public function addVisit(Visit $visit): static
    {
        if (!$this->visits->contains($visit)) {
            $this->visits->add($visit);
            $visit->setEvent($this);
        }

        return $this;
    }

    public function removeVisit(Visit $visit): static
    {
        if ($this->visits->removeElement($visit)) {
            // set the owning side to null (unless already changed)
            if ($visit->getEvent() === $this) {
                $visit->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Property>
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function addProperty(Property $property): static
    {
        if (!$this->properties->contains($property)) {
            $this->properties->add($property);
        }

        return $this;
    }

    public function removeProperty(Property $property): static
    {
        $this->properties->removeElement($property);

        return $this;
    }

    public function getProp(PropertyType $type): Collection
    {
        return $this->properties->filter(function (Property $property) use ($type) {
            $class = $type->getClassName();
            return $property instanceof $class;
        });
    }

    public function getPropsNames(PropertyType $type): array
    {
        return $this->getProp($type)->map(fn(Property $property) => $property->getName())->getValues();
    }
}
