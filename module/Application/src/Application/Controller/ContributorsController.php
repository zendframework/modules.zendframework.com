<?php

namespace Application\Controller;

use Application\Service\RepositoryRetriever;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContributorsController extends AbstractActionController
{
    const LIST_LIMIT = 36;

    /**
     * @var RepositoryRetriever
     */
    private $repositoryRetriever;

    /**
     * @var array
     */
    private $repositoryData;

    /**
     * @param RepositoryRetriever $repositoryRetriever
     * @param array $repositoryData
     */
    public function __construct(RepositoryRetriever $repositoryRetriever, array $repositoryData)
    {
        $this->repositoryRetriever = $repositoryRetriever;
        $this->repositoryData = $repositoryData;
    }

    public function indexAction()
    {
        $contributors = $this->repositoryRetriever->getContributors(
            $this->repositoryData['owner'],
            $this->repositoryData['name'],
            self::LIST_LIMIT
        );

        shuffle($contributors);

        $metadata = $this->repositoryRetriever->getUserRepositoryMetadata(
            $this->repositoryData['owner'],
            $this->repositoryData['name']
        );

        return new ViewModel([
            'contributors' => $contributors,
            'metadata' => $metadata,
        ]);
    }
}
