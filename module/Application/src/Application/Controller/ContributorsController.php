<?php

namespace Application\Controller;

use Application\Entity;
use Application\Service\RepositoryRetriever;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContributorsController extends AbstractActionController
{
    /**
     * @var RepositoryRetriever
     */
    private $repositoryRetriever;

    /**
     * @var Entity\Repository
     */
    private $repository;

    /**
     * @param RepositoryRetriever $repositoryRetriever
     * @param Entity\Repository $repository
     */
    public function __construct(RepositoryRetriever $repositoryRetriever, Entity\Repository $repository)
    {
        $this->repositoryRetriever = $repositoryRetriever;
        $this->repository = $repository;
    }

    public function indexAction()
    {
        $contributors = $this->repositoryRetriever->getContributors(
            $this->repository->owner(),
            $this->repository->name()
        );

        shuffle($contributors);

        $metadata = $this->repositoryRetriever->getUserRepositoryMetadata(
            $this->repository->owner(),
            $this->repository->name()
        );

        return new ViewModel([
            'contributors' => $contributors,
            'metadata' => $metadata,
        ]);
    }
}
