<?php

declare(strict_types=1);

namespace Diw\Pannellum\Form\FlexForm;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Provides items for FlexForm select fields to choose a Scene identifier.
 */
class SceneItemsProvider
{
    /**
     * ItemsProcFunc for FlexForm select: adds all scene identifiers (sorted alphabetically) as options.
     * Label format: "{title} [{identifier}]" (falls back to identifier if title is empty)
     *
     * @param array $config by reference; expects key 'items'
     */
    public function getSceneIdentifierItems(array &$config): void
    {
        $items = $config['items'] ?? [];

        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $qb = $connectionPool->getQueryBuilderForTable('tx_pannellum_scene');

        // Select all non-deleted scenes, order by identifier alphabetically
        $rows = $qb
            ->select('identifier', 'title')
            ->from('tx_pannellum_scene')
            ->where(
                $qb->expr()->and(
                    $qb->expr()->eq('deleted', $qb->createNamedParameter(0, \PDO::PARAM_INT)),
                    $qb->expr()->eq('hidden', $qb->createNamedParameter(0, \PDO::PARAM_INT))
                )
            )
            ->orderBy('identifier', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($rows as $row) {
            $identifier = trim((string)($row['identifier'] ?? ''));
            if ($identifier === '') {
                continue;
            }
            $title = trim((string)($row['title'] ?? ''));
            $label = $title !== '' ? sprintf('%s [%s]', $title, $identifier) : $identifier;
            $items[] = [$label, $identifier];
        }

        $config['items'] = $items;
    }
}
