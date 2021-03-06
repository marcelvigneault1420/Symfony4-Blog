<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Title can't be empty")
     */
    private $Title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Content can't be empty")
     */
    private $Content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateCreated;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateModified;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"Title"})
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private $IsPosted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DatePosted;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="Post", orphanRemoval=true)
     */
    private $Comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", mappedBy="Post")
     */
    private $Tags;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="Posts")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     * @Assert\NotBlank(message="Please select a category")
     */
    private $Category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    public function __construct()
    {
        $this->Comments = new ArrayCollection();
        $this->Tags = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setDates(): void
    {
        $this->setDateModified(new \DateTime('now'));
        if ($this->getDateCreated() === null) {
            $this->setDateCreated(new \DateTime('now'));
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): self
    {
        $this->Title = $Title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->Content;
    }

    public function setContent(string $Content): self
    {
        $this->Content = $Content;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->DateCreated;
    }

    public function setDateCreated(\DateTimeInterface $DateCreated): self
    {
        $this->DateCreated = $DateCreated;

        return $this;
    }

    public function getDateModified(): ?\DateTimeInterface
    {
        return $this->DateModified;
    }

    public function setDateModified(\DateTimeInterface $DateModified): self
    {
        $this->DateModified = $DateModified;

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

    public function getIsPosted(): ?bool
    {
        return $this->IsPosted;
    }

    public function setIsPosted(bool $IsPosted): self
    {
        $this->IsPosted = $IsPosted;

        return $this;
    }

    public function getDatePosted(): ?\DateTimeInterface
    {
        return $this->DatePosted;
    }

    public function setDatePosted(?\DateTimeInterface $DatePosted): self
    {
        $this->DatePosted = $DatePosted;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->Comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->Comments->contains($comment)) {
            $this->Comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->Comments->contains($comment)) {
            $this->Comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->Tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->Tags->contains($tag)) {
            $this->Tags[] = $tag;
            $tag->addPost($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->Tags->contains($tag)) {
            $this->Tags->removeElement($tag);
            $tag->removePost($this);
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->Category;
    }

    public function setCategory(?Category $Category): self
    {
        $this->Category = $Category;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }
}
