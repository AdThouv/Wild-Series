<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProgramRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ProgramRepository::class)
 * @Vich\Uploadable
 */
class Program
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ne me laisse pas tout vide")
     * @Assert\Length(max="255", maxMessage="La catégorie saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $summary;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $poster;

    //On va créer un nouvel attribut à notre entité, qui ne sera pas lié à une colonne
    // Tu peux d’ailleurs voir que l’annotation ORM column n’est pas spécifiée car
    //On ne rajoute pas de données de type file en bdd
    /**
     * @Vich\UploadableField(mapping="poster_file", fileNameProperty="poster")
     * @var File
     */
    private $posterFile;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class , inversedBy="programs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Season::class, mappedBy="program_id", orphanRemoval=true)
     */
    private $seasons;

    /**
     * @ORM\ManyToMany(targetEntity=Actor::class, mappedBy="programs")
     */
    private $actors;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Episode::class, mappedBy="program", orphanRemoval=true)
     */
    private $episodes;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="programs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var Datetime
     */
    private $updatedAt;
    



    public function __construct()
    {
        $this->seasons = new ArrayCollection();
        $this->actors = new ArrayCollection();
        $this->episodes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Season[]
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setProgramId($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getProgramId() === $this) {
                $season->setProgramId(null);
            }
        }

        return $this;
    }
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'title',
            'errorPath' => 'title',
            'message' => 'ce titre existe déjà',
        ]));
            $metadata->addPropertyConstraint('summary', new Assert\Regex([
            'pattern' => '/plus belle la vie/',
            'match' => false,
            'message' => 'On parle de vrais séries ici',
        ]));
    }

    /**
     * @return Collection|Actor[]
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(Actor $actor): self
    {
        if (!$this->actors->contains($actor)) {
            $this->actors[] = $actor;
            $actor->addProgram($this);
        }

        return $this;
    }

    public function removeActor(Actor $actor): self
    {
        if ($this->actors->removeElement($actor)) {
            $actor->removeProgram($this);
        }

        return $this;
    }
    public function setActor(Actor $actor): self
    {
        $this->actor = $actor;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Episode[]
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): self
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes[] = $episode;
            $episode->setProgram($this);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): self
    {
        if ($this->episodes->removeElement($episode)) {
            // set the owning side to null (unless already changed)
            if ($episode->getProgram() === $this) {
                $episode->setProgram(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
    public function setPosterFile(File $posterFile = null): Program
    {
        $this->posterFile = $posterFile;
        if ($posterFile) {
            $this->updatedAt = new DateTime('now');
        }
        return $this;
    }

    public function getPosterFile(): ?File
    {
        return $this->posterFile;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

}
