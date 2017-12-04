<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Table(name="tasks")
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    const STATUS_UNDONE = 1;
    const STATUS_DONE = 2;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var TodoList
     * @ORM\ManyToOne(targetEntity="App\Entity\TodoList", inversedBy="tasks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="list_id", referencedColumnName="id", onDelete="cascade")
     * })
     */
    private $todoList;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $text;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", options={"default":1})
     */
    private $status = self::STATUS_UNDONE;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return TodoList
     */
    public function getTodoList()
    {
        return $this->todoList;
    }

    /**
     * @param TodoList $todoList
     */
    public function setTodoList($todoList)
    {
        $this->todoList = $todoList;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Task
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Task
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}

