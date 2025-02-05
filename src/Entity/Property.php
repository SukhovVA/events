<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Property\Grade;
use App\Entity\Property\StudyLevel;
use App\Entity\Property\Subject;
use App\Entity\Property\Umk;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'property')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'grade'        => Grade::class,
    'subject'      => Subject::class,
    'umk'          => Umk::class,
    'subjectLevel' => StudyLevel::class,
])]
abstract class Property
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(type: Types::GUID, length: 36)]
    protected string $uuid;

    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
